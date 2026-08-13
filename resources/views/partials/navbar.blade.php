@php
    $wishlistCount = 0;
    $cartCount = 0;
    if (auth()->check()) {
        try { $wishlistCount = App\Models\Wishlist::where('user_id', auth()->id())->count(); } catch (\Throwable $e) {}
        try {
            $cart = App\Models\Cart::where('user_id', auth()->id())->with('items')->first();
            $cartCount = $cart ? $cart->items->sum('quantity') : 0;
        } catch (\Throwable $e) {}
    }
    try { $headerMenuItems = App\Models\NavigationMenu::where('location','header')->where('status',true)->first()?->items()->where('status',true)->get() ?? collect(); } catch (\Throwable $e) { $headerMenuItems = collect(); }
@endphp
<div class="tr-topline" aria-hidden="true">
    <div class="tr-shell">
        <span><i class="tr-pulse"></i> Secure shopping • Easy checkout • Order tracking</span>
        <span>Trendora Pro Commerce Experience</span>
    </div>
</div>
<header class="tr-navbar">
    <nav class="navbar navbar-expand-lg navbar-dark" aria-label="Primary navigation">
        <div class="tr-shell d-flex align-items-center w-100">
            <a class="tr-brand me-4" href="{{ route('home') }}" aria-label="Trendora home">
                <span class="tr-logo"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span>
                <span>Trend<em>ora</em></span>
            </a>
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#trendoraNav" aria-controls="trendoraNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="trendoraNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-1 me-lg-3">
                    @if($headerMenuItems->isNotEmpty())
                        @foreach($headerMenuItems as $menuItem)<li class="nav-item"><a class="nav-link tr-navlink" href="{{ $menuItem->url }}" target="{{ $menuItem->target }}" @if($menuItem->target==='_blank') rel="noopener noreferrer" @endif>{{ $menuItem->label }}</a></li>@endforeach
                    @else
                        <li class="nav-item"><a class="nav-link tr-navlink {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link tr-navlink {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Shop</a></li>
                        <li class="nav-item"><a class="nav-link tr-navlink {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Categories</a></li>
                        <li class="nav-item"><a class="nav-link tr-navlink {{ request()->routeIs('blogs.*') ? 'active' : '' }}" href="{{ route('blogs.index') }}">Journal</a></li>
                    @endif
                </ul>
                <form class="tr-search me-lg-auto" action="{{ route('products.index') }}" method="GET" role="search" data-global-search>
                    <i class="fa-solid fa-search" aria-hidden="true"></i>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products, styles, categories…" aria-label="Search products" autocomplete="off">
                    <span class="tr-kbd" aria-hidden="true">⌘K</span>
                </form>
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0 ms-lg-3">
                    <a class="tr-action" href="{{ route('compare.index') }}" aria-label="Compare products" title="Compare products"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>@if(count(session('compare_products', [])))<span class="tr-badge">{{ min(4,count(session('compare_products', []))) }}</span>@endif</a>
                    @auth
                        <a class="tr-action tr-wishlist-action" href="{{ route('user.wishlist') }}" aria-label="Wishlist" title="Wishlist"><i class="fa-regular fa-heart" aria-hidden="true"></i>@if($wishlistCount)<span class="tr-badge">{{ $wishlistCount > 99 ? '99+' : $wishlistCount }}</span>@endif</a>
                        <a class="tr-action tr-cart-action" href="{{ route('cart.index') }}" aria-label="Cart" title="Cart"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>@if($cartCount)<span class="tr-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif</a>
                        @if(auth()->user()->role === 'admin')
                            <a class="tr-action" href="{{ route('admin.dashboard') }}" aria-label="Admin panel" title="Admin panel"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></a>
                        @endif
                        <div class="dropdown">
                            <button class="tr-auth-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-regular fa-user me-1" aria-hidden="true"></i>{{ Str::limit(auth()->user()->name, 14) }}</button>
                            <ul class="dropdown-menu dropdown-menu-end mt-2">
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="fa-solid fa-gauge-high me-2"></i>Account Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.orders') }}"><i class="fa-solid fa-box me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="fa-regular fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.addresses') }}"><i class="fa-solid fa-location-dot me-2"></i>Addresses</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.wallet') }}"><i class="fa-solid fa-wallet me-2"></i>Wallet & Credit</a></li>
                                <li><a class="dropdown-item" href="{{ route('support.index') }}"><i class="fa-regular fa-circle-question me-2"></i>Support</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger" type="submit"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</button></form></li>
                            </ul>
                        </div>
                    @else
                        <a class="tr-auth-btn" href="{{ route('login') }}">Sign in</a>
                        <a class="tr-auth-btn primary" href="{{ route('register') }}">Create account</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
