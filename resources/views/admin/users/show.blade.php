@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<style>
    .detail-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 32px;
        color: #fff;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
    }
</style>

<div class="detail-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-user me-2 text-primary"></i>User Details</h2>
            <p class="text-muted mb-0">{{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="avatar-large mx-auto mb-3">{{ substr($user->name, 0, 1) }}</div>
                <h4 class="fw-bold">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->role == 'admin' ? 'success' : 'primary' }} rounded-pill px-4 py-2">
                    <i class="fas fa-{{ $user->role == 'admin' ? 'crown' : 'user' }} me-1"></i>
                    {{ ucfirst($user->role) }}
                </span>
                <p class="mt-3 text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Joined {{ $user->created_at->format('d M Y') }}
                </p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-shopping-bag me-2"></i>Order History</h5>
                    @if($user->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->orders as $order)
                                    <tr>
                                        <td>#{{ $order->order_number ?? $order->id }}</td>
                                        <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->order_status == 'pending' ? 'warning' : 'success' }}">
                                                {{ ucfirst($order->order_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No orders yet</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
                <h5 class="fw-bold">Store Credit</h5>
                <div class="display-6 fw-bold mb-3">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($user->store_credit ?? 0,2) }}</div>
                <form method="POST" action="{{ route('admin.users.wallet',$user) }}">@csrf
                    <label class="form-label">Adjustment (+ credit / - debit)</label><input class="form-control" type="number" step="0.01" name="amount" required>
                    <label class="form-label mt-2">Reason</label><input class="form-control" name="note" required>
                    <button class="btn btn-primary w-100 mt-3">Apply adjustment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection