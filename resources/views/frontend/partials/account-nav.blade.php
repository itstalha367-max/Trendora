<aside class="tr-account-nav tr-card tr-reveal">
    <div class="tr-account-user">
        <div class="tr-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div>
    </div>
    <nav>
        <a class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="fa-solid fa-grid-2"></i>Overview</a>
        <a class="{{ request()->routeIs('user.orders','user.order.*') ? 'active' : '' }}" href="{{ route('user.orders') }}"><i class="fa-solid fa-box"></i>Orders</a>
        <a class="{{ request()->routeIs('user.addresses*') ? 'active' : '' }}" href="{{ route('user.addresses') }}"><i class="fa-solid fa-location-dot"></i>Addresses</a>
        <a class="{{ request()->routeIs('user.returns*') ? 'active' : '' }}" href="{{ route('user.returns') }}"><i class="fa-solid fa-arrow-rotate-left"></i>Returns & refunds</a>
        <a class="{{ request()->routeIs('user.wishlist') ? 'active' : '' }}" href="{{ route('user.wishlist') }}"><i class="fa-regular fa-heart"></i>Wishlist</a>
        <a class="{{ request()->routeIs('user.reviews') ? 'active' : '' }}" href="{{ route('user.reviews') }}"><i class="fa-regular fa-star"></i>My reviews</a>
        <a class="{{ request()->routeIs('user.rewards') ? 'active' : '' }}" href="{{ route('user.rewards') }}"><i class="fa-solid fa-gift"></i>Rewards</a>
        <a class="{{ request()->routeIs('user.notifications') ? 'active' : '' }}" href="{{ route('user.notifications') }}"><i class="fa-regular fa-bell"></i>Notifications</a>
        <a class="{{ request()->routeIs('support.*') ? 'active' : '' }}" href="{{ route('support.index') }}"><i class="fa-regular fa-circle-question"></i>Support</a>
        <a class="{{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}"><i class="fa-regular fa-user"></i>Profile & security</a>
    </nav>
</aside>
