@extends('layouts.auth')

@section('title', 'Create Account - SMM Panel')

@section('content')
    <div class="auth-card-header">
        <div class="heading-md">Create your account</div>
        <p>Start ordering in minutes</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password-confirm">Confirm password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="auth-field">
            <label for="referral_code">Referral code <span style="opacity:.5;font-weight:400;">(optional)</span></label>
            <input id="referral_code" type="text" name="referral_code" value="{{ old('referral_code', request('ref')) }}" autocomplete="off">
            @error('referral_code')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn">Create Account</button>
    </form>

    <div class="auth-links">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
@endsection
