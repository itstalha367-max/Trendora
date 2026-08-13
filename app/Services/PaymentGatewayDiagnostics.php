<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;

class PaymentGatewayDiagnostics
{
    public function test(string $gateway): array
    {
        return match ($gateway) {
            'stripe' => $this->stripe(),
            'paypal' => $this->paypal(),
            'raftarpay' => $this->raftarPay(),
            default => ['ok' => false, 'status' => 'failed', 'message' => 'Unsupported payment gateway.'],
        };
    }

    private function stripe(): array
    {
        $secret = (string) Setting::get('payment_stripe_secret', '');
        $mode = (string) Setting::get('payment_stripe_mode', 'sandbox');

        if ($secret === '') {
            return ['ok' => false, 'status' => 'failed', 'message' => 'Stripe secret key is not configured.'];
        }

        if ($mode === 'live' && !str_starts_with($secret, 'sk_live_')) {
            return ['ok' => false, 'status' => 'failed', 'message' => 'Stripe is set to live mode but the saved secret does not look like a live key.'];
        }
        if ($mode !== 'live' && !str_starts_with($secret, 'sk_test_')) {
            return ['ok' => false, 'status' => 'failed', 'message' => 'Stripe is set to sandbox mode but the saved secret does not look like a test key.'];
        }

        try {
            $client = new StripeClient($secret);
            $balance = $client->balance->retrieve([]); // Read-only API call; no charge is created.
            $currencies = collect($balance->available ?? [])->pluck('currency')->filter()->unique()->values()->all();
            return [
                'ok' => true,
                'status' => 'passed',
                'message' => 'Stripe authenticated successfully. No payment was created.',
                'context' => ['mode' => $mode, 'currencies' => $currencies],
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => 'failed', 'message' => 'Stripe connection failed: '.$this->safeMessage($e->getMessage())];
        }
    }

    private function paypal(): array
    {
        $clientId = (string) Setting::get('payment_paypal_client_id', '');
        $secret = (string) Setting::get('payment_paypal_secret', '');
        $mode = (string) Setting::get('payment_paypal_mode', 'sandbox');
        if ($clientId === '' || $secret === '') {
            return ['ok' => false, 'status' => 'failed', 'message' => 'PayPal client ID and secret are required.'];
        }

        $base = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        try {
            $response = Http::asForm()->withBasicAuth($clientId, $secret)->timeout(10)->connectTimeout(5)
                ->post($base.'/v1/oauth2/token', ['grant_type' => 'client_credentials']);
            if ($response->successful() && $response->json('access_token')) {
                return [
                    'ok' => true,
                    'status' => 'passed',
                    'message' => 'PayPal authenticated successfully. No payment was created.',
                    'context' => ['mode' => $mode, 'expires_in' => $response->json('expires_in')],
                ];
            }
            return ['ok' => false, 'status' => 'failed', 'message' => 'PayPal authentication failed (HTTP '.$response->status().').'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => 'failed', 'message' => 'PayPal connection failed: '.$this->safeMessage($e->getMessage())];
        }
    }

    private function raftarPay(): array
    {
        $gateway = (string) config('raftarpay.default', 'jazzcash');
        $environment = (string) config('raftarpay.environment', 'sandbox');
        $required = match ($gateway) {
            'jazzcash' => ['merchant_id', 'password', 'integrity_salt'],
            'easypaisa' => ['store_id', 'hash_key'],
            'kuickpay' => ['merchant_id', 'auth_key', 'hash_key'],
            'faysal', 'meezan' => ['merchant_id', 'merchant_pwd', 'hash_key'],
            default => [],
        };
        $config = (array) config('raftarpay.gateways.'.$gateway, []);
        $missing = array_values(array_filter($required, fn ($key) => blank($config[$key] ?? null)));
        if ($missing) {
            return [
                'ok' => false,
                'status' => 'failed',
                'message' => 'RaftarPay '.$gateway.' is missing required merchant configuration: '.implode(', ', $missing).'.',
                'context' => ['gateway' => $gateway, 'environment' => $environment],
            ];
        }
        return [
            'ok' => true,
            'status' => 'passed',
            'message' => 'RaftarPay configuration is complete. Bank gateways do not expose a universal no-charge ping, so final validation should use a sandbox checkout.',
            'context' => ['gateway' => $gateway, 'environment' => $environment],
        ];
    }

    private function safeMessage(string $message): string
    {
        return str($message)->replaceMatches('/(sk_(?:test|live)_[A-Za-z0-9]+)/', '[redacted]')->limit(300)->toString();
    }
}
