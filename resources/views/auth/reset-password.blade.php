@extends('layouts.auth')
@section('title','Choose new password')
@section('icon','fa-solid fa-shield-halved')
@section('heading','Create a new password')
@section('subheading','Use a strong password you do not reuse on other services.')
@section('content')
<form action="{{ route('password.update') }}" method="POST" class="auth-form">@csrf<input type="hidden" name="token" value="{{ $token }}">
<div class="auth-field"><label class="auth-label" for="email">Email address</label><div class="auth-input-wrap"><i class="fa-regular fa-envelope"></i><input class="auth-input" id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required autocomplete="email"></div></div>
<div class="auth-field"><label class="auth-label" for="password">New password</label><div class="auth-input-wrap"><i class="fa-solid fa-lock"></i><input class="auth-input" id="password" type="password" name="password" minlength="8" required autocomplete="new-password"><button class="auth-toggle" type="button" data-password-toggle="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
<div class="auth-field"><label class="auth-label" for="password_confirmation">Confirm new password</label><div class="auth-input-wrap"><i class="fa-solid fa-check"></i><input class="auth-input" id="password_confirmation" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></div></div>
<button class="auth-btn primary" type="submit"><i class="fa-solid fa-check"></i> Reset password</button>
</form>
@endsection
