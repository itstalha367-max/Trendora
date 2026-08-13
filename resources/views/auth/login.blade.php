@extends('layouts.auth')
@section('title','Sign in')
@section('icon','fa-solid fa-arrow-right-to-bracket')
@section('heading','Welcome back')
@section('subheading','Sign in to manage orders, rewards and your account.')
@section('content')
<form method="POST" action="{{ url('/login') }}" class="auth-form">
    @csrf
    <div class="auth-field"><label class="auth-label" for="email">Email address</label><div class="auth-input-wrap"><i class="fa-regular fa-envelope"></i><input class="auth-input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus required placeholder="you@example.com"></div></div>
    <div class="auth-field"><label class="auth-label" for="password">Password</label><div class="auth-input-wrap"><i class="fa-solid fa-lock"></i><input class="auth-input" id="password" name="password" type="password" autocomplete="current-password" required placeholder="Your password"><button class="auth-toggle" type="button" data-password-toggle="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
    <div class="auth-options"><label class="auth-check"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Keep me signed in</label><a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a></div>
    <button class="auth-btn primary" type="submit"><i class="fa-solid fa-arrow-right"></i> Sign in securely</button>
</form>
<p class="auth-foot">New to Trendora? <a class="auth-link" href="{{ route('register') }}">Create an account</a></p>
@endsection
