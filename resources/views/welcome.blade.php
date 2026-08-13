@extends('layouts.app')
@section('title', 'Trendora')
@section('content')
<div class="tr-shell py-5"><div class="card p-5 text-center"><h1 class="fw-bold mb-3">Trendora</h1><p class="text-muted mb-4">Your Laravel 12 commerce storefront is ready.</p><div><a class="btn btn-primary px-4" href="{{ route('products.index') }}">Explore products</a></div></div></div>
@endsection
