{{-- resources/views/components/robots-meta.blade.php --}}
@props(['robots' => 'index, follow'])

<meta name="robots" content="{{ $robots }}">

@if(request()->has('redirect') && (request()->is('login') || request()->is('register')))
    {{-- Add noindex for login/register pages with redirect parameters --}}
    <meta name="robots" content="noindex, nofollow">
@endif

@if(request()->get('page', 1) > 1)
    {{-- Add noindex for pagination pages beyond page 1 --}}
    <meta name="robots" content="noindex, follow">
@endif
