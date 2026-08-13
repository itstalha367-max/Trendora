@extends('layouts.app')
@section('title','Access Denied')
@section('content')
<section class="container py-5"><div class="tn-error-card"><div class="tn-error-code">403</div><span class="tn-kicker">Protected area</span><h1>You do not have access to this page.</h1><p>Your account may need a different role or permission. Return to a safe page and continue from there.</p><div class="d-flex gap-2 justify-content-center flex-wrap"><a href="{{ route('home') }}" class="btn btn-primary">Back home</a>@auth<a href="{{ route('user.dashboard') }}" class="btn btn-outline-light">My account</a>@else<a href="{{ route('login') }}" class="btn btn-outline-light">Sign in</a>@endauth</div></div></section>
@endsection
