<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900 relative">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/bg-form.png') }}" alt="Background" class="w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/50 to-gray-900/90"></div>
        </div>

        <!-- Content Card -->
        <div
            class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/10 dark:bg-black/20 backdrop-blur-xl border border-white/20 shadow-2xl overflow-hidden sm:rounded-2xl relative z-10 transition-all duration-300 hover:shadow-brand-500/10 hover:border-white/30">

            <!-- Logo area -->
            <div class="flex justify-center mb-8">
                <a href="/" class="transition-transform hover:scale-105 duration-300">
                    <x-application-logo class="w-24 h-24 fill-current text-white drop-shadow-lg" />
                </a>
            </div>

            <div class="text-white">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer / Credits -->
        <div class="mt-8 text-white/40 text-sm relative z-10">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
    <x-toast />
</body>

</html>