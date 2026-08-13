@extends('layouts.app')

@section('title', App\Models\Setting::get('store_name','Trendora').' — Modern Commerce')

@section('content')
@php
    $currencySymbol = App\Models\Setting::get('currency_symbol', 'Rs');
    $heroTitle = App\Models\Setting::get('hero_title','Style that moves with you.');
    $heroSubtitle = App\Models\Setting::get('hero_subtitle','Discover curated products, premium experiences and effortless shopping.');
    $heroCta = App\Models\Setting::get('hero_cta','Shop collection');
    $heroProducts = $newProducts->take(3);
@endphp
<section class="tr-home-hero">
    <div class="tr-shell">
        <div class="tr-hero-grid">
            <div class="tr-hero-copy tr-reveal">
                <span class="tr-eyebrow"><i class="fa-solid fa-sparkles"></i> Trendora curated commerce</span>
                <h1>{{ $heroTitle }}</h1>
                <p>{{ $heroSubtitle }}</p>
                <div class="tr-hero-actions">
                    <a class="btn btn-primary btn-lg" href="{{ route('products.index') }}"><i class="fa-solid fa-bag-shopping me-2"></i>{{ $heroCta }}</a>
                    <a class="btn btn-outline-light btn-lg" href="{{ route('categories.index') }}">Browse categories</a>
                </div>
                <div class="tr-trust-row" aria-label="Store benefits">
                    <span><i class="fa-solid fa-shield-halved"></i> Secure checkout</span>
                    <span><i class="fa-solid fa-truck-fast"></i> Configurable delivery</span>
                    <span><i class="fa-solid fa-arrow-rotate-left"></i> Return workflow</span>
                </div>
            </div>
            <div class="tr-hero-stage tr-reveal" aria-label="Latest products">
                <div class="tr-hero-orb tr-hero-orb-one" aria-hidden="true"></div>
                <div class="tr-hero-orb tr-hero-orb-two" aria-hidden="true"></div>
                <div class="tr-hero-console">
                    <div class="tr-console-head"><span><i class="tr-pulse"></i> Live collection</span><small>{{ $newProducts->count() }} fresh picks</small></div>
                    <div class="tr-console-products">
                        @forelse($heroProducts as $product)
                            <a class="tr-console-product" href="{{ route('products.show',$product->slug) }}">
                                <img src="{{ $product->thumbnail_url }}" alt="" loading="eager">
                                <span><strong>{{ Str::limit($product->name,28) }}</strong><small>{{ $currencySymbol }} {{ number_format($product->price,2) }}</small></span>
                                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        @empty
                            <div class="tr-empty-mini"><i class="fa-solid fa-box-open"></i><span>Add your first products from Admin.</span></div>
                        @endforelse
                    </div>
                    <a class="tr-console-link" href="{{ route('products.index',['sort'=>'newest']) }}">Explore newest <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="tr-home-section">
    <div class="tr-shell">
        <div class="tr-home-heading tr-reveal"><div><span class="tr-eyebrow">Shop by category</span><h2>Find your next favorite.</h2><p>Jump straight into the collections your catalog actually contains.</p></div><a href="{{ route('categories.index') }}">View all categories <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="tr-category-grid">
            @forelse($categories as $category)
                <a class="tr-category-tile tr-reveal" href="{{ route('categories.show',$category->slug) }}">
                    <span class="tr-category-icon"><i class="fa-solid fa-layer-group"></i></span>
                    <span><strong>{{ $category->name }}</strong><small>{{ $category->products_count }} {{ Str::plural('product',$category->products_count) }}</small></span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            @empty
                <div class="tr-empty tr-card"><i class="fa-solid fa-layer-group"></i><h4>No categories yet</h4><p>Published categories with active products will appear here.</p></div>
            @endforelse
        </div>
    </div>
</section>

@if($featuredProducts->isNotEmpty())
<section class="tr-home-section">
    <div class="tr-shell">
        <div class="tr-home-heading tr-reveal"><div><span class="tr-eyebrow">Featured selection</span><h2>Picked to stand out.</h2><p>Products your merchandising team has marked as featured.</p></div><a href="{{ route('products.index',['featured'=>1]) }}">Shop featured <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="tr-product-grid">@foreach($featuredProducts as $product) @include('frontend.partials.product-card',['product'=>$product]) @endforeach</div>
    </div>
