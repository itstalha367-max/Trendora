@php
    $counts = Illuminate\Support\Facades\Cache::remember('trendora.admin.sidebar-counts', 30, function () {
        $data=['products'=>0,'categories'=>0,'orders'=>0,'users'=>0,'reviews'=>0,'carts'=>0,'returns'=>0,'support'=>0,'contacts'=>0,'inventory'=>0];
        try { $data['products']=App\Models\Product::count(); $data['categories']=App\Models\Category::count(); $data['orders']=App\Models\Order::where('order_status','pending')->count(); $data['users']=App\Models\User::count(); $data['reviews']=App\Models\Review::where('status','pending')->count(); $data['carts']=App\Models\AbandonedCart::where('status','active')->count(); $data['returns']=App\Models\ReturnRequest::where('status','pending')->count(); $data['support']=App\Models\SupportTicket::whereNotIn('status',['resolved','closed'])->count(); $data['contacts']=App\Models\ContactSubmission::where('status','new')->count(); $data['inventory']=App\Models\Inventory::whereColumn('quantity','<=','reorder_level')->count(); } catch (\Throwable $e) {}
        return $data;
    });
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070a12">
    <title>@yield('title', 'Trendora Admin')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/astra-commerce.css') }}">
</head>
<body class="trendora-admin astra-commerce">
<a href="#admin-content" class="ad-skip">Skip to admin content</a>
<div class="ad-shell">
    <aside class="ad-sidebar" id="adminSidebar" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard') }}" class="ad-brand">
            <span class="ad-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
            <span class="ad-brand-copy"><strong>Trendora</strong><small>Commerce OS</small></span>
        </a>
        <div class="ad-nav-search">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <label class="visually-hidden" for="adminNavSearch">Filter admin navigation</label>
            <input id="adminNavSearch" type="search" placeholder="Find a module…" autocomplete="off" data-admin-nav-search>
            <kbd>/</kbd>
        </div>

        <div class="ad-nav-section">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="ad-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i>Dashboard</a>
        <a href="{{ route('admin.operations.live') }}" class="ad-link {{ request()->routeIs('admin.operations.live') ? 'active' : '' }}"><i class="fa-solid fa-satellite-dish"></i>Live Store</a>
        <a href="{{ route('admin.analytics.commerce') }}" class="ad-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i>Commerce Analytics</a>
        <a href="{{ route('admin.reports') }}" class="ad-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i>Classic Reports</a>

        <div class="ad-nav-section">Catalog</div>
        <a href="{{ route('admin.products.index') }}" class="ad-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i>Products <span class="ad-count">{{ $counts['products'] }}</span></a>
        <a href="{{ route('admin.categories.index') }}" class="ad-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group"></i>Categories <span class="ad-count">{{ $counts['categories'] }}</span></a>
        <a href="{{ route('admin.brands.index') }}" class="ad-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><i class="fa-solid fa-copyright"></i>Brands</a>
        <a href="{{ route('admin.collections.index') }}" class="ad-link {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}"><i class="fa-solid fa-sparkles"></i>Collections</a>

        <div class="ad-nav-section">Orders & Service</div>
        <a href="{{ route('admin.draft-orders.index') }}" class="ad-link {{ request()->routeIs('admin.draft-orders.*') ? 'active' : '' }}"><i class="fa-regular fa-file-lines"></i>Draft Orders</a>
        <a href="{{ route('admin.orders.index') }}" class="ad-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping"></i>Orders <span class="ad-count">{{ $counts['orders'] }}</span></a>
        <a href="{{ route('admin.returns.index') }}" class="ad-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}"><i class="fa-solid fa-arrow-rotate-left"></i>Returns <span class="ad-count">{{ $counts['returns'] }}</span></a>
        <a href="{{ route('admin.abandoned-carts.index') }}" class="ad-link {{ request()->routeIs('admin.abandoned-carts*') ? 'active' : '' }}"><i class="fa-solid fa-cart-arrow-down"></i>Abandoned Carts <span class="ad-count">{{ $counts['carts'] }}</span></a>
        <a href="{{ route('admin.support.index') }}" class="ad-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}"><i class="fa-solid fa-headset"></i>Support <span class="ad-count">{{ $counts['support'] }}</span></a>
        <a href="{{ route('admin.contacts.index') }}" class="ad-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}"><i class="fa-regular fa-envelope"></i>Contact Inbox <span class="ad-count">{{ $counts['contacts'] }}</span></a>

        <div class="ad-nav-section">Inventory & Supply</div>
        <a href="{{ route('admin.inventory.index') }}" class="ad-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"><i class="fa-solid fa-boxes-stacked"></i>Inventory <span class="ad-count">{{ $counts['inventory'] }}</span></a>
        <a href="{{ route('admin.warehouses.index') }}" class="ad-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}"><i class="fa-solid fa-warehouse"></i>Warehouses</a>
        <a href="{{ route('admin.purchase-orders.index') }}" class="ad-link {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}"><i class="fa-solid fa-file-circle-plus"></i>Purchase Orders</a>
        <a href="{{ route('admin.suppliers.index') }}" class="ad-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}"><i class="fa-solid fa-truck-ramp-box"></i>Suppliers</a>

        <div class="ad-nav-section">Customers</div>
        <a href="{{ route('admin.users.index') }}" class="ad-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i>Customers <span class="ad-count">{{ $counts['users'] }}</span></a>
        <a href="{{ route('admin.segments.index') }}" class="ad-link {{ request()->routeIs('admin.segments.*') ? 'active' : '' }}"><i class="fa-solid fa-users-viewfinder"></i>Segments</a>
        <a href="{{ route('admin.reviews.index') }}" class="ad-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i class="fa-solid fa-star"></i>Reviews <span class="ad-count">{{ $counts['reviews'] }}</span></a>
        <a href="{{ route('admin.questions.index') }}" class="ad-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}"><i class="fa-regular fa-comments"></i>Product Questions</a>
        <a href="{{ route('admin.wishlist.index') }}" class="ad-link {{ request()->routeIs('admin.wishlist*') ? 'active' : '' }}"><i class="fa-solid fa-heart"></i>Wishlists</a>

        <div class="ad-nav-section">Marketing & Content</div>
        <a href="{{ route('admin.promotions.index') }}" class="ad-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}"><i class="fa-solid fa-tags"></i>Promotions</a>
        <a href="{{ route('admin.campaigns.index') }}" class="ad-link {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}"><i class="fa-solid fa-bullhorn"></i>Campaigns</a>
        <a href="{{ route('admin.newsletter.index') }}" class="ad-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}"><i class="fa-solid fa-envelopes-bulk"></i>Newsletter</a>
        <a href="{{ route('admin.banners.index') }}" class="ad-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}"><i class="fa-regular fa-images"></i>Banners</a>
        <a href="{{ route('admin.gift-cards.index') }}" class="ad-link {{ request()->routeIs('admin.gift-cards.*') ? 'active' : '' }}"><i class="fa-solid fa-gift"></i>Gift Cards</a>
        <a href="{{ route('admin.affiliates.index') }}" class="ad-link {{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}"><i class="fa-solid fa-handshake"></i>Affiliates</a>
        <a href="{{ route('admin.coupons.index') }}" class="ad-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"><i class="fa-solid fa-ticket"></i>Coupons</a>
        <a href="{{ route('admin.blogs.index') }}" class="ad-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}"><i class="fa-solid fa-pen-nib"></i>Blog</a>
        <a href="{{ route('admin.seo.index') }}" class="ad-link {{ request()->routeIs('admin.seo*') ? 'active' : '' }}"><i class="fa-solid fa-magnifying-glass-chart"></i>SEO</a>
        <a href="{{ route('admin.navigation.index') }}" class="ad-link {{ request()->routeIs('admin.navigation.*') ? 'active' : '' }}"><i class="fa-solid fa-bars-staggered"></i>Navigation</a>
        <a href="{{ route('admin.cms.index') }}" class="ad-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}"><i class="fa-solid fa-file-pen"></i>CMS Pages</a>
        <a href="{{ route('admin.templates.emails') }}" class="ad-link {{ request()->routeIs('admin.templates.emails*') ? 'active' : '' }}"><i class="fa-regular fa-envelope-open"></i>Email Templates</a>
        <a href="{{ route('admin.templates.notifications') }}" class="ad-link {{ request()->routeIs('admin.templates.notifications*') ? 'active' : '' }}"><i class="fa-regular fa-bell"></i>Notification Templates</a>
        <a href="{{ route('admin.theme.index') }}" class="ad-link {{ request()->routeIs('admin.theme.*') ? 'active' : '' }}"><i class="fa-solid fa-palette"></i>Theme Studio</a>

        <div class="ad-nav-section">Finance & Fulfilment</div>
        <a href="{{ route('admin.finance.index') }}" class="ad-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i>Finance Overview</a>
        <a href="{{ route('admin.transactions.index') }}" class="ad-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i>Transactions</a>
        <a href="{{ route('admin.finance.refunds') }}" class="ad-link {{ request()->routeIs('admin.finance.refunds*') ? 'active' : '' }}"><i class="fa-solid fa-rotate-left"></i>Refunds</a>
        <a href="{{ route('admin.payouts.index') }}" class="ad-link {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-transfer"></i>Payouts</a>
        <a href="{{ route('admin.payments') }}" class="ad-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"><i class="fa-regular fa-credit-card"></i>Gateway Settings</a>
        <a href="{{ route('admin.shipping.index') }}" class="ad-link {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}"><i class="fa-solid fa-truck-fast"></i>Shipping</a>
        <a href="{{ route('admin.taxes.index') }}" class="ad-link {{ request()->routeIs('admin.taxes.*') ? 'active' : '' }}"><i class="fa-solid fa-percent"></i>Taxes</a>
        <a href="{{ route('admin.commerce-settings.shipping') }}" class="ad-link {{ request()->routeIs('admin.commerce-settings.shipping*') ? 'active' : '' }}"><i class="fa-solid fa-truck-arrow-right"></i>Shipping Settings</a>
        <a href="{{ route('admin.commerce-settings.tax') }}" class="ad-link {{ request()->routeIs('admin.commerce-settings.tax*') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar"></i>Tax Settings</a>

        <div class="ad-nav-section">Organization & System</div>
        <a href="{{ route('admin.staff.index') }}" class="ad-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"><i class="fa-solid fa-user-shield"></i>Staff</a>
        <a href="{{ route('admin.roles.index') }}" class="ad-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"><i class="fa-solid fa-key"></i>Roles & Permissions</a>
        <a href="{{ route('admin.security.index') }}" class="ad-link {{ request()->routeIs('admin.security.*') ? 'active' : '' }}"><i class="fa-solid fa-shield-halved"></i>Security Center</a>
        <a href="{{ route('admin.activity.index') }}" class="ad-link {{ request()->routeIs('admin.activity*') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i>Activity Log</a>
        <a href="{{ route('admin.operations.system') }}" class="ad-link {{ request()->routeIs('admin.operations.system') ? 'active' : '' }}"><i class="fa-solid fa-heart-pulse"></i>System Status</a>
        <a href="{{ route('admin.readiness.index') }}" class="ad-link {{ request()->routeIs('admin.readiness.*') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i>Production Readiness</a>
        <a href="{{ route('admin.integrations.index') }}" class="ad-link {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}"><i class="fa-solid fa-plug"></i>Integrations</a>
        <a href="{{ route('admin.developer.index') }}" class="ad-link {{ request()->routeIs('admin.developer.*') ? 'active' : '' }}"><i class="fa-solid fa-code"></i>API & Webhooks</a>
        <a href="{{ route('admin.domains.index') }}" class="ad-link {{ request()->routeIs('admin.domains.*') ? 'active' : '' }}"><i class="fa-solid fa-globe"></i>Domains</a>
        <a href="{{ route('admin.backup.index') }}" class="ad-link {{ request()->routeIs('admin.backup*') ? 'active' : '' }}"><i class="fa-solid fa-database"></i>Backups</a>
        <a href="{{ route('admin.settings') }}" class="ad-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i class="fa-solid fa-sliders"></i>Settings</a>
        <a href="{{ route('admin.mail-settings.index') }}" class="ad-link {{ request()->routeIs('admin.mail-settings.*') ? 'active' : '' }}"><i class="fa-regular fa-envelope"></i>Email & SMTP</a>
        <a href="{{ route('admin.commerce-settings.store') }}" class="ad-link {{ request()->routeIs('admin.commerce-settings.store*') ? 'active' : '' }}"><i class="fa-solid fa-store"></i>Store Details</a>
        <a href="{{ route('admin.commerce-settings.checkout') }}" class="ad-link {{ request()->routeIs('admin.commerce-settings.checkout*') ? 'active' : '' }}"><i class="fa-solid fa-bag-shopping"></i>Checkout Settings</a>

        <div class="ad-sidebar-foot">
            <a href="{{ route('home') }}" class="ad-link"><i class="fa-solid fa-arrow-up-right-from-square"></i>View Store</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="ad-link ad-logout" type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i>Logout</button></form>
        </div>
    </aside>

    <section class="ad-main">
        <header class="ad-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="ad-mobile-toggle" type="button" data-admin-menu aria-label="Open admin menu" aria-controls="adminSidebar" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
                <div class="ad-title"><strong>@yield('page-title', 'Commerce Dashboard')</strong><small>Manage your storefront, customers and operations</small></div>
            </div>
            <div class="ad-top-actions">
                <a href="{{ route('admin.products.create') }}" class="ad-icon-btn ad-hide-mobile" title="Add product"><i class="fa-solid fa-plus"></i></a>
                <a href="{{ route('admin.notifications.index') }}" class="ad-icon-btn" title="Admin notifications"><i class="fa-regular fa-bell"></i></a>
                <a href="{{ route('home') }}" target="_blank" class="ad-icon-btn" title="Open storefront"><i class="fa-solid fa-store"></i></a>
                <a href="{{ route('admin.profile.edit') }}" class="ad-profile text-decoration-none text-white"><div class="ad-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}</div><div class="ad-profile-copy">{{ auth()->user()->name ?? 'Admin' }}<span>Administrator</span></div></a>
            </div>
        </header>
        <main class="ad-content" id="admin-content" tabindex="-1">
            @if(session('success'))<div class="alert alert-success border-0 rounded-4" role="status" aria-live="polite">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger border-0 rounded-4" role="alert">{{ session('error') }}</div>@endif
            @yield('content')
        </main>
    </section>
</div>
<div class="ad-overlay" data-admin-overlay aria-hidden="true"></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
