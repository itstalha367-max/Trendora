<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TrendoraSmokeTest extends Command
{
    protected $signature = 'trendora:smoke {--url= : Base URL of the running Trendora app} {--strict : Fail on redirects as well as errors}';
    protected $description = 'Run read-only HTTP smoke checks against the main public Trendora surfaces.';

    public function handle(): int
    {
        $base = rtrim((string) ($this->option('url') ?: config('app.url')), '/');
        if ($base === '') {
            $this->error('Set APP_URL or pass --url=https://store.example.com');
            return self::FAILURE;
        }

        $paths = [
            '/up' => 'Health endpoint',
            '/' => 'Homepage',
            '/products' => 'Catalog',
            '/categories' => 'Categories',
            '/journal' => 'Journal',
            '/about' => 'About',
            '/faq' => 'FAQ',
            '/help' => 'Help center',
            '/login' => 'Customer login',
            '/register' => 'Customer registration',
            '/admin/login' => 'Admin login',
        ];

        $failed = 0;
        $this->info('Trendora read-only smoke test · '.$base);
        $this->newLine();

        foreach ($paths as $path => $label) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders(['User-Agent' => 'TrendoraSmoke/1.0'])
                    ->withOptions(['allow_redirects' => false])
                    ->get($base.$path);
                $status = $response->status();
                $ok = $this->option('strict') ? $status >= 200 && $status < 300 : $status >= 200 && $status < 400;
                if (!$ok) $failed++;
                $this->components->twoColumnDetail($label.' · '.$path.' · HTTP '.$status, $ok ? '<fg=green>PASS</>' : '<fg=red>FAIL</>');
            } catch (\Throwable $e) {
                $failed++;
                $this->components->twoColumnDetail($label.' · '.$path, '<fg=red>ERROR</>');
                $this->line('  '.$e->getMessage());
            }
        }

        $this->newLine();
        if ($failed > 0) {
            $this->error($failed.' smoke check(s) failed. No write actions were performed.');
            return self::FAILURE;
        }

        $this->info('All read-only smoke checks passed.');
        return self::SUCCESS;
    }
}
