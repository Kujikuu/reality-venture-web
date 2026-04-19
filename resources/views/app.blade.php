<!DOCTYPE html>
<html class="scroll-pt-24">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title inertia>{{ config('app.name', 'Reality Venture') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Public+Sans:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
        <script>
            window.desksConfig = {
                apiUrl: '{{ config("desks.api_url") }}',
                siteKey: '{{ config("desks.site_key") }}',
                locale: '{{ app()->getLocale() }}',
            };
        </script>
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])
        @include('partials.seo-jsonld')
        @inertiaHead
    </head>
    <body class="flex-1 w-full flex flex-col">
        @inertia
    </body>
</html>
