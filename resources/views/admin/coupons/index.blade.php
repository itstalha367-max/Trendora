@extends('layouts.admin')

@section('title', 'Manage Coupons')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-add {
        background: linear-gradient(135deg, #f59e0b, #f9a825);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(253, 203, 110, 0.4);
        color: #fff;
    }
    .coupon-code {
        background: linear-gradient(135deg, #0f141e, #273142);
        padding: 5px 15px;
        border-radius: 8px;
        font-weight: 700;
        font-family: monospace;
        font-size: 16px;
        letter-spacing: 1px;
    }
    .badge-status {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-ticket-alt me-2 text-warning"></i>Manage Coupons</h2>
        <p class="text-muted mb-0">Create and manage discount coupons</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn-add">
        <i class="fas fa-plus me-2"></i>Add Coupon
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">Code</th>
                        <th style="padding: 15px 20px;">Name</th>
                        <th style="padding: 15px 20px;">Type</th>
                        <th style="padding: 15px 20px;">Value</th>
                        <th style="padding: 15px 20px;">Min Order</th>
                        <th style="padding: 15px 20px;">Used</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Expiry</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr>
                        <td style="padding: 15px 20px;">
                            <span class="coupon-code">{{ $coupon->code }}</span>
                        </td>
                        <td style="padding: 15px 20px;">{{ $coupon->name ?? 'N/A' }}</td>
                        <td style="padding: 15px 20px;">
                            <span class="badge bg-{{ $coupon->type == 'percentage' ? 'info' : 'primary' }} rounded-pill px-3 py-2">
                                {{ ucfirst($coupon->type) }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($coupon->type == 'percentage')
                                {{ $coupon->value }}%
                            @else
                                {{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($coupon->value, 2) }}
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($coupon->min_order > 0)
                                {{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($coupon->min_order, 2) }}
                            @else
                                <span class="text-muted">No min</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $coupon->used_count }}
                            @if($coupon->usage_limit)
                                / {{ $coupon->usage_limit }}
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $coupon->status ? 'success' : 'danger' }} text-white">
                                {{ $coupon->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($coupon->end_date)
                                {{ $coupon->end_date->format('d M Y') }}
                                @if($coupon->end_date->lt(now()))
                                    <span class="badge bg-danger ms-1">Expired</span>
                                @endif
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.coupons.toggle', $coupon->id) }}" class="btn btn-sm {{ $coupon->status ? 'btn-warning' : 'btn-success' }} rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas {{ $coupon->status ? 'fa-pause' : 'fa-play' }}"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No coupons found</h5>
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-warning mt-2">
                                <i class="fas fa-plus me-2"></i>Add Your First Coupon
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($coupons->hasPages())
        <div class="p-3 border-top">{{ $coupons->links() }}</div>
        @endif
    </div>
</div>
@endsection