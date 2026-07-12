@extends('layouts.auth')

@section('title', 'Sign In - SMM Panel')

@section('content')
    <div class="auth-card-header">
        <div class="heading-md">Welcome back</div>
        <p>Sign in to manage your orders</p>
    </div>

    @if (session('status'))
        <div class="auth-alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <label class="auth-remember">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Remember me
        </label>

        <button type="submit" class="auth-btn">Sign In</button>

        <div class="auth-links-row">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            @endif
        </div>
    </form>

    <div class="auth-links">
        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
    </div>
@endsection
