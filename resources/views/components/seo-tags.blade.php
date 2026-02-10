@props([
    'title' => config('app.name', 'Alumni System'),
    'description' => 'Reconnect with your alma mater, network with fellow graduates, and stay updated with the latest news and events.',
    'keywords' => 'alumni, university, graduates, networking, events, careers, education',
    'image' => asset('images/logo-2.png'),
    'type' => 'website',
])

<!-- Favicon -->
<link rel="icon" href="{{ asset('images/logo-2.png') }}" type="image/png">
<link rel="shortcut icon" href="{{ asset('images/logo-2.png') }}" type="image/png">

<!-- Primary Meta Tags -->
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ config('app.name') }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">
