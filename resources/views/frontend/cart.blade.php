@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<style>
    .cart-section { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .cart-item {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        margin-bottom: 15px;
    }
    .cart-item:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .cart-item .product-image {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #1a2230;
    }
    .cart-item .product-info {
        flex: 1;
        margin-left: 20px;
    }
    .cart-item .product-info h5 {
        font-weight: 700;
        margin: 0;
    }
    .cart-item .product-info .price {
        font-size: 18px;
        font-weight: 800;
        color: #8b5cf6;
    }
    .cart-item .product-info .variation {
        color: #93a1b4;
        font-size: 13px;
    }
    .cart-item .qty-control {
        display: flex;
        align-items: center;
        border: 2px solid #273142;
        border-radius: 10px;
        overflow: hidden;
    }
    .cart-item .qty-control button {
        padding: 6px 15px;
        border: none;
        background: transparent;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }
    .cart-item .qty-control button:hover {
        background: #0f141e;
    }
    .cart-item .qty-control input {
        width: 50px;
        text-align: center;
        border: none;
        font-size: 16px;
        font-weight: 700;
        padding: 6px 0;
        outline: none;
    }
    .cart-item .item-total {
        font-size: 18px;
        font-weight: 800;
        color: #151d2a;
    }
    .cart-item .btn-remove {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 20px;
        transition: all 0.3s;
        cursor: pointer;
    }
    .cart-item .btn-remove:hover {
        transform: scale(1.2);
    }
    .cart-summary {
        background: #111722;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.04);
        position: sticky;
        top: 20px;
    }
    .cart-summary .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }
    .cart-summary .summary-row.total {
        border-top: 2px solid #1a2230;
        padding-top: 15px;
        margin-top: 10px;
        font-size: 20px;
        font-weight: 800;
        color: #8b5cf6;
    }
    .cart-summary .summary-row.discount {
        color: #ef4444;
    }
    .btn-checkout {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 18px;
        transition: all 0.3s;
    }
    .btn-checkout:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
    .empty-cart {
        text-align: center;
        padding: 80px 20px;
        background: #111722;
        border-radius: 20px;
        border: 2px dashed #273142;
    }
    .empty-cart i {
        font-size: 64px;
        color: #93a1b4;
        opacity: 0.3;
    }
    .empty-cart h3 {
        font-weight: 700;
        color: #151d2a;
    }
    /* 🎫 Coupon Section */
    .coupon-section .input-group {
        border-radius: 12px;
        overflow: hidden;
    }
    .coupon-section .input-group .form-control {
        border: 2px solid #273142;
        border-right: none;
        padding: 12px 15px;
        border-radius: 12px 0 0 12px;
    }
    .coupon-section .input-group .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: none;
    }
    .coupon-section .input-group .btn-apply {
        border-radius: 0 12px 12px 0;
        padding: 12px 25px;
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    .coupon-section .input-group .btn-apply:hover {
        transform: scale(1.02);
    }
    .coupon-applied {
        background: #10271f;
        border-radius: 12px;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        animation: fadeIn 0.3s ease-out;
    }
    .coupon-applied .btn-remove-coupon {
        background: none;
        border: none;
        color: #ef4444;
        font-weight: 700;
        cursor: pointer;
    }
    .coupon-applied .btn-remove-coupon:hover {
        text-decoration: underline;
    }
</style>

<div class="cart-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Shopping Cart</h2>
            <p class="text-muted mb-0">Review your items</p>
        </div>
        @if($cart && $cart->items_count > 0)
            <form method="POST" action="{{ route('cart.clear') }}" class="d-inline" onsubmit="return confirm('Clear all items?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger rounded-3">
                <i class="fas fa-trash me-2"></i>Clear Cart
            </button></form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($cart && $cart->items_count > 0)
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-md-8">
                @foreach($cart->items as $item)
                <div class="cart-item" id="cart-item-{{ $item->id }}">
                    <div class="row align-items-center">
                        <div class="col-2">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->product->name }}" class="product-image">
                            @else
                                <div class="product-image" style="background: #0f141e; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #93a1b4;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-4">
                            <h5>{{ $item->product->name }}</h5>
                            @if($item->variation)
                                <span class="variation">{{ $item->variation->attribute_name }}: {{ $item->variation->attribute_value }}</span>
                            @endif
                            <div>
                                <span class="price">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->price, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="qty-control">
                                <button onclick="updateQuantity({{ $item->id }}, -1)">−</button>
                                <input type="number" id="qty-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="99">
                                <button onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                            </div>
                        </div>
                        <div class="col-2 text-center">
                            <span class="item-total" id="item-total-{{ $item->id }}">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->total, 2) }}</span>
                        </div>
                        <div class="col-1 text-end">
                            <button class="btn-remove" onclick="removeItem({{ $item->id }})">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Cart Summary -->
            <div class="col-md-4">
                <div class="cart-summary">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    
                    <!-- 🎫 Coupon Section - YAHAN SHOW HO GA -->
                    <div class="coupon-section mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="couponInput" placeholder="Enter coupon code">
                            <button class="btn-apply" onclick="applyCoupon()">
                                <i class="fas fa-ticket-alt me-2"></i>Apply
                            </button>
                        </div>
                        <div id="couponMessage" class="mt-2" style="display: none;"></div>
                        <div id="couponApplied" class="mt-2" style="display: {{ session('coupon_code') ? 'block' : 'none' }};">
                            <div class="coupon-applied">
                                <span>
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Coupon applied: <strong>{{ session('coupon_code') }}</strong>
                                    (-$<span id="couponDiscount">{{ number_format(session('coupon_discount', 0), 2) }}</span>)
                                </span>
                                <button class="btn-remove-coupon" onclick="removeCoupon()">✕</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    
                    <!-- 🔥 Discount Row -->
                    <div class="summary-row discount" id="discountRow" style="{{ session('coupon_discount') ? 'display: flex;' : 'display: none;' }}">
                        <span>Discount</span>
                        <span>-$<span id="discountAmount">{{ number_format(session('coupon_discount', 0), 2) }}</span></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>{{ App\Models\Setting::get('currency_symbol','Rs') }} 0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="cart-total">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($cart->total, 2) }}</span>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" class="btn-checkout mt-3">
                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                    </a>
                    <div class="mt-3 text-center">
                        <a href="{{ route('products.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Looks like you haven't added anything to your cart yet</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-5 py-3 mt-3">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    @endif
