@extends('layouts.admin')

@section('title', 'Abandoned Carts')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stat-box {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        text-align: center;
    }
    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .stat-box .number {
        font-size: 28px;
        font-weight: 800;
        margin: 0;
    }
    .stat-box .label {
        color: #93a1b4;
        font-size: 14px;
        margin: 0;
    }
    .filter-card {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .cart-item {
        transition: all 0.3s;
    }
    .cart-item:hover {
        background: #0f141e;
    }
    .badge-status {
        padding: 4px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s;
    }
    .btn-action:hover {
        transform: scale(1.1);
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-warning"></i>Abandoned Carts</h2>
        <p class="text-muted mb-0">Track and recover lost sales</p>
    </div>
</div>

<!-- 📊 Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Total Carts</p>
            <h3 class="number text-primary">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Active</p>
            <h3 class="number text-warning">{{ $stats['active'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Recovered</p>
            <h3 class="number text-success">{{ $stats['recovered'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Expired</p>
            <h3 class="number text-danger">{{ $stats['expired'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Total Value</p>
            <h3 class="number text-info">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($stats['total_value'], 2) }}</h3>
        </div>
    </div>
</div>

<!-- 🔍 Filters -->
<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-bold small text-muted">Search</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by email or name..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">Status</label>
            <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="recovered" {{ request('status') == 'recovered' ? 'selected' : '' }}>Recovered</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" onclick="applyFilters()">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success w-100" onclick="sendBulkReminder()">
                <i class="fas fa-envelope me-2"></i>Send Reminders
            </button>
        </div>
    </div>
</div>

<!-- 📋 Carts Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px; width: 40px;">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th style="padding: 15px 20px;">Customer</th>
                        <th style="padding: 15px 20px;">Items</th>
                        <th style="padding: 15px 20px;">Total</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Last Activity</th>
                        <th style="padding: 15px 20px;">Reminders</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carts as $cart)
                    <tr class="cart-item">
                        <td style="padding: 15px 20px;">
                            <input type="checkbox" class="cart-checkbox" value="{{ $cart->id }}">
                        </td>
                        <td style="padding: 15px 20px;">
                            <div>
                                <div class="fw-bold">{{ $cart->name ?? 'Guest' }}</div>
                                <small class="text-muted">{{ $cart->email ?? 'No email' }}</small>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            @php
                                $items = is_array($cart->items) ? $cart->items : [];
                            @endphp
                            <span class="fw-bold">{{ count($items) }}</span>
                            <small class="text-muted">items</small>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($cart->total, 2) }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $cart->status == 'active' ? 'warning' : ($cart->status == 'recovered' ? 'success' : 'danger') }} text-{{ $cart->status == 'active' ? 'dark' : 'white' }}">
                                {{ ucfirst($cart->status) }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $cart->last_activity_at ? $cart->last_activity_at->diffForHumans() : 'N/A' }}
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $cart->reminder_count }}
                            @if($cart->reminder_sent_at)
                                <br><small class="text-muted">{{ $cart->reminder_sent_at->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.abandoned-carts.show', $cart->id) }}" class="btn btn-sm btn-primary btn-action" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($cart->status == 'active')
                                    <a href="{{ route('admin.abandoned-carts.send-reminder', $cart->id) }}" class="btn btn-sm btn-success btn-action" title="Send Reminder">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                    <a href="{{ route('admin.abandoned-carts.recover', $cart->id) }}" class="btn btn-sm btn-warning btn-action" title="Mark as Recovered">
                                        <i class="fas fa-check"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.abandoned-carts.destroy', $cart->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No abandoned carts found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($carts->hasPages())
        <div class="p-3 border-top">{{ $carts->links() }}</div>
        @endif
    </div>
</div>

<script>
// Select All
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.cart-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    let url = new URL(window.location.href);
    
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    window.location.href = url.toString();
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

function sendBulkReminder() {
    const ids = [];
    document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
        ids.push(cb.value);
    });
    
    if (ids.length === 0) {
        alert('Please select carts to send reminders');
        return;
    }
    
    if (!confirm('Send reminders to ' + ids.length + ' customers?')) return;
    
    fetch('{{ route("admin.abandoned-carts.bulk-reminder") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    });
}
</script>
@endsection