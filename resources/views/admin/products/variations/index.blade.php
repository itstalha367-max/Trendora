@extends('layouts.admin')

@section('title', 'Product Variations')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-add {
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
    .variation-item {
        transition: all 0.3s;
    }
    .variation-item:hover {
        background: #0f141e;
    }
    .badge-status {
        padding: 4px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>Product Variations</h2>
            <p class="text-muted mb-0">{{ $product->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-secondary rounded-3">
                <i class="fas fa-arrow-left me-2"></i>Back to Product
            </a>
            <a href="{{ route('admin.products.variations.create', $product->id) }}" class="btn-add">
                <i class="fas fa-plus me-2"></i>Add Variation
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">#</th>
                        <th style="padding: 15px 20px;">Attribute</th>
                        <th style="padding: 15px 20px;">Value</th>
                        <th style="padding: 15px 20px;">SKU</th>
                        <th style="padding: 15px 20px;">Price</th>
                        <th style="padding: 15px 20px;">Stock</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Default</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->variations as $index => $variation)
                    <tr class="variation-item">
                        <td style="padding: 15px 20px;">{{ $index + 1 }}</td>
                        <td style="padding: 15px 20px;">
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                {{ $variation->attribute_name }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                @if($variation->image)
                                    <img src="{{ asset('storage/' . $variation->image) }}" alt="{{ $variation->attribute_value }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; margin-right: 10px;">
                                @endif
                                <span class="fw-bold">{{ $variation->attribute_value }}</span>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <code>{{ $variation->sku ?? 'N/A' }}</code>
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($variation->price)
                                <span class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($variation->price, 2) }}</span>
                                @if($variation->compare_price)
                                    <br><small class="text-muted text-decoration-line-through">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($variation->compare_price, 2) }}</small>
                                @endif
                            @else
                                <span class="text-muted">Use product price</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge rounded-pill px-3 py-2 {{ $variation->stock_quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $variation->stock_quantity }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $variation->status ? 'success' : 'danger' }} text-white">
                                {{ $variation->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($variation->is_default)
                                <span class="badge bg-warning text-dark">⭐ Default</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.products.variations.edit', [$product->id, $variation->id]) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.products.variations.toggle', [$product->id, $variation->id]) }}" class="btn btn-sm {{ $variation->status ? 'btn-warning' : 'btn-success' }} rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Toggle Status">
                                    <i class="fas {{ $variation->status ? 'fa-pause' : 'fa-play' }}"></i>
                                </a>
                                <form action="{{ route('admin.products.variations.destroy', [$product->id, $variation->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-layer-group fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No variations added yet</h5>
                            <a href="{{ route('admin.products.variations.create', $product->id) }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Add Variation
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection