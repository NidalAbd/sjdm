@if(isset($seoTitle))
    <title>{{ $seoTitle }}</title>
@endif

@if(isset($seoDescription))
    <meta name="description" content="{{ $seoDescription }}">
@endif

@if(isset($canonicalUrl))
    <link rel="canonical" href="{{ $canonicalUrl }}">
@endif

<!-- Language alternates -->
@foreach(['en', 'ar'] as $lang)
    @if(app()->getLocale() != $lang)
        @php
            $localeUrl = $lang === 'en'
                ? str_replace('/' . app()->getLocale(), '', url()->current())
                : str_replace(url()->current(), url('/' . $lang), url()->current());

            // Add query parameters if any (for canonical URLs with parameters)
            if(request()->getQueryString()) {
                $localeUrl .= '?' . request()->getQueryString();
            }
        @endphp
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $localeUrl }}">
    @endif
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

<!-- Open Graph Tags -->
<meta property="og:title" content="{{ $seoTitle ?? 'SMM-Followers' }}">
<meta property="og:description" content="{{ $seoDescription ?? 'Social Media Marketing Services' }}">
<meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

<!-- Twitter Card Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle ?? 'SMM-Followers' }}">
<meta name="twitter:description" content="{{ $seoDescription ?? 'Social Media Marketing Services' }}">
<meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

<!-- Structured Data -->
@if(isset($structuredData))
    <script type="application/ld+json">
        {!! $structuredData !!}
    </script>
@endif
