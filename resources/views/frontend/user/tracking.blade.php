@extends('layouts.app')

@section('title', 'Order Tracking')

@section('content')
<style>
    .tracking-section { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tracking-card {
        background: #111722;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .order-status {
        text-align: center;
        padding: 20px 0;
    }
    .order-status .icon {
        font-size: 48px;
        margin-bottom: 10px;
    }
    .order-status .status-text {
        font-size: 24px;
        font-weight: 700;
    }
    .order-status .status-text.pending { color: #f59e0b; }
    .order-status .status-text.processing { color: #22d3ee; }
    .order-status .status-text.shipped { color: #8b5cf6; }
    .order-status .status-text.delivered { color: #10b981; }
    .order-status .status-text.cancelled { color: #ef4444; }
    
    .progress-track {
        position: relative;
        margin: 40px 0;
    }
    .progress-track .progress-bar-track {
        height: 4px;
        background: #273142;
        border-radius: 2px;
        position: relative;
    }
    .progress-track .progress-bar-track .progress-fill {
        height: 100%;
        border-radius: 2px;
        background: linear-gradient(90deg, #8b5cf6, #5b7cff);
        transition: width 0.6s ease;
    }
    .progress-track .steps {
        display: flex;
        justify-content: space-between;
        margin-top: -10px;
        position: relative;
    }
    .progress-track .steps .step {
        text-align: center;
        flex: 1;
    }
    .progress-track .steps .step .circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #273142;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        color: #93a1b4;
        transition: all 0.3s;
        position: relative;
        z-index: 1;
    }
    .progress-track .steps .step.active .circle {
        background: #8b5cf6;
        color: #fff;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    .progress-track .steps .step.completed .circle {
        background: #10b981;
        color: #fff;
    }
    .progress-track .steps .step .label {
        display: block;
        font-size: 12px;
        color: #93a1b4;
        margin-top: 8px;
        font-weight: 600;
    }
    .progress-track .steps .step.active .label {
        color: #8b5cf6;
    }
    .progress-track .steps .step.completed .label {
        color: #10b981;
    }
    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    .order-info-grid .info-item {
        background: #0f141e;
        padding: 15px;
        border-radius: 12px;
    }
    .order-info-grid .info-item .label {
        font-size: 12px;
        color: #93a1b4;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .order-info-grid .info-item .value {
        font-weight: 600;
        margin-top: 5px;
    }
    .btn-track-again {
        padding: 12px 30px;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s;
    }
    .btn-track-again:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
</style>

<div class="tracking-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-truck me-2 text-primary"></i>Order Tracking</h2>
            <p class="text-muted mb-0">Track your order status</p>
        </div>
        <a href="{{ route('user.orders') }}" class="btn btn-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <div class="tracking-card">
        <div class="order-status">
            <div class="icon">
                @if($order->isPending())
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                @elseif($order->isProcessing())
                    <i class="fas fa-spinner" style="color: #22d3ee;"></i>
                @elseif($order->isShipped())
                    <i class="fas fa-truck" style="color: #8b5cf6;"></i>
                @elseif($order->isDelivered())
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                @elseif($order->isCancelled())
                    <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                @endif
            </div>
            <span class="status-text {{ $order->order_status }}">
                {{ $order->status_text }}
            </span>
            @if($order->tracking_number)
                <div class="mt-2">
                    <span class="badge bg-info rounded-pill px-3 py-2">
                        <i class="fas fa-barcode me-1"></i>
                        Tracking: {{ $order->tracking_number }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Progress Tracker -->
        <div class="progress-track">
            <div class="progress-bar-track">
                <div class="progress-fill" style="width: {{ $order->status_progress }}%;"></div>
            </div>
            <div class="steps">
                <div class="step {{ $order->isPending() ? 'active' : ($order->isProcessing() || $order->isShipped() || $order->isDelivered() ? 'completed' : '') }}">
                    <div class="circle">1</div>
                    <span class="label">Order Placed</span>
                </div>
                <div class="step {{ $order->isProcessing() ? 'active' : ($order->isShipped() || $order->isDelivered() ? 'completed' : '') }}">
                    <div class="circle">2</div>
                    <span class="label">Processing</span>
                </div>
                <div class="step {{ $order->isShipped() ? 'active' : ($order->isDelivered() ? 'completed' : '') }}">
                    <div class="circle">3</div>
                    <span class="label">Shipped</span>
                </div>
                <div class="step {{ $order->isDelivered() ? 'completed' : '' }}">
                    <div class="circle">4</div>
                    <span class="label">Delivered</span>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="order-info-grid">
            <div class="info-item">
                <div class="label">Order Number</div>
                <div class="value">#{{ $order->order_number }}</div>
            </div>
            <div class="info-item">
                <div class="label">Order Date</div>
                <div class="value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Total Amount</div>
                <div class="value text-primary">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total, 2) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Payment Status</div>
                <div class="value">
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} rounded-pill">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="mt-3">
            <div class="info-item" style="background: #0f141e; padding: 15px; border-radius: 12px;">
                <div class="label">Shipping Address</div>
                <div class="value">
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state ?? '' }} {{ $order->shipping_zip ?? '' }}<br>
                    {{ $order->shipping_country }}
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mt-3">
            <h6 class="fw-bold">Order Items</h6>
            <div class="table-responsive">
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
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->price, 2) }}</td>
                            <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection