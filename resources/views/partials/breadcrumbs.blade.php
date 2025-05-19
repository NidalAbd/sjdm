@php
    $segments = Request::segments();
    $breadcrumbs = [];
@endphp

<nav aria-label="breadcrumb" class="">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('adminlte.home') }}</a></li>
        @foreach($segments as $key => $segment)
            @php
                $url = url(implode('/', array_slice($segments, 0, $key + 1)));
                // Convert segment to translation key if available
                $translationKey = 'adminlte.breadcrumbs.' . strtolower($segment);
                $name = __($translationKey) !== $translationKey ? __($translationKey) : ucfirst($segment);

                if ($key + 1 === count($segments)) {
                    $breadcrumbs[] = ['name' => $name, 'url' => $url, 'active' => true];
                } else {
                    $breadcrumbs[] = ['name' => $name, 'url' => $url, 'active' => false];
                }
            @endphp
        @endforeach

        @foreach($breadcrumbs as $breadcrumb)
            @if ($breadcrumb['active'])
                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
    @foreach($breadcrumbs as $index => $breadcrumb)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "name": "{{ $breadcrumb['title'] }}",
            "item": "{{ $breadcrumb['url'] }}"
        }@if(!$loop->last),@endif
    @endforeach
    ]
}
</script>

@include('partials.alert')
