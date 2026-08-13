@extends('layouts.auth')
@section('title','Confirm password')
@section('icon','fa-solid fa-lock')
@section('heading','Confirm it is you')
@section('subheading','Re-enter your password before continuing to this protected action.')
@section('content')
<form method="POST" action="{{ route('password.confirm.store') }}" class="auth-form">@csrf
<div class="auth-field"><label class="auth-label" for="password">Password</label><div class="auth-input-wrap"><i class="fa-solid fa-lock"></i><input class="auth-input" id="password" type="password" name="password" required autocomplete="current-password" autofocus><button class="auth-toggle" type="button" data-password-toggle="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div></div>
<button class="auth-btn primary" type="submit">Confirm password</button></form>
<p class="auth-foot"><a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a></p>
@endsection
