@extends('layouts.auth')

@section('title', 'Reset Password - SMM Panel')

@section('content')
    <div class="auth-card-header">
        <div class="heading-md">Choose a new password</div>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus autocomplete="email">
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password-confirm">Confirm new password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="auth-btn">Reset Password</button>
    </form>
@endsection
