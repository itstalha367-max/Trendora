@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<style>
    .wishlist-section { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .wishlist-item {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .wishlist-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .wishlist-item .product-image {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #1a2230;
    }
    .wishlist-item .product-info {
        flex: 1;
        margin-left: 20px;
    }
    .wishlist-item .product-info h5 {
        font-weight: 700;
        margin: 0;
    }
    .wishlist-item .product-info .price {
        font-size: 18px;
        font-weight: 800;
        color: #8b5cf6;
    }
    .wishlist-item .product-info .old-price {
        font-size: 14px;
        color: #93a1b4;
        text-decoration: line-through;
        margin-left: 10px;
    }
    .wishlist-item .product-info .stock {
        font-size: 13px;
        font-weight: 600;
    }
    .wishlist-item .product-info .stock.in-stock { color: #10b981; }
    .wishlist-item .product-info .stock.out-of-stock { color: #ef4444; }
    .wishlist-empty {
        text-align: center;
        padding: 60px 20px;
        background: #111722;
        border-radius: 20px;
        border: 2px dashed #273142;
    }
    .wishlist-empty i {
        font-size: 64px;
        color: #93a1b4;
        opacity: 0.3;
        margin-bottom: 20px;
    }
    .wishlist-empty h3 {
        font-weight: 700;
        color: #151d2a;
    }
    .wishlist-empty p {
        color: #93a1b4;
    }
    .btn-heart {
        background: none;
        border: none;
        font-size: 24px;
        color: #ef4444;
        transition: all 0.3s;
        cursor: pointer;
    }
    .btn-heart:hover {
        transform: scale(1.2);
    }
    .btn-cart {
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
</style>

<div class="wishlist-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-heart me-2 text-danger"></i>My Wishlist</h2>
            <p class="text-muted mb-0">Your favorite products</p>
        </div>
        <span class="badge bg-danger rounded-pill px-4 py-2 fs-6">
            {{ $wishlist->count() }} Items
        </span>
    </div>

    @if($wishlist->count() > 0)
        <div class="row g-3">
            @foreach($wishlist as $item)
            <div class="col-12">
                <div class="wishlist-item">
                    <div class="d-flex align-items-center">
                        @if($item->product->thumbnail)
                            <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="product-image">
                        @elseif($item->product->images && count($item->product->images) > 0)
                            <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $item->product->name }}" class="product-image">
                        @else
                            <div class="product-image" style="background: #0f141e; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #93a1b4;">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        
                        <div class="product-info">
                            <h5>{{ $item->product->name }}</h5>
                            <div>
                                <span class="price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->product->price, 2) }}</span>
                                @if($item->product->compare_price)
                                    <span class="old-price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->product->compare_price, 2) }}</span>
                                @endif
                            </div>
                            @if($item->variation)
                                <small class="text-muted">Variation: {{ $item->variation->attribute_name }} - {{ $item->variation->attribute_value }}</small>
                            @endif
                            <div>
                                <span class="stock {{ $item->product->stock_quantity > 0 ? 'in-stock' : 'out-of-stock' }}">
                                    <i class="fas {{ $item->product->stock_quantity > 0 ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $item->product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 align-items-center">
                        @if($item->product->stock_quantity > 0)
                            <button class="btn-cart" onclick="addToCart({{ $item->product->id }})">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        @endif
                        <button class="btn-heart" onclick="removeFromWishlist({{ $item->id }})" title="Remove from wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="wishlist-empty">
            <i class="fas fa-heart"></i>
            <h3>Your wishlist is empty</h3>
            <p>Start adding your favorite products to your wishlist</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-5 py-3 mt-3">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    @endif
</div>

<script>
function removeFromWishlist(id) {
    if (!confirm('Remove this item from wishlist?')) return;
    
    fetch('{{ url("/wishlist/remove") }}/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function addToCart(productId) {
    fetch('{{ url("/cart/add") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Added to cart!');
        }
    });
}
</script>
@endsection