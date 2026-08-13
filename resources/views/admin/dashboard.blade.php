@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    /* 🎨 Dashboard Animations */
    .welcome-banner {
        background: linear-gradient(135deg, #8b5cf6 0%, #5b7cff 100%);
        border-radius: 20px;
        padding: 35px 40px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        animation: slideDown 0.6s ease-out;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        animation: floatBanner 8s infinite ease-in-out;
    }
    
    @keyframes floatBanner {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-20px, -20px) scale(1.1); }
    }
    
    .welcome-banner h2 { color: #fff; font-weight: 800; position: relative; z-index: 1; }
    .welcome-banner p { color: rgba(255,255,255,0.8); position: relative; z-index: 1; margin: 0; }
    
    .welcome-banner .date-badge {
        background: rgba(255,255,255,0.2);
        padding: 8px 20px;
        border-radius: 50px;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
        backdrop-filter: blur(10px);
    }
    
    /* 🎨 Stats Cards */
    .stat-card {
        background: #111722;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    .stat-card:nth-child(5) { animation-delay: 0.5s; }
    .stat-card:nth-child(6) { animation-delay: 0.6s; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    }
    
    .stat-card .icon-wrapper {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s;
    }
    
    .stat-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(-5deg);
    }
    
    .stat-card .stat-number {
        font-size: 32px;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #151d2a, #7f8da0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .stat-card .stat-label {
        color: #93a1b4;
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* 🎨 Chart Cards */
    .chart-card {
        background: #111722;
        border-radius: 20px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.04);
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        animation-delay: 0.4s;
        overflow: hidden;
        padding: 20px;
    }
    
    .chart-card .card-header {
        background: transparent;
        border-bottom: 2px solid #1a2230;
        padding: 10px 0 15px;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    /* 🎨 Low Stock Alert */
    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;
        border-radius: 12px;
        background: #17130d;
        border-left: 4px solid #ef4444;
        margin-bottom: 8px;
        transition: all 0.3s;
    }
    
    .alert-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.15);
    }
    
    .alert-item .stock-badge {
        padding: 3px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        background: #ef4444;
        color: #fff;
    }
    
    /* 🎨 Quick Actions */
    .quick-action {
        background: #111722;
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        text-decoration: none;
        color: #151d2a;
        display: block;
    }
    
    .quick-action:nth-child(1) { animation-delay: 0.7s; }
    .quick-action:nth-child(2) { animation-delay: 0.8s; }
    .quick-action:nth-child(3) { animation-delay: 0.9s; }
    .quick-action:nth-child(4) { animation-delay: 1s; }
    
    .quick-action:hover {
        transform: translateY(-5px);
        border-color: #8b5cf6;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.15);
        color: #151d2a;
    }
    
    .quick-action .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    
    .quick-action:hover .icon-box {
        transform: scale(1.1) rotate(5deg);
    }
    
    .quick-action h6 { font-weight: 700; margin: 0; }
    .quick-action small { color: #93a1b4; font-size: 13px; }
    
    /* 🎨 Top Products */
    .top-product-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #1a2230;
    }
    
    .top-product-item:last-child { border-bottom: none; }
    
    .top-product-item .rank {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        background: #0f141e;
    }
    
    .top-product-item .rank.gold { background: #f59e0b; color: #fff; }
    .top-product-item .rank.silver { background: #93a1b4; color: #fff; }
    .top-product-item .rank.bronze { background: #f59e0b; color: #fff; }
    
    .badge-status {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .stat-card .stat-number { font-size: 24px; }
        .welcome-banner { padding: 20px; }
        .chart-container { height: 200px; }
    }
</style>

<!-- 🎨 Welcome Banner -->
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-wave-square me-2"></i>Welcome back, {{ Auth::user()->name }}! </h2>
            <p>Here's what's happening with your store today.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="date-badge">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('l, d M Y') }}
            </span>
        </div>
    </div>
</div>

<!-- 🎨 Statistics Cards -->
<div class="row g-4">
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label"><i class="fas fa-box me-1"></i> Products</p>
                    <h3 class="stat-number">{{ $stats['total_products'] }}</h3>
                </div>
                <div class="icon-wrapper" style="background: linear-gradient(135deg, rgba(139,92,246,.12), rgba(91,124,255,.12)); color: #8b5cf6;">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label"><i class="fas fa-shopping-cart me-1"></i> Orders</p>
                    <h3 class="stat-number">{{ $stats['total_orders'] }}</h3>
                </div>
                <div class="icon-wrapper" style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(16,185,129,.12)); color: #10b981;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label"><i class="fas fa-users me-1"></i> Users</p>
                    <h3 class="stat-number">{{ $stats['total_users'] }}</h3>
                </div>
                <div class="icon-wrapper" style="background: linear-gradient(135deg, rgba(245,158,11,.12), rgba(245,158,11,.12)); color: #f59e0b;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label"><i class="fas fa-clock me-1"></i> Pending</p>
                    <h3 class="stat-number">{{ $stats['pending_orders'] }}</h3>
                </div>
                <div class="icon-wrapper" style="background: linear-gradient(135deg, rgba(245,158,11,.10), rgba(245,158,11,.10)); color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label"><i class="fas fa-tag me-1"></i> Categories</p>
                    <h3 class="stat-number">{{ $stats['total_categories'] }}</h3>
                </div>
                <div class="icon-wrapper" style="background: linear-gradient(135deg, rgba(91,124,255,.12), rgba(91,124,255,.12)); color: #5b7cff;">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label"><i class="fas fa-dollar-sign me-1"></i> Revenue</p>
                    <h3 class="stat-number">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($totalRevenue ?? 0, 0) }}</h3>
                </div>
                <div class="icon-wrapper" style="background: linear-gradient(135deg, rgba(239,68,68,.10), rgba(239,68,68,.10)); color: #ef4444;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🎨 Charts Row -->
