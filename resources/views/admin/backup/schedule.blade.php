@extends('layouts.admin')
@section('title','Backup Schedule')
@section('page-title','Backup Schedule')
@section('content')
@php
    $frequency=App\Models\Setting::get('backup_frequency','daily');
    $time=App\Models\Setting::get('backup_time','02:00');
    $keep=App\Models\Setting::get('backup_keep','14');
@endphp
<div class="ad-page-head"><div><a class="ad-back" href="{{ route('admin.backup.index') }}">← Backup center</a><h2>Retention schedule</h2><p>Store the preferred backup cadence and retention target. Your server scheduler/cron still needs to invoke the backup job.</p></div></div>
<div class="row g-4"><div class="col-xl-7"><form class="card p-4" method="POST" action="{{ route('admin.backup.schedule.update') }}">@csrf @method('PUT')<div class="row g-3"><div class="col-md-4"><label class="form-label">Frequency</label><select class="form-select" name="frequency" required><option value="daily" @selected($frequency==='daily')>Daily</option><option value="weekly" @selected($frequency==='weekly')>Weekly</option><option value="monthly" @selected($frequency==='monthly')>Monthly</option></select></div><div class="col-md-4"><label class="form-label">Preferred time</label><input class="form-control" type="time" name="time" value="{{ $time }}" required></div><div class="col-md-4"><label class="form-label">Keep archives</label><input class="form-control" type="number" min="1" max="100" name="keep_backups" value="{{ $keep }}" required></div></div><button class="btn btn-primary mt-4 align-self-start" type="submit">Save schedule</button></form></div><div class="col-xl-5"><div class="card p-4 h-100"><span class="ad-eyebrow">Server requirement</span><h5 class="mt-2">Cron still drives automation</h5><p class="text-muted">The values above are store preferences. Configure your server scheduler to run Laravel every minute, then invoke your backup workflow at the preferred cadence.</p><code>* * * * * php /path/to/artisan schedule:run</code><p class="small text-muted mt-3 mb-0">Also confirm <code>php-zip</code> is enabled before relying on automated archives.</p></div></div></div>
@endsection
