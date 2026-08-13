<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
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
        .header p {
            color: #b2bec3;
            margin: 5px 0 0;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .order-info .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .order-info .label {
            color: #b2bec3;
            font-weight: 600;
        }
        .order-info .value {
            font-weight: 700;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #b2bec3;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f3f5;
        }
        .total-row {
            font-size: 18px;
            font-weight: 800;
            color: #667eea;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f3f5;
            color: #b2bec3;
            font-size: 14px;
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
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Trendora</h1>
            <p>Order Confirmation</p>
        </div>

        <h2>Thank you for your order! 🎉</h2>
        <p>Hi <strong>{{ $order->user->name ?? 'Guest' }}</strong>,</p>
        <p>Your order has been confirmed and is being processed.</p>
        @if(!empty($customMessage))<div style="background:#f3f0ff;padding:16px 18px;border-radius:12px;margin:18px 0;color:#4c3f91;line-height:1.6">{!! nl2br(e($customMessage)) !!}</div>@endif

        <div class="order-info">
            <div class="row">
                <span class="label">Order Number</span>
                <span class="value">#{{ $order->order_number ?? $order->id }}</span>
            </div>
            <div class="row">
                <span class="label">Order Date</span>
                <span class="value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">Payment Status</span>
                <span class="value">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div class="row">
                <span class="label">Order Status</span>
                <span class="value">{{ ucfirst($order->order_status) }}</span>
            </div>
        </div>

        <h3>Order Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
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
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Subtotal</strong></td>
                    <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->tax > 0)
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Tax</strong></td>
                    <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->tax, 2) }}</td>
                </tr>
                @endif
                @if($order->shipping_cost > 0)
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Shipping</strong></td>
                    <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                    <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/orders/' . $order->id) }}" class="btn">View Order Details</a>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p style="font-size: 12px;">If you have any questions, contact us at support@trendora.com</p>
        </div>
    </div>
</body>
</html>