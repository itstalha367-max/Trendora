@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<style>
    .report-header { animation: slideDown 0.5s ease-out; }
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
    .chart-container {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
    }
</style>

<div class="report-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Sales Report</h2>
            <p class="text-muted mb-0">Revenue and order analytics</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports') }}" class="btn btn-secondary rounded-3">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            <select class="form-select w-auto" onchange="window.location.href='?period='+this.value">
                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
            </select>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Total Revenue</p>
                <h3 class="number text-primary">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($summary['total_revenue'] ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Total Orders</p>
                <h3 class="number text-success">{{ $summary['total_orders'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Average Order Value</p>
                <h3 class="number text-warning">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($summary['avg_order_value'] ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Period</p>
                <h3 class="number text-info">{{ ucfirst(request('period', 'month')) }}</h3>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="chart-container">
        <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-primary"></i>Daily Sales</h5>
        <div style="height: 300px;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Top Products -->
    <div class="chart-container mt-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-crown me-2 text-warning"></i>Top Products</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts ?? [] as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->order_items_count ?? 0 }}</td>
                        <td>{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->order_items_sum_total ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const labels = @json($salesData->pluck('date')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d M');
    }));
    
    const revenue = @json($salesData->pluck('revenue'));
    const orders = @json($salesData->pluck('orders'));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: revenue,
                    backgroundColor: 'rgba(102, 126, 234, 0.7)',
                    borderColor: '#8b5cf6',
                    borderWidth: 2,
                    borderRadius: 8,
                    order: 1
                },
                {
                    label: 'Orders',
                    data: orders,
                    type: 'line',
                    backgroundColor: 'rgba(0, 184, 148, 0.1)',
                    borderColor: '#10b981',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 5,
                    tension: 0.4,
                    order: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#151d2a',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    cornerRadius: 10,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Revenue ($)') {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                            return context.parsed.y + ' orders';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            if (value > 0) return '$' + value;
                            return value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection