<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Webhook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductionReadinessService
{
    public function report(): array
    {
        $groups = [
            'Runtime' => $this->runtimeChecks(),
            'Data & storage' => $this->dataChecks(),
            'Security' => $this->securityChecks(),
            'Mail & jobs' => $this->deliveryChecks(),
            'Payments' => $this->paymentChecks(),
            'Webhooks' => $this->webhookChecks(),
        ];

        $checks = collect($groups)->flatten(1);
        $score = $checks->isEmpty() ? 0 : (int) round($checks->sum(fn ($c) => match ($c['status']) {
            'ok' => 1,
            'warn' => .5,
            default => 0,
        }) / $checks->count() * 100);

        return [
            'score' => $score,
            'status' => $score >= 90 ? 'ready' : ($score >= 70 ? 'attention' : 'blocked'),
            'groups' => $groups,
            'counts' => [
                'ok' => $checks->where('status', 'ok')->count(),
                'warn' => $checks->where('status', 'warn')->count(),
                'fail' => $checks->where('status', 'fail')->count(),
            ],
        ];
    }

    private function runtimeChecks(): array
    {
        $extensions = ['openssl', 'pdo', 'mbstring', 'curl', 'fileinfo', 'zip'];
        $checks = [
            $this->check('laravel', 'Laravel', app()->version(), version_compare(app()->version(), '12.0.0', '>='), 'Laravel 12 or newer is expected.'),
            $this->check('php', 'PHP', PHP_VERSION, version_compare(PHP_VERSION, '8.2.0', '>='), 'Laravel 12 requires PHP 8.2+.'),
        ];
        foreach ($extensions as $extension) {
            $checks[] = $this->check('ext-'.$extension, 'PHP ext: '.$extension, extension_loaded($extension) ? 'Loaded' : 'Missing', extension_loaded($extension), $extension === 'zip' ? 'Required by the installed backup package.' : 'Required by common Laravel/store functionality.');
        }
        return $checks;
    }

    private function dataChecks(): array
    {
        $dbOk = false;
        $dbValue = 'Offline';
        try {
            DB::connection()->getPdo();
            $dbOk = true;
            $dbValue = (string) (DB::connection()->getDatabaseName() ?: DB::connection()->getDriverName().' connected');
        } catch (\Throwable $e) {
            $dbValue = 'Connection failed';
        }

        $cacheOk = false;
        try {
            Cache::put('trendora.readiness', 'ok', 10);
            $cacheOk = Cache::get('trendora.readiness') === 'ok';
        } catch (\Throwable $e) {}

        return [
            $this->check('db', 'Database', $dbValue, $dbOk, 'Database must be reachable.'),
            $this->check('cache', 'Cache store', (string) config('cache.default'), $cacheOk, 'Configured cache store must be writable.'),
            $this->check('storage-link', 'Public storage link', is_link(public_path('storage')) ? 'Linked' : 'Missing', is_link(public_path('storage')), 'Run php artisan storage:link once after deployment.'),
            $this->check('storage-writable', 'Storage writable', is_writable(storage_path()) ? 'Writable' : 'Not writable', is_writable(storage_path()), 'PHP must be able to write storage/.'),
            $this->check('cache-writable', 'Bootstrap cache writable', is_writable(base_path('bootstrap/cache')) ? 'Writable' : 'Not writable', is_writable(base_path('bootstrap/cache')), 'PHP must be able to write bootstrap/cache/.'),
        ];
    }

    private function securityChecks(): array
    {
        $production = app()->environment('production');
        $url = (string) config('app.url');
        $https = str_starts_with(strtolower($url), 'https://');
        $key = (string) config('app.key');
        $sessionSecure = (bool) config('session.secure');

        return [
            $this->statusCheck('environment', 'Environment', app()->environment(), $production ? 'ok' : 'warn', $production ? 'Production environment is active.' : 'Use APP_ENV=production for live deployment.'),
            $this->statusCheck('debug', 'Debug mode', config('app.debug') ? 'Enabled' : 'Disabled', config('app.debug') ? 'fail' : 'ok', 'APP_DEBUG must be false in production.'),
            $this->check('app-key', 'Application key', $key !== '' ? 'Configured' : 'Missing', $key !== '', 'APP_KEY protects encrypted application data.'),
            $this->statusCheck('https', 'Application URL', $url ?: 'Not configured', $https ? 'ok' : ($production ? 'fail' : 'warn'), 'Production APP_URL should use HTTPS.'),
            $this->statusCheck('secure-cookie', 'Secure session cookie', $sessionSecure ? 'Enabled' : 'Disabled', $sessionSecure ? 'ok' : ($production ? 'warn' : 'ok'), 'Set SESSION_SECURE_COOKIE=true when served only over HTTPS.'),
        ];
    }

    private function deliveryChecks(): array
    {
        $mailer = (string) config('mail.default');
        $mailHost = (string) config('mail.mailers.smtp.host');
        $from = (string) config('mail.from.address');
        $queue = (string) config('queue.default');
        $heartbeat = Setting::get('scheduler_heartbeat_at');
        $heartbeatAt = null;
        try { $heartbeatAt = $heartbeat ? Carbon::parse($heartbeat) : null; } catch (\Throwable $e) {}
        $schedulerOk = $heartbeatAt && $heartbeatAt->gt(now()->subMinutes(5));

        return [
            $this->statusCheck('mailer', 'Mail driver', $mailer, in_array($mailer, ['log', 'array'], true) ? 'warn' : 'ok', 'Use SMTP or another transactional mail transport in production.'),
            $this->statusCheck('smtp-host', 'SMTP host', $mailHost ?: 'Not configured', $mailHost && $mailHost !== '127.0.0.1' ? 'ok' : 'warn', 'Configure a real SMTP host before live email testing.'),
            $this->statusCheck('mail-from', 'Mail from address', $from ?: 'Missing', filter_var($from, FILTER_VALIDATE_EMAIL) && !str_contains($from, 'example.com') ? 'ok' : 'warn', 'Use a verified sender address.'),
            $this->statusCheck('queue', 'Queue connection', $queue, $queue === 'sync' ? 'warn' : 'ok', 'A persistent queue worker is recommended for production.'),
            $this->statusCheck('scheduler', 'Scheduler heartbeat', $heartbeatAt?->diffForHumans() ?? 'Never recorded', $schedulerOk ? 'ok' : 'warn', 'Run php artisan schedule:run every minute from cron.'),
        ];
    }

    private function paymentChecks(): array
    {
        $stripeEnabled = Setting::isPaymentEnabled('stripe');
        $paypalEnabled = Setting::isPaymentEnabled('paypal');
        $stripeSecret = (string) Setting::get('payment_stripe_secret', '');
        $paypalClient = (string) Setting::get('payment_paypal_client_id', '');
        $paypalSecret = (string) Setting::get('payment_paypal_secret', '');
        $raftarGateway = (string) config('raftarpay.default', 'jazzcash');
        $raftarEnvironment = (string) config('raftarpay.environment', 'sandbox');

        return [
            $this->statusCheck('stripe', 'Stripe', $stripeEnabled ? 'Enabled' : 'Disabled', !$stripeEnabled ? 'warn' : ($stripeSecret !== '' ? 'ok' : 'fail'), $stripeEnabled ? 'Run the safe connection test before launch.' : 'Enable only if Stripe is part of your checkout.'),
            $this->statusCheck('paypal', 'PayPal', $paypalEnabled ? 'Enabled' : 'Disabled', !$paypalEnabled ? 'warn' : ($paypalClient !== '' && $paypalSecret !== '' ? 'ok' : 'fail'), $paypalEnabled ? 'Run the safe connection test before launch.' : 'Enable only if PayPal is part of your checkout.'),
            $this->statusCheck('raftarpay', 'RaftarPay', $raftarGateway.' · '.$raftarEnvironment, $raftarEnvironment === 'production' ? 'ok' : 'warn', 'Keep sandbox until merchant credentials and callback URLs are verified.'),
        ];
    }

    private function webhookChecks(): array
    {
        if (!Schema::hasTable('webhooks')) {
            return [$this->statusCheck('webhooks-table', 'Webhooks', 'Table missing', 'fail', 'Run migrations before configuring webhooks.')];
        }
        $enabled = Webhook::where('status', true)->get();
        $invalid = $enabled->filter(fn ($w) => !str_starts_with(strtolower((string) $w->url), 'https://'))->count();
        return [
            $this->statusCheck('webhooks-enabled', 'Enabled endpoints', (string) $enabled->count(), $enabled->count() ? 'ok' : 'warn', 'No endpoint is fine if your store does not need outbound webhooks.'),
            $this->statusCheck('webhooks-https', 'Webhook HTTPS', $invalid ? $invalid.' invalid' : 'All enabled URLs use HTTPS', $invalid ? 'fail' : 'ok', 'Outbound webhook destinations must use public HTTPS URLs.'),
        ];
    }

    private function check(string $key, string $label, string $value, bool $ok, string $help): array
    {
        return $this->statusCheck($key, $label, $value, $ok ? 'ok' : 'fail', $help);
    }

    private function statusCheck(string $key, string $label, string $value, string $status, string $help): array
    {
        return compact('key', 'label', 'value', 'status', 'help');
    }
}
