@extends('layouts.admin')

@section('title', 'Abandoned Cart Detail')

@section('content')
<style>
    .detail-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="detail-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-warning"></i>Abandoned Cart Detail</h2>
            <p class="text-muted mb-0">#{{ $cart->id }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($cart->status == 'active')
                <a href="{{ route('admin.abandoned-carts.send-reminder', $cart->id) }}" class="btn btn-success rounded-3">
                    <i class="fas fa-envelope me-2"></i>Send Reminder
                </a>
                <a href="{{ route('admin.abandoned-carts.recover', $cart->id) }}" class="btn btn-warning rounded-3">
                    <i class="fas fa-check me-2"></i>Mark Recovered
                </a>
            @endif
            <a href="{{ route('admin.abandoned-carts.index') }}" class="btn btn-secondary rounded-3">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Customer</label>
                        <p class="fw-bold">{{ $cart->name ?? 'Guest' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Email</label>
                        <p>{{ $cart->email ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">User</label>
                        <p>{{ $cart->user->name ?? 'Not registered' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Status</label>
                        <p>
                            <span class="badge bg-{{ $cart->status == 'active' ? 'warning' : ($cart->status == 'recovered' ? 'success' : 'danger') }} text-{{ $cart->status == 'active' ? 'dark' : 'white' }}">
                                {{ ucfirst($cart->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Subtotal</label>
                        <p class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($cart->subtotal, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Total</label>
                        <p class="fw-bold text-primary">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($cart->total, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Coupon</label>
                        <p>{{ $cart->coupon_code ?? 'None' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Reminders Sent</label>
                        <p>{{ $cart->reminder_count }}</p>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label class="fw-bold text-muted small">Cart Items</label>
                @php $items = is_array($cart->items) ? $cart->items : []; @endphp
                @if(count($items) > 0)
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Product' }}</td>
                                    <td>{{ $item['quantity'] ?? 1 }}</td>
                                    <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item['price'] ?? 0, 2) }}</td>
                                    <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No items found</p>
                @endif
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Last Activity</label>
                        <p>{{ $cart->last_activity_at ? $cart->last_activity_at->format('d M Y, h:i A') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Session ID</label>
                        <p><code>{{ $cart->session_id ?? 'N/A' }}</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection