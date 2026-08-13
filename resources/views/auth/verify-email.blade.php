@extends('layouts.auth')
@section('title','Verify email')
@section('icon','fa-solid fa-envelope-circle-check')
@section('heading','Verify your email')
@section('subheading','Open the verification link we sent to your email address.')
@section('content')
<div class="auth-form"><div class="auth-note"><i class="fa-solid fa-circle-info"></i> Verification protects order history, saved addresses and account recovery.</div>
<form action="{{ route('verification.send') }}" method="POST">@csrf<button class="auth-btn primary" type="submit" style="width:100%"><i class="fa-regular fa-paper-plane"></i> Resend verification email</button></form>
<form action="{{ route('logout') }}" method="POST">@csrf<button class="auth-btn secondary" type="submit" style="width:100%"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign out</button></form></div>
@endsection