<div class="row g-4 mt-2">
    <div class="col-md-8">
        <div class="chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Sales Overview</h5>
                <span class="badge bg-primary rounded-pill px-3 py-2">Last 7 Days</span>
            </div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-crown me-2 text-warning"></i>Top Products</h5>
            </div>
            <div style="padding: 10px 0;">
                @if($topProducts->count() > 0)
                    @foreach($topProducts as $index => $product)
                    <div class="top-product-item">
                        <div class="d-flex align-items-center gap-3">
                            <span class="rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->order_items_count ?? 0 }} sales</small>
                            </div>
                        </div>
                        <span class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->price, 2) }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-box-open fa-2x d-block mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">No products sold yet</p>
                        <small>Add products to see stats here</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 🎨 Low Stock Alerts -->
@if($lowStockProducts->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4" style="border-left: 5px solid #ef4444;">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Low Stock Alert
                    <span class="badge bg-danger ms-2">{{ $lowStockProducts->count() }} Products</span>
                </h5>
                <div class="row g-2">
                    @foreach($lowStockProducts as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="alert-item">
                            <div>
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                            </div>
                            <span class="stock-badge">{{ $product->stock_quantity }} left</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- 🎨 Recent Orders -->
<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i>Recent Orders</h5>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">Order #</th>
                        <th style="padding: 15px 20px;">Customer</th>
                        <th style="padding: 15px 20px;">Total</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Date</th>
                        <th style="padding: 15px 20px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recent_orders'] as $order)
                    <tr>
                        <td style="padding: 15px 20px;">
                            <span class="fw-bold">#{{ $order->order_number ?? $order->id }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b7cff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px;">
                                    {{ substr($order->user->name ?? 'G', 0, 1) }}
                                </div>
                                {{ $order->user->name ?? 'Guest' }}
                            </div>
                        </td>
                        <td style="padding: 15px 20px;" class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total ?? 0, 2) }}</td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $order->order_status == 'pending' ? 'warning' : ($order->order_status == 'processing' ? 'info' : ($order->order_status == 'delivered' ? 'success' : 'danger')) }} text-{{ $order->order_status == 'pending' ? 'dark' : 'white' }}">
                                <i class="fas fa-{{ $order->order_status == 'pending' ? 'clock' : ($order->order_status == 'processing' ? 'spinner' : ($order->order_status == 'delivered' ? 'check' : 'times')) }} me-1"></i>
                                {{ ucfirst($order->order_status ?? 'Pending') }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">{{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}</td>
                        <td style="padding: 15px 20px;">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x d-block mb-2" style="opacity: 0.2;"></i>
                            No orders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>

<!-- 🎨 Quick Actions -->
<div class="row g-4 mt-3">
    <div class="col-md-3">
        <a href="{{ route('admin.products.index') }}" class="quick-action">
            <div class="icon-box" style="background: linear-gradient(135deg, rgba(139,92,246,.12), rgba(91,124,255,.12)); color: #8b5cf6;">
                <i class="fas fa-box"></i>
            </div>
            <h6>Manage Products</h6>
            <small>Add, edit or delete products</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.categories.index') }}" class="quick-action">
            <div class="icon-box" style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(16,185,129,.12)); color: #10b981;">
                <i class="fas fa-tags"></i>
            </div>
            <h6>Manage Categories</h6>
            <small>Organize your products</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.orders.index') }}" class="quick-action">
            <div class="icon-box" style="background: linear-gradient(135deg, rgba(245,158,11,.12), rgba(245,158,11,.12)); color: #f59e0b;">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h6>Manage Orders</h6>
            <small>View and process orders</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.users.index') }}" class="quick-action">
            <div class="icon-box" style="background: linear-gradient(135deg, rgba(239,68,68,.10), rgba(239,68,68,.10)); color: #ef4444;">
                <i class="fas fa-users"></i>
            </div>
            <h6>Manage Users</h6>
            <small>View and manage customers</small>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesData = @json($salesData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels || ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales ($)',
                data: salesData.sales || [0, 0, 0, 0, 0, 0, 0],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8b5cf6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#151d2a',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    cornerRadius: 10,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: { callback: function(value) { return '$' + value; } }
                },
                x: { grid: { display: false } }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
});
</script>
@endpush