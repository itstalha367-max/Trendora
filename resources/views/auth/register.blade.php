@extends('layouts.auth')
@section('title','Create account')
@section('icon','fa-solid fa-user-plus')
@section('heading','Create your account')
@section('subheading','Save addresses, track orders and unlock a faster checkout.')
@section('content')
<form method="POST" action="{{ url('/register') }}" class="auth-form">
    @csrf
    <div class="auth-field"><label class="auth-label" for="name">Full name</label><div class="auth-input-wrap"><i class="fa-regular fa-user"></i><input class="auth-input" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus placeholder="Your full name"></div></div>
    <div class="auth-field"><label class="auth-label" for="email">Email address</label><div class="auth-input-wrap"><i class="fa-regular fa-envelope"></i><input class="auth-input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required placeholder="you@example.com"></div></div>
    <div class="auth-row">
        <div class="auth-field"><label class="auth-label" for="password">Password</label><div class="auth-input-wrap"><i class="fa-solid fa-lock"></i><input class="auth-input" id="password" name="password" type="password" autocomplete="new-password" required minlength="8" placeholder="8+ characters"><button class="auth-toggle" type="button" data-password-toggle="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
        <div class="auth-field"><label class="auth-label" for="password_confirmation">Confirm password</label><div class="auth-input-wrap"><i class="fa-solid fa-check"></i><input class="auth-input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8" placeholder="Repeat password"></div></div>
    </div>
    <button class="auth-btn primary" type="submit"><i class="fa-solid fa-user-check"></i> Create account</button>
    <p class="auth-meta">By creating an account you agree to our <a class="auth-link" href="{{ route('pages.terms') }}">Terms</a> and <a class="auth-link" href="{{ route('pages.privacy') }}">Privacy Policy</a>.</p>
</form>
<p class="auth-foot">Already have an account? <a class="auth-link" href="{{ route('login') }}">Sign in</a></p>
@endsection
