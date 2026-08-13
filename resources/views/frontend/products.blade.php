@extends('layouts.app')

@php $catalogTitle=request()->routeIs('search.results') ? 'Search Results' : (request()->routeIs('categories.show') ? (($categories->firstWhere('slug',request('category'))?->name ?? 'Category').' Products') : 'Shop All Products'); @endphp
@section('title', $catalogTitle.' — Trendora')

@section('content')
<style>
    .products-header {
        animation: slideDown 0.5s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .filter-sidebar {
        background: #111722;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(0,0,0,0.04);
        position: sticky;
        top: 20px;
        animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .filter-sidebar .filter-title {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #1a2230;
    }
    
    .filter-sidebar .filter-group {
        margin-bottom: 20px;
    }
    
    .filter-sidebar .filter-group label {
        font-weight: 600;
        font-size: 14px;
        color: #151d2a;
        display: block;
        margin-bottom: 8px;
    }
    
    .filter-sidebar .form-control,
    .filter-sidebar .form-select {
        border-radius: 10px;
        border: 2px solid #273142;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .filter-sidebar .form-control:focus,
    .filter-sidebar .form-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .filter-sidebar .btn-filter {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s;
    }
    
    .filter-sidebar .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .filter-sidebar .btn-reset {
        width: 100%;
        padding: 10px;
        background: transparent;
        color: #7f8da0;
        border: 2px solid #273142;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 10px;
    }
    
    .filter-sidebar .btn-reset:hover {
        border-color: #ef4444;
        color: #ef4444;
    }
    
    .product-grid-card {
        background: #111722;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
        position: relative;
    }
    
    .product-grid-card:nth-child(1) { animation-delay: 0.05s; }
    .product-grid-card:nth-child(2) { animation-delay: 0.1s; }
    .product-grid-card:nth-child(3) { animation-delay: 0.15s; }
    .product-grid-card:nth-child(4) { animation-delay: 0.2s; }
    .product-grid-card:nth-child(5) { animation-delay: 0.25s; }
    .product-grid-card:nth-child(6) { animation-delay: 0.3s; }
    .product-grid-card:nth-child(7) { animation-delay: 0.35s; }
    .product-grid-card:nth-child(8) { animation-delay: 0.4s; }
    .product-grid-card:nth-child(9) { animation-delay: 0.45s; }
    .product-grid-card:nth-child(10) { animation-delay: 0.5s; }
    .product-grid-card:nth-child(11) { animation-delay: 0.55s; }
    .product-grid-card:nth-child(12) { animation-delay: 0.6s; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .product-grid-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    }
    
    .product-grid-card .image-wrap {
        height: 220px;
        overflow: hidden;
        background: #0f141e;
        position: relative;
    }
    
    .product-grid-card .image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    
    .product-grid-card:hover .image-wrap img {
        transform: scale(1.05);
    }
    
    .product-grid-card .badge-sale {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ef4444, #ef4444);
        color: #fff;
        padding: 5px 18px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
    
    .product-grid-card .wishlist-btn {
        position: absolute;
        top: 15px;
        right: 70px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #ef4444;
        transition: all 0.3s;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .product-grid-card .wishlist-btn:hover {
        transform: scale(1.1);
        background: #ef4444;
        color: #fff;
    }
    
    .product-grid-card .info {
        padding: 20px;
    }
    
    .product-grid-card .info h5 {
        font-weight: 700;
        margin: 0;
        font-size: 16px;
    }
    
    .product-grid-card .info .category-name {
        color: #93a1b4;
        font-size: 13px;
        margin: 0;
    }
    
    .product-grid-card .info .price {
        font-size: 20px;
        font-weight: 800;
        color: #8b5cf6;
        margin: 0;
    }
    
    .product-grid-card .info .old-price {
        font-size: 14px;
        color: #93a1b4;
        text-decoration: line-through;
        margin-left: 10px;
    }
    
    .product-grid-card .btn-add-cart {
        width: 100%;
        padding: 10px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 10px;
    }
    
    .product-grid-card .btn-add-cart:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .product-grid-card .btn-view {
        width: 100%;
        padding: 10px;
        border-radius: 12px;
        border: 2px solid #273142;
        background: transparent;
        color: #151d2a;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 5px;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .product-grid-card .btn-view:hover {
        border-color: #8b5cf6;
        color: #8b5cf6;
    }
    
    .empty-products {
        text-align: center;
        padding: 60px 20px;
        background: #111722;
        border-radius: 20px;
        border: 2px dashed #273142;
    }
    
    .empty-products i {
        font-size: 64px;
        color: #93a1b4;
        opacity: 0.3;
        margin-bottom: 20px;
    }
    
    .empty-products h4 {
        font-weight: 700;
        color: #151d2a;
    }
    
    .empty-products p {
        color: #93a1b4;
    }
    
    .price-input-group {
        display: flex;
        gap: 10px;
    }
    
    .price-input-group .form-control {
        width: 50%;
    }
    
    @media (max-width: 768px) {
        .filter-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 20px;
        }
        .product-grid-card .image-wrap {
            height: 150px;
        }
    }
</style>

<section class="tr-catalog-page">
<div class="tr-shell">
<div class="products-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-box-open me-2 text-primary"></i>{{ $catalogTitle }}</h2>@if(request()->routeIs('search.results') && request('search'))<p class="text-muted mb-0 mt-1">Results for “{{ request('search') }}”</p>@endif
            <p class="text-muted mb-0">Discover our amazing collection</p>
        </div>
        <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">
            {{ $products->total() }} Products
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- 🔍 Filter Sidebar -->
    <div class="col-lg-3 col-12">
        <button class="catalog-filter-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#catalogFilters" aria-expanded="false" aria-controls="catalogFilters">
            <span><i class="fas fa-sliders me-2"></i>Filters & sorting</span>
            <i class="fas fa-chevron-down" aria-hidden="true"></i>
        </button>
        <div class="filter-sidebar collapse d-lg-block" id="catalogFilters">
            <div class="filter-title">
                <i class="fas fa-filter me-2 text-primary"></i>Filters
            </div>
            
            <form action="{{ route('products.index') }}" method="GET">
                <!-- Search -->
                <div class="filter-group">
                    <label><i class="fas fa-search me-2"></i>Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Search products..." value="{{ request('search') }}">
                </div>
                
                <!-- Category -->
                <div class="filter-group">
                    <label><i class="fas fa-tag me-2"></i>Category</label>
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-copyright me-2"></i>Brand</label>
                    <select class="form-select" name="brand"><option value="">All Brands</option>@foreach($brands as $brand)<option value="{{ $brand->slug }}" @selected(request('brand')===$brand->slug)>{{ $brand->name }}</option>@endforeach</select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-layer-group me-2"></i>Collection</label>
                    <select class="form-select" name="collection"><option value="">All Collections</option>@foreach($collections as $collection)<option value="{{ $collection->slug }}" @selected(request('collection')===$collection->slug)>{{ $collection->name }}</option>@endforeach</select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-star me-2"></i>Minimum Rating</label>
                    <select class="form-select" name="rating"><option value="">Any rating</option>@foreach([4,3,2,1] as $rating)<option value="{{ $rating }}" @selected((string)request('rating')===(string)$rating)>{{ $rating }}★ & up</option>@endforeach</select>
                </div>
                <div class="filter-group">
                    <label class="form-check mb-2"><input class="form-check-input me-2" type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock'))> In stock only</label>
                    <label class="form-check mb-2"><input class="form-check-input me-2" type="checkbox" name="sale" value="1" @checked(request()->boolean('sale'))> On sale</label>
                    <label class="form-check"><input class="form-check-input me-2" type="checkbox" name="featured" value="1" @checked(request()->boolean('featured'))> Featured</label>
                </div>

                <!-- Price Range -->
                <div class="filter-group">
                    <label><i class="fas fa-dollar-sign me-2"></i>Price Range</label>
                    <div class="price-input-group">
                        <input type="number" class="form-control" name="min_price" placeholder="{{ number_format($priceBounds['min'],0) }}" value="{{ request('min_price') }}">
                        <input type="number" class="form-control" name="max_price" placeholder="{{ number_format($priceBounds['max'],0) }}" value="{{ request('max_price') }}">
                    </div>
                </div>
                
                <!-- Sort -->
                <div class="filter-group">
                    <label><i class="fas fa-sort me-2"></i>Sort By</label>
                    <select class="form-select" name="sort">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                        <option value="popular" @selected(request('sort')==='popular')>Most Popular</option>
                        <option value="rating" @selected(request('sort')==='rating')>Top Rated</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
                
                <a href="{{ route('products.index') }}" class="btn-reset d-block text-center">
                    <i class="fas fa-undo me-2"></i>Reset All
                </a>
            </form>
        </div>
    </div>
    
    <!-- 📦 Products Grid -->
    <div class="col-lg-9 col-12">
        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-xl-4 col-sm-6 col-12">
                    <div class="product-grid-card">
                        <div class="image-wrap">
                            <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('images/no-image.png') }}" alt="{{ $product->name }}">
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="badge-sale">🔥 SALE</span>
                            @endif
                            <button class="wishlist-btn" type="button" aria-label="Toggle {{ $product->name }} wishlist" onclick="toggleWishlist({{ $product->id }})">
                                <i class="far fa-heart" id="wishlist-icon-{{ $product->id }}"></i>
                            </button>
                        </div>
                        <div class="info">
                            <p class="category-name">{{ $product->brand?->name ? $product->brand->name.' · ' : '' }}{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <div class="small text-warning mb-1"><i class="fas fa-star"></i> {{ number_format((float)($product->rating_avg ?? 0),1) }} <span class="text-muted">· {{ number_format($product->views ?? 0) }} views</span></div>
                            <h5>{{ Str::limit($product->name, 25) }}</h5>
                            <div>
                                <span class="price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->price, 2) }}</span>
                                @if($product->compare_price && $product->compare_price > $product->price)
                                    <span class="old-price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->compare_price, 2) }}</span>
                                @endif
                            </div>
                            <button class="btn-add-cart" type="button" onclick="addToCart(event, {{ $product->id }})">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn-view">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                            <form method="POST" action="{{ route('compare.add', $product) }}" class="mt-2">@csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100 rounded-pill"><i class="fas fa-scale-balanced me-2"></i>Compare</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="empty-products">
                <i class="fas fa-box-open"></i>
                <h4>No Products Found</h4>
                <p>Try adjusting your filters or search terms</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-undo me-2"></i>Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>
</div>
</section>

<script>
// ❤️ Toggle Wishlist
function toggleWishlist(productId) {
    @if(Auth::check())
        fetch('{{ url("/wishlist/toggle") }}/' + productId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const icon = document.getElementById('wishlist-icon-' + productId);
            if (data.in_wishlist) {
                icon.className = 'fas fa-heart';
                icon.style.color = '#ef4444';
            } else {
                icon.className = 'far fa-heart';
                icon.style.color = '';
            }
        });
    @else
        alert('Please login to add to wishlist');
    @endif
}
// 🛒 Add to Cart - Products Page
function addToCart(event, productId) {
    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
    btn.disabled = true;
    
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
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            alert('✅ ' + data.message);
            updateCartCount(data.count);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('❌ Error adding to cart');
    });
}

// Check wishlist status on load
@if(Auth::check())
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        const productId = btn.onclick.toString().match(/\d+/)?.[0];
        if (productId) {
            fetch('{{ url("/wishlist/check") }}/' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.in_wishlist) {
                        const icon = document.getElementById('wishlist-icon-' + productId);
                        if (icon) {
                            icon.className = 'fas fa-heart';
                            icon.style.color = '#ef4444';
                        }
                    }
                });
        }
    });
@endif
</script>
@endsection