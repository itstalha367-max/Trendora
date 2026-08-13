@php
    $currencySymbol = App\Models\Setting::get('currency_symbol', 'Rs');
    $onSale = $product->compare_price && (float) $product->compare_price > (float) $product->price;
@endphp
<article class="tr-product-tile tr-reveal">
    <a class="tr-product-media" href="{{ route('products.show', $product->slug) }}" aria-label="View {{ $product->name }}">
        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
        <span class="tr-product-sheen" aria-hidden="true"></span>
        <div class="tr-product-badges">
            @if($onSale)<span class="tr-chip danger">Sale</span>@endif
            @if($product->featured)<span class="tr-chip">Featured</span>@endif
        </div>
    </a>
    <div class="tr-product-body">
        <div class="tr-product-meta">
            <span>{{ $product->brand?->name ?? $product->category?->name ?? 'Trendora' }}</span>
            <span class="{{ $product->stock_quantity > 0 ? 'is-stock' : 'is-out' }}">{{ $product->stock_quantity > 0 ? 'In stock' : 'Sold out' }}</span>
        </div>
        <a class="tr-product-title" href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        <div class="tr-product-price">
            <strong>{{ $currencySymbol }} {{ number_format($product->price, 2) }}</strong>
            @if($onSale)<del>{{ $currencySymbol }} {{ number_format($product->compare_price, 2) }}</del>@endif
        </div>
        <div class="tr-product-actions">
            @auth
                <button type="button" class="btn btn-primary flex-grow-1" data-cart-add data-product-id="{{ $product->id }}" data-url="{{ route('cart.add') }}" @disabled($product->stock_quantity <= 0)>
                    <i class="fa-solid fa-bag-shopping me-2"></i>{{ $product->stock_quantity > 0 ? 'Add to cart' : 'Sold out' }}
                </button>
                <button type="button" class="tr-square-btn" data-wishlist-toggle data-product-id="{{ $product->id }}" data-url="{{ route('wishlist.toggle', $product->id) }}" aria-label="Toggle {{ $product->name }} in wishlist" title="Wishlist"><i class="fa-regular fa-heart"></i></button>
            @else
                <a class="btn btn-primary flex-grow-1" href="{{ route('login') }}"><i class="fa-solid fa-bag-shopping me-2"></i>Sign in to buy</a>
                <a class="tr-square-btn" href="{{ route('login') }}" aria-label="Sign in to save {{ $product->name }}"><i class="fa-regular fa-heart"></i></a>
            @endauth
            <form method="POST" action="{{ route('compare.add', $product) }}">@csrf<button type="submit" class="tr-square-btn" aria-label="Compare {{ $product->name }}" title="Compare"><i class="fa-solid fa-scale-balanced"></i></button></form>
        </div>
    </div>
</article>
