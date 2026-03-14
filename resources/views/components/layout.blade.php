@props([
    'title' => 'Indonesia Mining Watch - WebGIS Pemantauan Wilayah Pertambangan',
    'metaDescription' => 'WebGIS Pemantauan Wilayah Pertambangan di Indonesia - Indonesia Mining Watch',
    'metaTitle' => null,
    'canonicalUrl' => null,
    'ogType' => 'website',
    'ogImage' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'robots' => 'index,follow',
])
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    @if($canonicalUrl)
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif

    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle ?? $metaTitle ?? $title }}">
    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription }}">
    @if($canonicalUrl)
        <meta property="og:url" content="{{ $canonicalUrl }}">
    @endif
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle ?? $metaTitle ?? $title }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? $metaDescription }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- MapLibre GL JS -->
    <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
</head>

<body class="bg-gray-950 text-gray-100 font-sans antialiased min-h-screen">
    {{ $slot }}

    @stack('scripts')
</body>

</html>
