<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Invoice - {{ $order->order_number ?? $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }
        
        body {
            background: #fff;
            padding: 40px;
            color: #2d3436;
        }
        
        .invoice-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
        }
        
        /* 🎨 Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
            margin-bottom: 30px;
        }
        
        .header .brand h1 {
            font-size: 28px;
            color: #667eea;
            font-weight: 800;
        }
        
        .header .brand small {
            color: #b2bec3;
            font-size: 14px;
        }
        
        .header .invoice-info {
            text-align: right;
        }
        
        .header .invoice-info h2 {
            font-size: 24px;
            color: #2d3436;
        }
        
        .header .invoice-info p {
            color: #b2bec3;
            font-size: 14px;
            margin: 2px 0;
        }
        
        /* 🎨 Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }
        
        .status-badge.pending { background: #fdcb6e; color: #2d3436; }
        .status-badge.processing { background: #74b9ff; }
        .status-badge.shipped { background: #a29bfe; }
        .status-badge.delivered { background: #00b894; }
        .status-badge.cancelled { background: #ff6b6b; }
        
        /* 🎨 Customer Info */
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }
        
        .customer-info .label {
            font-size: 12px;
            color: #b2bec3;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        
        .customer-info .value {
            font-size: 16px;
            font-weight: 600;
            margin-top: 5px;
        }
        
        /* 🎨 Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 30px;
        }
        
        .table thead {
            background: #f8f9fa;
        }
        
        .table thead th {
            padding: 12px 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #636e72;
            font-weight: 700;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f3f5;
            font-size: 14px;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* 🎨 Totals */
        .totals {
            width: 300px;
            margin-left: auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 14px;
        }
        
        .totals .row.total {
            border-top: 2px solid #667eea;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 20px;
            font-weight: 800;
            color: #667eea;
        }
        
        /* 🎨 Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f1f3f5;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #b2bec3;
        }
        
        .footer .signature {
            text-align: center;
        }
        
        .footer .signature .line {
            width: 150px;
            border-top: 1px solid #dee2e6;
            margin: 5px auto;
        }
        
        /* 🎨 Print Styles */
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
            .header { border-bottom-color: #667eea; }
            .table thead { background: #f8f9fa; }
            .totals { background: #f8f9fa; }
        }
        
        /* 🎨 Responsive */
        @media (max-width: 768px) {
            .header { flex-direction: column; text-align: center; }
            .header .invoice-info { text-align: center; margin-top: 15px; }
            .customer-info { flex-direction: column; gap: 15px; }
            .totals { width: 100%; }
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/astra-commerce.css') }}">
</head>
<body class="trendora-invoice astra-commerce">
    <div class="invoice-wrapper">
        <!-- 🎨 Header -->
        <div class="header">
            <div class="brand">
                <h1>🛍️ {{ $company['name'] }}</h1>
                <small>{{ $company['address'] }}</small>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>Invoice #:</strong> {{ $order->order_number ?? $order->id }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge {{ $order->order_status }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </p>
            </div>
        </div>
        
        <!-- 🎨 Customer Info -->
        <div class="customer-info">
            <div>
                <div class="label">Bill To</div>
                <div class="value">{{ $order->user->name ?? 'Guest' }}</div>
                <div style="color: #636e72; font-size: 14px; margin-top: 3px;">
                    {{ $order->user->email ?? 'N/A' }}
                </div>
            </div>
            <div>
                <div class="label">Shipping Address</div>
                <div class="value">{{ $order->shipping_name }}</div>
                <div style="color: #636e72; font-size: 14px; margin-top: 3px;">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state ?? '' }} {{ $order->shipping_zip ?? '' }}<br>
                    {{ $order->shipping_country }}
                </div>
            </div>
            <div>
                <div class="label">Contact</div>
                <div style="margin-top: 5px;">
                    <div><i class="fas fa-phone"></i> {{ $order->shipping_phone }}</div>
                    <div><i class="fas fa-envelope"></i> {{ $order->shipping_email }}</div>
                </div>
            </div>
        </div>
        
        <!-- 🎨 Order Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product_sku)
                            <br><small style="color: #b2bec3;">SKU: {{ $item->product_sku }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- 🎨 Totals -->
        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->tax > 0)
            <div class="row">
                <span>Tax</span>
                <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->tax, 2) }}</span>
            </div>
            @endif
            @if($order->shipping_cost > 0)
            <div class="row">
                <span>Shipping</span>
                <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->shipping_cost, 2) }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="row" style="color: #ff6b6b;">
                <span>Discount</span>
                <span>-{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="row total">
                <span>Total</span>
                <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total, 2) }}</span>
            </div>
        </div>
        
        <!-- 🎨 Payment Info -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 12px; color: #b2bec3; text-transform: uppercase; font-weight: 700;">Payment Method</div>
                    <div style="font-weight: 600; margin-top: 3px;">
                        {{ ucfirst($order->payment_gateway ?? 'N/A') }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #b2bec3; text-transform: uppercase; font-weight: 700;">Transaction ID</div>
                    <div style="font-weight: 600; margin-top: 3px;">
                        {{ $order->transaction_id ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #b2bec3; text-transform: uppercase; font-weight: 700;">Payment Status</div>
                    <div style="font-weight: 600; margin-top: 3px; color: {{ $order->payment_status == 'paid' ? '#00b894' : '#fdcb6e' }}">
                        {{ ucfirst($order->payment_status) }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 🎨 Notes -->
        @if($order->notes)
        <div style="margin-top: 20px; padding: 15px; border-left: 4px solid #667eea; background: #f8f9fa; border-radius: 12px;">
            <div style="font-size: 12px; color: #b2bec3; text-transform: uppercase; font-weight: 700;">Order Notes</div>
            <div style="margin-top: 3px;">{{ $order->notes }}</div>
        </div>
        @endif
        
        <!-- 🎨 Footer -->
        <div class="footer">
            <div>
                <strong>{{ $company['name'] }}</strong><br>
                {{ $company['address'] }}<br>
                {{ $company['phone'] }} | {{ $company['email'] }}
            </div>
            <div class="signature">
                <div class="line"></div>
                <span style="font-size: 12px; color: #b2bec3;">Authorized Signature</span>
            </div>
            <div style="text-align: right;">
                <div>Thank you for your business!</div>
                <div style="font-size: 12px; color: #b2bec3;">{{ $company['website'] }}</div>
            </div>
        </div>
    </div>
</body>
</html>