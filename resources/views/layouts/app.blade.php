@php
    try {
        $themeAccent = App\Models\Setting::get('theme_accent','#8b5cf6');
        $themeAccent2 = App\Models\Setting::get('theme_accent_2','#22d3ee');
        $themeRadius = App\Models\Setting::get('theme_card_radius','22');
    } catch (\Throwable $e) { $themeAccent='#8b5cf6'; $themeAccent2='#22d3ee'; $themeRadius='22'; }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070b14">
    <title>@yield('title', 'Trendora — Modern Commerce')</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/trendora.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <style>:root{--tr-primary:{{ $themeAccent }};--tr-primary-2:{{ $themeAccent2 }};--tr-radius:{{ (int)$themeRadius }}px}</style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/astra-commerce.css') }}">
</head>
<body class="trendora-store astra-commerce">
    <a href="#main-content" class="tr-skip">Skip to content</a>
    @include('partials.navbar')
    <main id="main-content" class="tr-main" tabindex="-1">
        @if(session('success'))
            <div class="tr-shell pt-3"><div class="alert alert-success border-0 rounded-4 mb-0" role="status" aria-live="polite">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="tr-shell pt-3"><div class="alert alert-danger border-0 rounded-4 mb-0" role="alert">{{ session('error') }}</div></div>
        @endif
        @if($errors->any())
            <div class="tr-shell pt-3"><div class="alert alert-danger border-0 rounded-4 mb-0" role="alert"><strong>Please check the form:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
        @endif
        @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.mobile-dock')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/trendora.js') }}"></script>
    @stack('scripts')
</body>
</html>
