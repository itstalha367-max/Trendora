@extends('layouts.auth')
@section('title','Reset password')
@section('icon','fa-solid fa-key')
@section('heading','Forgot your password?')
@section('subheading','Enter your account email and we will send a secure reset link.')
@section('content')
<form action="{{ route('password.email') }}" method="POST" class="auth-form">@csrf
<div class="auth-field"><label class="auth-label" for="email">Email address</label><div class="auth-input-wrap"><i class="fa-regular fa-envelope"></i><input class="auth-input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus required placeholder="you@example.com"></div></div>
<button class="auth-btn primary" type="submit"><i class="fa-regular fa-paper-plane"></i> Send reset link</button>
</form><p class="auth-foot"><a class="auth-link" href="{{ route('login') }}"><i class="fa-solid fa-arrow-left"></i> Back to sign in</a></p>
@endsection
