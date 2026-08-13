<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            font-size: 28px;
            margin: 0;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 25px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 700;
            background: #00b894;
            color: #fff;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f3f5;
            color: #b2bec3;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Trendora</h1>
            <p>Order Status Update</p>
        </div>

        <h2>Hi {{ $order->user->name ?? 'Guest' }},</h2>

        @if(!empty($customMessage))
            <div class="order-info" style="white-space: pre-line;">{!! nl2br(e($customMessage)) !!}</div>
        @else
            <p>Your order status has been updated:</p>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 20px; color: #b2bec3;">
                <span style="text-decoration: line-through;">{{ ucfirst($oldStatus) }}</span>
                <span style="font-size: 30px; margin: 0 15px;">→</span>
                <span class="status-badge">{{ ucfirst($newStatus) }}</span>
            </p>
        </div>

        <div class="order-info">
            <p><strong>Order Number:</strong> #{{ $order->order_number ?? $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
            <p><strong>Total:</strong> {{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total, 2) }}</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/orders/' . $order->id) }}" class="btn">View Order</a>
        </div>

        <div class="footer">
            <p>If you have any questions, contact us at support@trendora.com</p>
        </div>
    </div>
</body>
</html>