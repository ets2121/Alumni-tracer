<nav x-data="{ open: false }" @page-navigated.window="open = false"
    class="bg-brand-600 dark:bg-dark-bg-elevated border-b border-brand-500 dark:border-dark-border">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo-1.png') }}" alt="Logo" class="h-8 w-auto" loading="lazy">
                        <span class="font-bold text-white text-xl">AlumniConnect</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="text-white hover:text-black hover:border-brand-200">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('alumni.gallery.index')" :active="request()->routeIs('alumni.gallery.*')"
                        class="text-white hover:text-black hover:border-brand-200">
                        {{ __('Gallery') }}
                    </x-nav-link>
                    <x-nav-link :href="route('alumni.memos.index')" :active="request()->routeIs('alumni.memos.*')"
                        class="text-brand-100 hover:text-white hover:border-brand-200">
                        {{ __('CHED Memos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')"
                        class="text-brand-100 hover:text-white hover:border-brand-200">
                        {{ __('Group Chat') }}
                    </x-nav-link>
                    <x-nav-link :href="route('alumni.evaluations.index')"
                        :active="request()->routeIs('alumni.evaluations.*')"
                        class="text-brand-100 hover:text-white hover:border-brand-200 relative">
                        {{ __('Evaluations') }}
                        <!-- Badge -->
                        @if(Auth::user()->status === 'active')
                            <span class="absolute top-3 -right-2 flex h-2.5 w-2.5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                            </span>
                        @endif
                    </x-nav-link>
                    <x-nav-link :href="route('tracer.index')" :active="request()->routeIs('tracer.*')"
                        class="text-brand-100 hover:text-white hover:border-brand-200 relative">
                        {{ __('Tracer Survey') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <x-theme-toggle />

                <!-- If admin or dept_admin, show admin dashboard link -->
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                        class="me-4 text-sm font-medium text-brand-100 hover:text-white px-3 py-2 rounded-md bg-brand-500/30">
                        Admin Panel
                    </a>
                @endif
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-brand-100 bg-brand-600 hover:bg-brand-700 focus:outline-none transition ease-in-out duration-150">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt=""
                                    class="w-8 h-8 rounded-full object-cover me-2 border border-brand-400" loading="lazy">
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center me-2 border border-brand-400">
                                    <svg class="w-4 h-4 text-brand-200" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                            @endif
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('alumni.profile.edit')">
                            {{ __('My Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Account Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button class="w-full text-start">
                            <x-dropdown-link href="#" x-data @click.prevent="$dispatch('open-confirmation-modal', {
                                title: 'Sign Out',
                                message: 'Are you sure you want to end your session?',
                                action: '{{ route('logout') }}',
                                method: 'POST',
                                confirmText: 'Log Out'
                            })">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-brand-200 hover:text-white hover:bg-brand-700 focus:outline-none focus:bg-brand-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden bg-brand-700 dark:bg-dark-bg-elevated border-b dark:border-dark-border">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="text-brand-100 hover:bg-brand-600 hover:text-white border-l-4 border-transparent hover:border-brand-300">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')"
                class="text-brand-100 hover:bg-brand-600 hover:text-white border-l-4 border-transparent hover:border-brand-300">
                {{ __('Group Chat') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('alumni.evaluations.index')"
                :active="request()->routeIs('alumni.evaluations.*')"
                class="text-brand-100 hover:bg-brand-600 hover:text-white border-l-4 border-transparent hover:border-brand-300">
                {{ __('Evaluations') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-brand-600 dark:border-dark-border">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-brand-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('alumni.profile.edit')"
                    class="text-brand-200 hover:text-white hover:bg-brand-600">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')"
                    class="text-brand-200 hover:text-white hover:bg-brand-600">
                    {{ __('Account Settings') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        class="text-brand-200 hover:text-white hover:bg-brand-600" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>