<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="dark">
    <title>@yield('title', 'Account') · {{ config('app.name', 'Trendora') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/astra-commerce.css') }}">
</head>
<body class="trendora-auth astra-commerce">
<a class="auth-skip" href="#auth-content">Skip to form</a>
<div class="auth-orb auth-orb-one" aria-hidden="true"></div>
<div class="auth-orb auth-orb-two" aria-hidden="true"></div>
<main class="auth-shell" id="auth-content" tabindex="-1">
    <section class="auth-story" aria-label="Trendora">
        <a href="{{ route('home') }}" class="auth-brand" aria-label="Trendora home"><span>T</span> TRENDORA</a>
        <div>
            <span class="auth-eyebrow">SECURE COMMERCE</span>
            <h1>Style, speed and trust in one storefront.</h1>
            <p>Manage your account, orders and preferences from a polished customer experience built around secure Laravel workflows.</p>
        </div>
        <div class="auth-points" aria-label="Account benefits">
            <span><i class="fa-solid fa-shield-halved"></i> Protected account</span>
            <span><i class="fa-solid fa-box"></i> Order tracking</span>
            <span><i class="fa-solid fa-bolt"></i> Faster checkout</span>
        </div>
    </section>
    <section class="auth-panel">
        <div class="auth-card">
            <a href="{{ route('home') }}" class="auth-mobile-brand"><span>T</span> TRENDORA</a>
            <header class="auth-heading">
                <span class="auth-icon"><i class="@yield('icon', 'fa-solid fa-user')"></i></span>
                <div>
                    <h2>@yield('heading', 'Welcome')</h2>
                    <p>@yield('subheading')</p>
                </div>
            </header>

            @if(session('success'))<div class="auth-alert success" role="status">{{ session('success') }}</div>@endif
            @if(session('status'))<div class="auth-alert success" role="status">{{ session('status') }}</div>@endif
            @if(session('error'))<div class="auth-alert danger" role="alert">{{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="auth-alert danger" role="alert"><strong>Please check the form.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            @yield('content')
        </div>
    </section>
</main>
<script src="{{ asset('js/auth.js') }}" defer></script>
</body>
</html>
