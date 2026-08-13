@extends('layouts.app')

@section('title', 'Order Success')

@section('content')
<style>
    .success-section {
        text-align: center;
        padding: 60px 20px;
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .success-icon {
        font-size: 80px;
        color: #10b981;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    .order-card {
        background: #111722;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.04);
        text-align: left;
        max-width: 500px;
        margin: 0 auto;
    }
    .order-card .row-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #1a2230;
    }
    .order-card .row-item:last-child {
        border-bottom: none;
        border-top: 2px solid #1a2230;
        padding-top: 15px;
        margin-top: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #8b5cf6;
    }
    .btn-continue {
        padding: 12px 40px;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-continue:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
</style>

<div class="success-section">
    <div class="success-icon">✅</div>
    <h2 class="fw-bold mt-3">Order Placed Successfully! 🎉</h2>
    <p class="text-muted">Thank you for your order. We'll process it as soon as possible.</p>

    <div class="order-card mt-4">
        <h5 class="fw-bold">Order #{{ $order->order_number }}</h5>
        <div class="row-item">
            <span>Subtotal</span>
            <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div class="row-item">
            <span>Tax</span>
            <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->tax, 2) }}</span>
        </div>
        <div class="row-item">
            <span>Shipping</span>
            <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->shipping_cost, 2) }}</span>
        </div>
        <div class="row-item">
            <span>Total</span>
            <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total, 2) }}</span>
        </div>
        <div class="mt-3">
            <span class="badge bg-{{ $order->order_status == 'pending' ? 'warning' : 'success' }} rounded-pill px-3 py-2">
                {{ ucfirst($order->order_status) }}
            </span>
        </div>
    </div>

    <a href="{{ route('home') }}" class="btn-continue mt-4">
        <i class="fas fa-home me-2"></i>Continue Shopping
    </a>
</div>
@endsection