<nav class="tr-mobile-dock" aria-label="Mobile quick navigation">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fa-solid fa-house" aria-hidden="true"></i><span>Home</span></a>
    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') || request()->routeIs('categories.*') ? 'active' : '' }}"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i><span>Shop</span></a>
    <button type="button" data-mobile-search><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span>Search</span></button>
    @auth
        <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') || request()->routeIs('checkout.*') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i><span>Cart</span></a>
        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.*') ? 'active' : '' }}"><i class="fa-regular fa-user" aria-hidden="true"></i><span>Account</span></a>
    @else
        <a href="{{ route('compare.index') }}" class="{{ request()->routeIs('compare.*') ? 'active' : '' }}"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i><span>Compare</span></a>
        <a href="{{ route('login') }}"><i class="fa-regular fa-user" aria-hidden="true"></i><span>Sign in</span></a>
    @endauth
</nav>
