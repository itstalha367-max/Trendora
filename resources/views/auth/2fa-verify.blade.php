@extends('layouts.auth')
@section('title','Verify 2FA')
@section('icon','fa-solid fa-mobile-screen-button')
@section('heading','Enter your authenticator code')
@section('subheading','Use the current 6-digit code from your authenticator app to continue.')
@section('content')
<form action="{{ route('2fa.verify.submit') }}" method="POST" class="auth-form">@csrf
<div class="auth-field"><label class="auth-label" for="code">Verification code</label><div class="auth-input-wrap"><i class="fa-solid fa-key"></i><input class="auth-input auth-code" id="code" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" name="code" placeholder="123456" maxlength="6" required autofocus></div></div>
<button type="submit" class="auth-btn primary"><i class="fa-solid fa-check"></i> Verify and continue</button>
<div class="auth-note">Codes refresh in your authenticator app. Trendora never asks you to share the authenticator secret with support.</div>
</form>
@endsection
