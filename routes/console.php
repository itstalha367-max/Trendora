<?php

use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// This heartbeat lets the Production Readiness screen verify that cron is actually running.
Schedule::call(function () {
    try { Setting::set('scheduler_heartbeat_at', now()->toIso8601String()); } catch (\Throwable $e) {}
})->everyMinute()->name('trendora-scheduler-heartbeat')->withoutOverlapping();
