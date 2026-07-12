@extends('layouts.auth')

@section('title', 'Verify Email - SMM Panel')

@section('content')
    <div class="auth-card-header">
        <div class="heading-md">Verify your email</div>
        <p>Check your inbox for a verification link</p>
    </div>

    @if (session('resent'))
        <div class="auth-alert">A fresh verification link has been sent to your email address.</div>
    @endif

    <p style="font-size:0.85rem;opacity:0.6;text-align:center;margin-bottom:16px;">
        Before continuing, please click the link in the verification email. If you didn't receive it, you can request another below.
    </p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="auth-btn">Resend Verification Email</button>
    </form>
@endsection
