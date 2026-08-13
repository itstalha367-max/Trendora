@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .filter-card {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .log-item {
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    .log-item:hover {
        background: #0f141e;
    }
    .log-item .badge-action {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
    }
    .log-item .badge-action.post { background: rgba(139,92,246,.12); color: #8b5cf6; }
    .log-item .badge-action.put { background: rgba(245,158,11,.12); color: #f59e0b; }
    .log-item .badge-action.delete { background: #ef444420; color: #ef4444; }
    .log-item .badge-action.patch { background: rgba(91,124,255,.12); color: #5b7cff; }
    
    .log-item .module-badge {
        padding: 2px 10px;
        border-radius: 50px;
        font-size: 11px;
        background: #1a2230;
        color: #7f8da0;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Activity Logs</h2>
        <p class="text-muted mb-0">Track all admin activities</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.activity.clear-old') }}" class="btn btn-warning rounded-3" onclick="return confirm('Delete logs older than 30 days?')">
            <i class="fas fa-trash-alt me-2"></i>Clear Old
        </a>
        <a href="{{ route('admin.activity.clear') }}" class="btn btn-danger rounded-3" onclick="return confirm('Delete all logs?')">
            <i class="fas fa-trash me-2"></i>Clear All
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- 🔍 Filters -->
<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-bold small text-muted">Search</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by user, description..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">Module</label>
            <select class="form-select" id="moduleFilter">
                <option value="">All Modules</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                        {{ ucfirst($module) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">Action</label>
            <select class="form-select" id="actionFilter">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ $action }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" onclick="applyFilters()">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
    </div>
</div>

<!-- 📋 Logs Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">User</th>
                        <th style="padding: 15px 20px;">Action</th>
                        <th style="padding: 15px 20px;">Module</th>
                        <th style="padding: 15px 20px;">Description</th>
                        <th style="padding: 15px 20px;">IP Address</th>
                        <th style="padding: 15px 20px;">Date/Time</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="log-item" style="border-left-color: {{ $log->action == 'POST' ? '#8b5cf6' : ($log->action == 'DELETE' ? '#ef4444' : '#f59e0b') }};">
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b7cff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px;">
                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $log->user->name ?? 'System' }}</div>
                                    <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-action {{ strtolower($log->action) }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="module-badge">{{ ucfirst($log->module) }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $log->description }}
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <code>{{ $log->ip_address }}</code>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div>{{ $log->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <a href="{{ route('admin.activity.show', $log->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-history fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No activity logs found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($logs->hasPages())
        <div class="p-3 border-top">{{ $logs->links() }}</div>
        @endif
    </div>
</div>

<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const module = document.getElementById('moduleFilter').value;
    const action = document.getElementById('actionFilter').value;
    
    let url = new URL(window.location.href);
    
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    if (module) url.searchParams.set('module', module);
    else url.searchParams.delete('module');
    
    if (action) url.searchParams.set('action', action);
    else url.searchParams.delete('action');
    
    window.location.href = url.toString();
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>
@endsection