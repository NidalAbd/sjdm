@extends('layouts.auth')

@section('title', 'Reset Password - SMM Panel')

@section('content')
    <div class="auth-card-header">
        <div class="heading-md">Forgot your password?</div>
        <p>We'll email you a reset link</p>
    </div>

    @if (session('status'))
        <div class="auth-alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn">Send Password Reset Link</button>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}">Back to sign in</a>
    </div>
@endsection
