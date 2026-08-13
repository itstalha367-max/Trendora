@extends('layouts.auth')
@section('title','Set up 2FA')
@section('icon','fa-solid fa-shield-halved')
@section('heading','Add two-factor protection')
@section('subheading','Scan the QR code with your authenticator app, then enter its 6-digit code.')
@section('content')
<div class="auth-form">
    <div class="auth-qr" aria-label="Authenticator QR code">{!! $qrCode !!}</div>
    <div class="auth-field"><span class="auth-label">Manual setup key</span><div class="auth-secret">{{ $secret }}</div></div>
    <form action="{{ route('2fa.enable') }}" method="POST" class="auth-form">@csrf
        <div class="auth-field"><label class="auth-label" for="code">Verification code</label><div class="auth-input-wrap"><i class="fa-solid fa-key"></i><input class="auth-input auth-code" id="code" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" name="code" placeholder="123456" maxlength="6" required autofocus></div></div>
        <button type="submit" class="auth-btn primary"><i class="fa-solid fa-shield"></i> Enable 2FA</button>
    </form>
    <a href="{{ route('home') }}" class="auth-btn secondary">Skip for now</a>
</div>
@endsection
