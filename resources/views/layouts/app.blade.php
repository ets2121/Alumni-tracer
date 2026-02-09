<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

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

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="h-full font-sans antialiased text-gray-900 flex flex-col overflow-hidden" x-data="{ isLoading: false }"
    @loading-start.window="isLoading = true" @loading-end.window="isLoading = false">
    <!-- Global Loading Bar -->
    <div x-show="isLoading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed top-0 left-0 right-0 h-1 bg-brand-500 z-[100] shadow-[0_0_10px_rgba(34,197,94,0.5)]">
        <div class="h-full bg-brand-600 animate-[loading_2s_ease-in-out_infinite]"></div>
    </div>

    <style>
        @keyframes loading {
            0% {
                width: 0;
                left: 0;
            }

            50% {
                width: 70%;
                left: 15%;
            }

            100% {
                width: 0;
                left: 100%;
            }
        }
    </style>

    <!-- Top Navigation (Fixed) -->
    <div
        class="flex-shrink-0 z-30 bg-white dark:bg-dark-bg-elevated shadow-sm border-b border-gray-200 dark:border-dark-border">
        @include('layouts.navigation')
    </div>

    <!-- Main Content Wrapper (Scrollable) -->
    <div id="content-wrapper"
        class="flex-1 flex flex-col relative bg-gray-50 dark:bg-dark-bg-deep {{ request()->routeIs('chat.*', 'dashboard') ? 'overflow-hidden' : 'overflow-y-auto custom-scrollbar' }}">

        <!-- Page Heading (Sticky) -->
        @isset($header)
            <header
                class="bg-white/80 dark:bg-dark-bg-elevated/80 backdrop-blur-md shadow-sm border-b border-gray-100 dark:border-dark-border sticky top-0 z-20 flex-shrink-0">
                <div id="header-container"
                    class="{{ request()->routeIs('chat.*', 'dashboard') ? 'max-w-2xl mx-auto py-3 px-4' : 'max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8' }}">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main
            class="flex-1 w-full {{ request()->routeIs('chat.*', 'dashboard') ? 'min-h-0 overflow-hidden' : 'max-w-7xl mx-auto py-8 sm:px-6 lg:px-8' }}">
            {{ $slot }}
        </main>

        <!-- Footer -->
        @unless(request()->routeIs('chat.*', 'dashboard'))
            <footer
                class="bg-white border-t border-gray-200 mt-auto flex-shrink-0 dark:bg-dark-bg-deep dark:border-dark-border">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500 dark:text-dark-text-muted">
                        &copy; {{ date('Y') }} {{ config('app.university_name') }}. All rights reserved.
                    </p>
                </div>
            </footer>
        @endunless
    </div>

    <x-confirm-modal />
    <x-toast />
    @stack('scripts')
</body>

</html>