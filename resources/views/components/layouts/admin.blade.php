<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.university_name') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>

<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100 overflow-hidden"
    x-data="{ sidebarOpen: false, isLoading: false }" @loading-start.window="isLoading = true"
    @loading-end.window="isLoading = false" @page-loading.window="isLoading = true">
    <!-- Global Loading Bar -->
    <div x-show="isLoading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed top-0 left-0 right-0 h-0.5 bg-brand-500 z-[100] shadow-[0_0_10px_rgba(34,197,94,0.5)]">
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
    <div x-data="{ sidebarOpen: false }" class="h-full flex bg-gray-50 dark:bg-black">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-brand-900 text-white shadow-2xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:flex-shrink-0 flex flex-col h-full border-r border-brand-800">

            <!-- Logo Header -->
            <div class="flex-shrink-0 px-6 py-5 border-b border-brand-800/50 bg-brand-950/20 backdrop-blur-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="relative w-10 h-10 flex-shrink-0">
                        <img src="{{ asset('images/logo-1.png') }}" alt="Logo"
                            class="w-full h-full object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300"
                            loading="lazy">
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-xs text-brand-200 font-medium uppercase tracking-wider truncate">
                            {{ Auth::user()->isDepartmentAdmin() ? Auth::user()->department_name : 'Admin Portal' }}
                        </span>
                        <span
                            class="text-sm font-bold text-white truncate leading-tight">{{ config('app.university_name') }}</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="pt-4 pb-2 px-3">
                    <p class="text-[10px] font-bold text-brand-400 uppercase tracking-widest">Management</p>
                </div>

                <a href="{{ route('admin.pre-registration.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.pre-registration.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pre-registration.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                        </path>
                    </svg>
                    <span>Pre-Registration</span>
                </a>

                <a href="{{ route('admin.alumni.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.alumni.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.alumni.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span>Alumni Records</span>
                </a>

                @if(Auth::user()->isSystemAdmin())
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                                               {{ request()->routeIs('admin.users.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span>User Management</span>
                    </a>

                    <a href="{{ route('admin.courses.index') }}"
                        class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                                               {{ request()->routeIs('admin.courses.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.courses.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span>Courses</span>
                    </a>
                @endif

                <div class="pt-4 pb-2 px-3">
                    <p class="text-[10px] font-bold text-brand-400 uppercase tracking-widest">Content</p>
                </div>

                <a href="{{ route('admin.news_events.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.news_events.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.news_events.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                        </path>
                    </svg>
                    <span>News & Events</span>
                </a>

                <a href="{{ route('admin.gallery.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.gallery.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.gallery.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>Gallery</span>
                </a>

                <a href="{{ route('admin.memos.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.memos.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.memos.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>CHED Memos</span>
                </a>

                <div class="pt-4 pb-2 px-3">
                    <p class="text-[10px] font-bold text-brand-400 uppercase tracking-widest">Reports & Feedback</p>
                </div>

                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.reports.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>Reports</span>
                </a>

                <a href="{{ route('admin.evaluations.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.evaluations.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.evaluations.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Evaluations</span>
                </a>

                <a href="{{ route('admin.tracer.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.tracer.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.tracer.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Tracer Survey</span>
                </a>

                <a href="{{ route('admin.chat-management.index') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium transition-all duration-200 group
                   {{ request()->routeIs('admin.chat-management.*') ? 'bg-brand-700 text-white shadow-md ring-1 ring-brand-600' : 'text-brand-100 hover:bg-brand-800 hover:text-white hover:translate-x-1' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.chat-management.*') ? 'text-white' : 'text-brand-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    <span>Chat Management</span>
                </a>
            </nav>

            <!-- User Info Footer -->
            <div class="flex-shrink-0 border-t border-brand-800 p-4 bg-brand-950/20">
                <div class="flex items-center">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                            class="h-9 w-9 rounded-full object-cover ring-2 ring-brand-600" loading="lazy">
                    @else
                        <div
                            class="bg-brand-700 rounded-full h-9 w-9 flex items-center justify-center text-white font-bold ring-2 ring-brand-600">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <a href="{{ route('admin.profile.edit') }}"
                            class="text-[10px] font-bold text-brand-400 hover:text-white uppercase tracking-widest transition-colors mt-0.5 block">My
                            Profile</a>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-theme-toggle />
                        <button type="button" @click="$dispatch('open-confirmation-modal', { 
                                title: 'Sign Out', 
                                message: 'Are you sure you want to end your session?', 
                                action: '{{ route('logout') }}', 
                                method: 'POST', 
                                confirmText: 'Log Out' 
                            })" class="flex-shrink-0 text-brand-300 hover:text-white transition-colors"
                            title="Log Out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

            <!-- Mobile Header with Toggle -->
            <header
                class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 lg:hidden flex-shrink-0 relative z-20">
                <div class="px-4 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <span class="text-lg font-bold text-gray-900">{{ config('app.university_name') }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-theme-toggle />
                        <!-- Mobile Notification/Profile placeholder -->
                        <div
                            class="h-8 w-8 bg-brand-100 rounded-full flex items-center justify-center text-brand-700 font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Area -->
            <main
                class="flex-1 relative bg-gray-50 dark:bg-black focus:outline-none custom-scrollbar {{ request()->routeIs('admin.chat-management.show') ? 'overflow-hidden flex flex-col' : 'overflow-y-auto' }}">

                <!-- Sticky Page Header -->
                @if(isset($header) && !request()->routeIs('admin.chat-management.show'))
                    <div
                        class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-6 py-4 shadow-sm">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $header }}
                        </h1>
                    </div>
                @endif

                <div
                    class="{{ request()->routeIs('admin.chat-management.show') ? 'h-full w-full' : 'py-6 px-6 max-w-7xl mx-auto' }}">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                @unless(request()->routeIs('admin.chat-management.show'))
                    <footer class="mt-auto py-6 px-6 text-center text-xs text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.university_name') }}. All rights reserved.
                    </footer>
                @endunless
            </main>
        </div>
    </div>
    <x-confirm-modal />
    <x-toast />
    @stack('scripts')
</body>

</html>