</section>
@endif

<section class="tr-home-section">
    <div class="tr-shell">
        <div class="tr-home-heading tr-reveal"><div><span class="tr-eyebrow">New arrivals</span><h2>Fresh into Trendora.</h2><p>The latest active products in your store catalog.</p></div><a href="{{ route('products.index',['sort'=>'newest']) }}">View newest <i class="fa-solid fa-arrow-right"></i></a></div>
        @if($newProducts->isNotEmpty())<div class="tr-product-grid">@foreach($newProducts as $product) @include('frontend.partials.product-card',['product'=>$product]) @endforeach</div>@else<div class="tr-empty tr-card"><i class="fa-solid fa-box-open"></i><h4>No products published yet</h4><p>Publish products in Admin and they will appear automatically.</p></div>@endif
    </div>
</section>

<section class="tr-home-section">
    <div class="tr-shell">
        <div class="tr-service-grid">
            <article class="tr-service-card tr-reveal"><span><i class="fa-solid fa-bolt"></i></span><h3>Fast discovery</h3><p>Advanced search, category, brand, collection, stock, rating and price filters are built into the catalog.</p></article>
            <article class="tr-service-card tr-reveal"><span><i class="fa-solid fa-lock"></i></span><h3>Protected checkout</h3><p>Server-calculated shipping, tax, inventory validation, payment ledger and wallet flows keep totals consistent.</p></article>
            <article class="tr-service-card tr-reveal"><span><i class="fa-solid fa-headset"></i></span><h3>After-sales care</h3><p>Order tracking, returns, refunds, notifications and support tickets continue the experience after purchase.</p></article>
        </div>
    </div>
</section>

@if($blogPosts->isNotEmpty())
<section class="tr-home-section">
    <div class="tr-shell">
        <div class="tr-home-heading tr-reveal"><div><span class="tr-eyebrow">Trendora journal</span><h2>Stories behind the storefront.</h2><p>Published articles from your content team.</p></div><a href="{{ route('blogs.index') }}">Open journal <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="tr-journal-grid">
            @foreach($blogPosts as $blog)
                <article class="tr-journal-card tr-reveal">
                    <a class="tr-journal-media" href="{{ route('blogs.show',$blog->slug) }}"><img src="{{ $blog->featured_image ? asset('storage/'.$blog->featured_image) : asset('images/no-image.png') }}" alt="{{ $blog->title }}" loading="lazy"></a>
                    <div class="tr-journal-body"><span class="tr-eyebrow">{{ $blog->published_at?->format('d M Y') ?? $blog->created_at->format('d M Y') }} · {{ $blog->reading_time }}</span><h3><a href="{{ route('blogs.show',$blog->slug) }}">{{ $blog->title }}</a></h3><p>{{ Str::limit(strip_tags($blog->excerpt ?? $blog->content),110) }}</p><a class="tr-inline-link" href="{{ route('blogs.show',$blog->slug) }}">Read article <i class="fa-solid fa-arrow-right"></i></a></div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="tr-home-section tr-home-section-last">
    <div class="tr-shell">
        <div class="tr-newsletter-card tr-reveal">
            <div><span class="tr-eyebrow"><i class="fa-regular fa-envelope"></i> Useful updates only</span><h2>Stay close to new drops.</h2><p>Subscribe for product releases, offers and editorial updates. Your email is stored in the admin Newsletter center.</p></div>
            <form method="POST" action="{{ route('newsletter.subscribe') }}" class="tr-newsletter-form">@csrf<input type="hidden" name="source" value="homepage"><label class="visually-hidden" for="homeNewsletter">Email address</label><input id="homeNewsletter" class="form-control form-control-lg" type="email" name="email" autocomplete="email" placeholder="you@example.com" required><button class="btn btn-primary btn-lg" type="submit">Subscribe <i class="fa-solid fa-arrow-right ms-2"></i></button></form>
        </div>
    </div>
</section>
@endsection
