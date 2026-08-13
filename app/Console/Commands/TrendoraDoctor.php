<?php

namespace App\Console\Commands;

use App\Services\ProductionReadinessService;
use Illuminate\Console\Command;

class TrendoraDoctor extends Command
{
    protected $signature = 'trendora:doctor {--strict : Treat warnings as failures} {--json : Output machine-readable JSON}';
    protected $description = 'Run Trendora production-readiness diagnostics without creating payments or modifying orders.';

    public function handle(ProductionReadinessService $service): int
    {
        $report = $service->report();
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->newLine();
            $this->info('Trendora production readiness: '.$report['score'].'% · '.strtoupper($report['status']));
            foreach ($report['groups'] as $group => $checks) {
                $this->newLine();
                $this->components->twoColumnDetail('<fg=cyan>'.$group.'</>', '');
                foreach ($checks as $check) {
                    $icon = match ($check['status']) { 'ok' => '<fg=green>PASS</>', 'warn' => '<fg=yellow>WARN</>', default => '<fg=red>FAIL</>' };
                    $this->components->twoColumnDetail($check['label'].' · '.$check['value'], $icon);
                }
            }
            $this->newLine();
            $this->line('Use Admin → Production Readiness for SMTP/payment/webhook connectivity tests.');
        }

        if ($report['counts']['fail'] > 0) return self::FAILURE;
        if ($this->option('strict') && $report['counts']['warn'] > 0) return self::FAILURE;
        return self::SUCCESS;
    }
}
