@extends('layouts.admin')

@section('title', 'Product Report')

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
    .rank-badge {
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        text-align: center;
        line-height: 30px;
        font-weight: 700;
        font-size: 14px;
        color: #fff;
    }
    .rank-badge.gold { background: #f59e0b; }
    .rank-badge.silver { background: #93a1b4; }
    .rank-badge.bronze { background: #f59e0b; }
    .rank-badge.default { background: #cbd5e1; color: #151d2a; }
</style>

<div class="report-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-box me-2 text-success"></i>Product Report</h2>
            <p class="text-muted mb-0">Best selling products performance</p>
        </div>
        <a href="{{ route('admin.reports') }}" class="btn btn-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Total Products</p>
                <h3 class="number text-primary">{{ App\Models\Product::count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Active Products</p>
                <h3 class="number text-success">{{ App\Models\Product::where('status', true)->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Out of Stock</p>
                <h3 class="number text-danger">{{ App\Models\Product::where('stock_quantity', '<=', 0)->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Total Sales</p>
                <h3 class="number text-warning">{{ App\Models\OrderItem::sum('quantity') }}</h3>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div style="overflow-x: auto;">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead style="background: #0f141e;">
                        <tr>
                            <th style="padding: 15px 20px;">#</th>
                            <th style="padding: 15px 20px;">Product</th>
                            <th style="padding: 15px 20px;">Category</th>
                            <th style="padding: 15px 20px;">Price</th>
                            <th style="padding: 15px 20px;">Total Sales</th>
                            <th style="padding: 15px 20px;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products ?? [] as $index => $product)
                        <tr>
                            <td style="padding: 15px 20px;">
                                <span class="rank-badge {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td style="padding: 15px 20px;">
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                            </td>
                            <td style="padding: 15px 20px;">
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                    {{ $product->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 15px 20px;">
                                <span class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->price, 2) }}</span>
                            </td>
                            <td style="padding: 15px 20px;">
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    {{ $product->order_items_count ?? 0 }}
                                </span>
                            </td>
                            <td style="padding: 15px 20px;">
                                <span class="fw-bold text-success">
                                    {{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->order_items_sum_total ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                                <h5 class="text-muted">No products sold yet</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
</div>
@endsection