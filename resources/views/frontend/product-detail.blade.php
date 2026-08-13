@extends('layouts.app')

@section('title', $product->name)

@section('content')
<style>
    .product-detail {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .product-gallery .main-image {
        width: 100%;
        height: 450px;
        border-radius: 20px;
        object-fit: cover;
        border: 1px solid rgba(0,0,0,0.04);
    }
    
    .product-gallery .thumbnails {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    
    .product-gallery .thumbnails img {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
    }
    
    .product-gallery .thumbnails img:hover,
    .product-gallery .thumbnails img.active {
        border-color: #8b5cf6;
    }
    
    .product-info .product-title {
        font-size: 28px;
        font-weight: 800;
        color: #151d2a;
        margin: 0;
    }
    
    .product-info .product-category {
        color: #93a1b4;
        font-size: 14px;
    }
    
    .product-info .rating {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }
    
    .product-info .rating .stars {
        color: #f59e0b;
        font-size: 18px;
        letter-spacing: 2px;
    }
    
    .product-info .price-box {
        background: #0f141e;
        padding: 20px;
        border-radius: 16px;
        margin: 15px 0;
    }
    
    .product-info .price-box .current-price {
        font-size: 32px;
        font-weight: 800;
        color: #8b5cf6;
    }
    
    .product-info .price-box .old-price {
        font-size: 20px;
        color: #93a1b4;
        text-decoration: line-through;
        margin-left: 15px;
    }
    
    .product-info .price-box .discount {
        background: #ef4444;
        color: #fff;
        padding: 2px 15px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 700;
        margin-left: 15px;
    }
    
    .product-info .variation-group {
        margin: 20px 0;
    }
    
    .product-info .variation-group label {
        font-weight: 700;
        font-size: 14px;
        color: #151d2a;
        display: block;
        margin-bottom: 8px;
    }
    
    .product-info .variation-group .variation-options {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .product-info .variation-group .variation-options .var-btn {
        padding: 8px 20px;
        border-radius: 10px;
        border: 2px solid #273142;
        background: transparent;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .product-info .variation-group .variation-options .var-btn:hover {
        border-color: #8b5cf6;
        color: #8b5cf6;
    }
    
    .product-info .variation-group .variation-options .var-btn.active {
        border-color: #8b5cf6;
        background: #8b5cf6;
        color: #fff;
    }
    
    .product-info .variation-group .variation-options .var-btn.out-of-stock {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    .product-info .quantity-box {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 20px 0;
    }
    
    .product-info .quantity-box label {
        font-weight: 700;
        font-size: 14px;
        color: #151d2a;
    }
    
    .product-info .quantity-box .qty-control {
        display: flex;
        align-items: center;
        border: 2px solid #273142;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .product-info .quantity-box .qty-control button {
        padding: 8px 18px;
        border: none;
        background: transparent;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .product-info .quantity-box .qty-control button:hover {
        background: #0f141e;
    }
    
    .product-info .quantity-box .qty-control input {
        width: 60px;
        text-align: center;
        border: none;
        font-size: 18px;
        font-weight: 700;
        padding: 8px 0;
        outline: none;
    }
    
    .btn-add-to-cart {
        padding: 16px 40px;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 18px;
        transition: all 0.3s;
        width: 100%;
    }
    
    .btn-add-to-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
    
    .btn-add-to-cart:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .btn-wishlist {
        padding: 16px 20px;
        background: #111722;
        border: 2px solid #273142;
        border-radius: 14px;
        font-weight: 700;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }
    
    .btn-wishlist:hover {
        border-color: #ef4444;
        color: #ef4444;
    }
    
    .btn-wishlist.active {
        border-color: #ef4444;
        background: #ef4444;
        color: #fff;
    }
    
    .product-description {
        margin-top: 30px;
        padding: 30px;
        background: #111722;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    
    .product-description h5 {
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .product-description p {
        color: #7f8da0;
        line-height: 1.8;
    }
    
    /* Reviews Section */
    .reviews-section {
        margin-top: 30px;
        padding: 30px;
        background: #111722;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    
    .reviews-section .review-item {
        padding: 15px 0;
        border-bottom: 1px solid #1a2230;
    }
    
    .reviews-section .review-item:last-child {
        border-bottom: none;
    }
    
    .reviews-section .review-item .review-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .reviews-section .review-item .review-user .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
    }
    
    .reviews-section .review-item .review-user .name {
        font-weight: 700;
    }
    
    .reviews-section .review-item .review-user .date {
        color: #93a1b4;
        font-size: 13px;
    }
    
    .reviews-section .review-item .review-stars {
        color: #f59e0b;
        font-size: 16px;
        letter-spacing: 2px;
        margin: 5px 0;
    }
    
    .reviews-section .review-item .review-comment {
        color: #7f8da0;
        margin: 0;
    }
    
    .no-reviews {
        text-align: center;
        padding: 30px;
        color: #93a1b4;
    }
    
    /* Related Products */
    .related-products {
        margin-top: 40px;
    }
    
    .related-products .section-title {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .related-products .section-title h3 {
        font-weight: 800;
        font-size: 28px;
        color: #151d2a;
    }
    
    .related-products .section-title .underline {
        display: block;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #8b5cf6, #5b7cff);
        margin: 10px auto 0;
        border-radius: 2px;
    }
    
    .related-product-card {
        background: #111722;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s;
        border: 1px solid rgba(0,0,0,0.04);
    }
    
    .related-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    
    .related-product-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    
    .related-product-card .info {
        padding: 15px;
    }
    
    .related-product-card .info h6 {
        font-weight: 700;
        margin: 0;
        font-size: 15px;
    }
    
    .related-product-card .info .price {
        font-weight: 800;
        color: #8b5cf6;
        font-size: 18px;
    }
    
    @media (max-width: 768px) {
        .product-gallery .main-image {
            height: 250px;
        }
        .product-info .product-title {
            font-size: 22px;
        }
        .product-info .price-box .current-price {
            font-size: 24px;
        }
        .btn-add-to-cart {
            font-size: 16px;
            padding: 14px 20px;
        }
    }
</style>

<div class="product-detail">
    <div class="row g-4">
        <!-- 📸 Product Gallery -->
        <div class="col-md-6">
            <div class="product-gallery">
                @php
                    $images = $product->images ?? [];
                    $firstImage = $product->thumbnail ?? ($images[0] ?? null);
                @endphp
                
                <img src="{{ $firstImage ? asset('storage/' . $firstImage) : asset('images/no-image.png') }}" 
                     alt="{{ $product->name }}" 
                     class="main-image" 
                     id="mainImage">
                
                @if(count($images) > 1)
                    <div class="thumbnails">
                        @foreach($images as $image)
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="{{ $product->name }}" 
                                 onclick="changeImage(this.src, this)"
                                 class="{{ $loop->first ? 'active' : '' }}">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- 📝 Product Info -->
        <div class="col-md-6">
            <div class="product-info">
                <p class="product-category">
                    <i class="fas fa-tag me-1"></i>
                    {{ $product->category->name ?? 'Uncategorized' }}
                </p>
                
                <h1 class="product-title">{{ $product->name }}</h1>
                
                <div class="rating">
                    <span class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                ⭐
                            @else
                                ☆
                            @endif
                        @endfor
                    </span>
                    <span class="text-muted">
                        ({{ $totalReviews }} reviews)
                    </span>
                </div>
                
                <div class="price-box">
                    <span class="current-price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="old-price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->compare_price, 2) }}</span>
                        @php
                            $discount = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
                        @endphp
                        <span class="discount">-{{ $discount }}%</span>
                    @endif
                </div>
                
                <!-- 📦 Variations -->
                @if($attributeNames->count() > 0)
                    <div class="variation-group">
                        <label>Select Options</label>
                        <div class="variation-options">
                            @foreach($attributeNames as $attrName)
                                @php
                                    $variations = $product->variations()->where('attribute_name', $attrName)->get();
                                @endphp
                                <div style="width: 100%; margin-bottom: 10px;">
                                    <span style="font-weight: 600; font-size: 13px; color: #7f8da0; display: block; margin-bottom: 5px;">
                                        {{ $attrName }}
                                    </span>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($variations as $variation)
                                            <button class="var-btn {{ $loop->first ? 'active' : '' }} 
                                                {{ $variation->stock_quantity <= 0 ? 'out-of-stock' : '' }}"
                                                data-variation-id="{{ $variation->id }}"
                                                data-price="{{ $variation->price ?? $product->price }}"
                                                data-stock="{{ $variation->stock_quantity }}"
                                                onclick="selectVariation(this, {{ $variation->id }})">
                                                {{ $variation->attribute_value }}
                                                @if($variation->stock_quantity <= 0)
                                                    (Out of Stock)
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- 🔢 Quantity -->
                <div class="quantity-box">
                    <label>Quantity</label>
                    <div class="qty-control">
                        <button onclick="updateQuantity(-1)">−</button>
                        <input type="number" id="quantity" value="1" min="1" max="999">
                        <button onclick="updateQuantity(1)">+</button>
                    </div>
                </div>
                
                <!-- 🛒 Add to Cart & Wishlist -->
                <div class="row g-2">
                    <div class="col-7">
                        <button class="btn-add-to-cart" onclick="addToCart()">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                    <div class="col-2">
                        <button class="btn-wishlist" onclick="toggleWishlist({{ $product->id }})" title="Wishlist">
                            <i class="far fa-heart" id="wishlist-icon-detail"></i>
                        </button>
                    </div>
                    <div class="col-3">
                        <form method="POST" action="{{ route('compare.add', $product) }}" class="h-100">@csrf
                            <button class="btn-wishlist w-100 h-100" type="submit" title="Compare"><i class="fas fa-scale-balanced"></i></button>
                        </form>
                    </div>
                </div>
                
                <!-- 📦 Stock Status -->
                <div class="mt-3">
                    @if($product->stock_quantity > 0)
                        <span class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            In Stock ({{ $product->stock_quantity }} available)
                        </span>
                    @else
                        <span class="text-danger">
                            <i class="fas fa-times-circle me-1"></i>
                            Out of Stock
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- 📝 Description -->
    <div class="product-description">
        <h5><i class="fas fa-align-left me-2 text-primary"></i>Product Description</h5>
        <p>{{ $product->description }}</p>
    </div>
    
    <!-- ⭐ Reviews -->
    <div class="reviews-section">
        <h5><i class="fas fa-star me-2 text-warning"></i>Customer Reviews ({{ $totalReviews }})</h5>
        @auth
            @if($canReview)
            <form class="tr-card p-3 mb-4" method="POST" action="{{ route('user.reviews.store', $product) }}">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-3"><label class="form-label">Your rating</label><select class="form-select" name="rating" required>@for($r=5;$r>=1;$r--)<option value="{{ $r }}" {{ (int)($myReview->rating ?? 5)===$r?'selected':'' }}>{{ $r }} / 5</option>@endfor</select></div>
                    <div class="col-md-7"><label class="form-label">Review</label><input class="form-control" name="comment" value="{{ old('comment',$myReview->comment ?? '') }}" placeholder="Share your experience"></div>
                    <div class="col-md-2"><button class="btn btn-primary w-100">{{ $myReview ? 'Update' : 'Submit' }}</button></div>
                </div>
                <small class="text-muted d-block mt-2">Verified purchase · reviews are published after moderation.</small>
            </form>
            @endif
        @endauth
        
        @if($totalReviews > 0)
            @foreach($product->reviews()->where('status', 'approved')->latest()->get() as $review)
                <div class="review-item">
                    <div class="review-user">
                        <div class="avatar">{{ substr($review->user->name ?? 'U', 0, 1) }}</div>
                        <div>
                            <span class="name">{{ $review->user->name ?? 'Anonymous' }}</span>
                            <span class="date">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="review-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                ⭐
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <p class="review-comment">{{ $review->comment }}</p>
                </div>
            @endforeach
        @else
            <div class="no-reviews">
                <i class="fas fa-star fa-2x d-block mb-2" style="opacity: 0.2;"></i>
                <p>No reviews yet. Be the first to review this product!</p>
            </div>
        @endif
    </div>
    
    <!-- Product Q&A -->
    <div class="reviews-section mt-4">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4"><div><h5><i class="fa-regular fa-comments me-2 text-info"></i>Questions & answers</h5><p class="text-muted mb-0">Ask about sizing, compatibility, delivery or product details.</p></div></div>
        <form class="tr-card p-3 mb-4" method="POST" action="{{ route('products.questions.store',$product) }}">@csrf
            <div class="row g-3">@guest<div class="col-md-3"><input class="form-control" name="name" placeholder="Your name"></div><div class="col-md-3"><input class="form-control" type="email" name="email" placeholder="Email"></div>@endguest<div class="{{ auth()->check()?'col-md-10':'col-md-4' }}"><input class="form-control" name="question" minlength="5" placeholder="Ask a question about this product" required></div><div class="col-md-2"><button class="btn btn-primary w-100">Ask</button></div></div>
            <small class="text-muted d-block mt-2">Questions are reviewed before they are published.</small>
        </form>
        @forelse($questions as $q)<div class="review-item"><div class="review-user"><div class="avatar">Q</div><div><div class="name">{{ $q->user?->name ?: $q->name ?: 'Customer' }}</div><div class="date">{{ $q->created_at->format('d M Y') }}</div></div></div><div class="review-comment"><strong>{{ $q->question }}</strong><div class="mt-2 p-3 rounded-3" style="background:rgba(124,92,255,.08)"><small class="text-muted d-block mb-1">Trendora answer</small>{{ $q->answer }}</div></div></div>@empty<div class="no-reviews"><i class="fa-regular fa-comments"></i><p>No published questions yet.</p></div>@endforelse
    </div>

    <!-- 🔗 Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="related-products">
            <div class="section-title">
                <h3>Related Products</h3>
                <span class="underline"></span>
            </div>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-md-3 col-6">
                    <div class="related-product-card">
                        <img src="{{ $related->thumbnail ? asset('storage/' . $related->thumbnail) : asset('images/no-image.png') }}" alt="{{ $related->name }}">
                        <div class="info">
                            <h6>{{ Str::limit($related->name, 25) }}</h6>
                            <span class="price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($related->price, 2) }}</span>
                            <a href="{{ route('products.show', $related->slug) }}" class="btn btn-sm btn-outline-primary w-100 mt-2 rounded-pill">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
// 🖼️ Change Main Image
function changeImage(src, el) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
    el.classList.add('active');
}

// 🔢 Update Quantity
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    let val = parseInt(input.value) + change;
    if (val < 1) val = 1;
    if (val > 999) val = 999;
    input.value = val;
}

// 📦 Select Variation
let selectedVariationId = null;

function selectVariation(btn, variationId) {
    const parent = btn.closest('.variation-options');
    parent.querySelectorAll('.var-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedVariationId = variationId;
}


// 🛒 Add to Cart - FIXED
function addToCart() {
    const productId = {{ $product->id }};
    const quantity = document.getElementById('quantity').value;
    
    let data = {
        product_id: productId,
        quantity: quantity
    };
    
    if (selectedVariationId) {
        data.variation_id = selectedVariationId;
    }
    
    // Show loading state
    const btn = document.querySelector('.btn-add-to-cart');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
    btn.disabled = true;
    
    fetch('{{ url("/cart/add") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            alert('✅ ' + data.message);
            // Update cart count in navbar
            updateCartCount(data.count);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('❌ Error adding to cart. Please try again.');
    });
}

function updateCartCount(count) {
    const badge = document.querySelector('.navbar .badge.bg-danger');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }
}


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
            const icon = document.getElementById('wishlist-icon-detail');
            if (data.in_wishlist) {
                icon.className = 'fas fa-heart';
                document.querySelector('.btn-wishlist').classList.add('active');
            } else {
                icon.className = 'far fa-heart';
                document.querySelector('.btn-wishlist').classList.remove('active');
            }
        });
    @else
        alert('Please login to add to wishlist');
    @endif
}

// ❤️ Check Wishlist Status
@if(Auth::check())
    fetch('{{ url("/wishlist/check") }}/{{ $product->id }}')
        .then(response => response.json())
        .then(data => {
            if (data.in_wishlist) {
                const icon = document.getElementById('wishlist-icon-detail');
                if (icon) {
                    icon.className = 'fas fa-heart';
                    document.querySelector('.btn-wishlist').classList.add('active');
                }
            }
        });
@endif
</script>
@endsection