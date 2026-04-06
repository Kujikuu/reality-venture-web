@php
    $page = app(\Inertia\Inertia::class)->getShared('seo');
    $jsonLd = null;

    if (is_callable($page)) {
        $resolved = $page();
        $jsonLd = $resolved['jsonLd'] ?? null;
    } elseif (is_array($page)) {
        $jsonLd = $page['jsonLd'] ?? null;
    }
@endphp

@if($jsonLd)
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
