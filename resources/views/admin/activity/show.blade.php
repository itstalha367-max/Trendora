@extends('layouts.admin')

@section('title', 'Log Detail')

@section('content')
<style>
    .detail-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .json-view {
        background: #151d2a;
        color: #cbd5e1;
        padding: 20px;
        border-radius: 12px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        overflow-x: auto;
        max-height: 400px;
    }
    .json-view .key { color: #f59e0b; }
    .json-view .string { color: #10b981; }
    .json-view .number { color: #8b5cf6; }
    .json-view .boolean { color: #ef4444; }
    .json-view .null { color: #93a1b4; }
</style>

<div class="detail-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Log Detail</h2>
            <p class="text-muted mb-0">View detailed activity log information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.activity.index') }}" class="btn btn-secondary rounded-3">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">User</label>
                        <p class="fw-bold">{{ $log->user->name ?? 'System' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Email</label>
                        <p>{{ $log->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Action</label>
                        <p><span class="badge bg-primary">{{ $log->action }}</span></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Module</label>
                        <p><span class="badge bg-secondary">{{ ucfirst($log->module) }}</span></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Date & Time</label>
                        <p>{{ $log->created_at->format('d M Y, h:i:s A') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">IP Address</label>
                        <p><code>{{ $log->ip_address }}</code></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">User Agent</label>
                        <p style="font-size: 13px; word-break: break-all;">{{ $log->user_agent }}</p>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label class="fw-bold text-muted small">Description</label>
                <p>{{ $log->description }}</p>
            </div>

            @if($log->data)
            <div>
                <label class="fw-bold text-muted small">Data</label>
                <div class="json-view">
                    <pre style="margin: 0; color: #cbd5e1;">{{ json_encode($log->data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection