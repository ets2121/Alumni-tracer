<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.university_name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-gray-900 flex flex-col overflow-hidden">

    <!-- Top Navigation (Fixed) -->
    <div class="flex-shrink-0 z-30 bg-white shadow-sm border-b border-gray-200">
        @include('layouts.navigation')
    </div>

    <!-- Main Content Wrapper (Scrollable) -->
    <div
        class="flex-1 flex flex-col relative bg-gray-50 {{ request()->routeIs('chat.*') ? 'overflow-hidden' : 'overflow-y-auto custom-scrollbar' }}">

        <!-- Page Heading (Sticky) -->
        @isset($header)
            <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-20">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main
            class="flex-grow w-full {{ request()->routeIs('chat.*') ? 'h-full' : 'max-w-7xl mx-auto py-8 sm:px-6 lg:px-8' }}">
            {{ $slot }}
        </main>

        <!-- Footer -->
        @unless(request()->routeIs('chat.*'))
            <footer class="bg-white border-t border-gray-200 mt-auto flex-shrink-0">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} {{ config('app.university_name') }}. All rights reserved.
                    </p>
                </div>
            </footer>
        @endunless
    </div>

    <x-toast />
    @stack('scripts')
</body>

</html>