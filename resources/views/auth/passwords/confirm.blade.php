@extends('layouts.auth')

@section('title', 'Confirm Password - SMM Panel')

@section('content')
    <div class="auth-card-header">
        <div class="heading-md">Confirm your password</div>
        <p>Please confirm your password before continuing</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autofocus autocomplete="current-password">
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn">Confirm</button>
    </form>
@endsection