</div>

<script>
// 🎫 Apply Coupon
function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    const messageDiv = document.getElementById('couponMessage');
    const appliedDiv = document.getElementById('couponApplied');
    
    if (!code) {
        messageDiv.style.display = 'block';
        messageDiv.innerHTML = '<div class="alert alert-danger border-0 rounded-4">Please enter a coupon code</div>';
        return;
    }
    
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<div class="alert alert-info border-0 rounded-4"><i class="fas fa-spinner fa-spin me-2"></i>Applying coupon...</div>';
    
    fetch('{{ url("/cart/apply-coupon") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ coupon_code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.style.display = 'none';
            appliedDiv.style.display = 'block';
            document.getElementById('couponDiscount').textContent = data.discount;
            document.getElementById('subtotal').textContent = '$' + data.subtotal;
            document.getElementById('cart-total').textContent = '$' + data.total;
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountAmount').textContent = data.discount;
            document.getElementById('couponInput').value = '';
        } else {
            messageDiv.style.display = 'block';
            messageDiv.innerHTML = '<div class="alert alert-danger border-0 rounded-4">' + data.message + '</div>';
        }
    })
    .catch(error => {
        messageDiv.style.display = 'block';
        messageDiv.innerHTML = '<div class="alert alert-danger border-0 rounded-4">Error applying coupon</div>';
    });
}

// 🎫 Remove Coupon
function removeCoupon() {
    fetch('{{ url("/cart/remove-coupon") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('couponApplied').style.display = 'none';
            document.getElementById('discountRow').style.display = 'none';
            document.getElementById('subtotal').textContent = '$' + data.subtotal;
            document.getElementById('cart-total').textContent = '$' + data.total;
            document.getElementById('couponInput').value = '';
        }
    });
}

// Enter key for coupon
document.getElementById('couponInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyCoupon();
    }
});

// 🛒 Update Quantity
function updateQuantity(itemId, change) {
    const input = document.getElementById('qty-' + itemId);
    let qty = parseInt(input.value) + change;
    if (qty < 1) qty = 1;
    input.value = qty;
    
    fetch('{{ url("/cart/update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: qty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('item-total-' + itemId).textContent = '$' + data.item_total;
            document.getElementById('subtotal').textContent = '$' + data.subtotal;
            document.getElementById('cart-total').textContent = '$' + data.total;
            updateCartCount(data.count);
        } else {
            alert(data.message);
            input.value = qty - change;
        }
    });
}

// 🗑️ Remove Item
function removeItem(itemId) {
    if (!confirm('Remove this item?')) return;
    
    fetch('{{ url("/cart/remove") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ item_id: itemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-item-' + itemId).remove();
            document.getElementById('subtotal').textContent = '$' + data.subtotal;
            document.getElementById('cart-total').textContent = '$' + data.total;
            updateCartCount(data.count);
            
            if (data.count === 0) {
                location.reload();
            }
        }
    });
}

// 🔄 Update Cart Count
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
</script>
@endsection