<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visual Gallery') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="alumniGallery()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Memories & Events</h3>
                    <p class="text-gray-500">Relive the moments and see what's happening in our community.</p>
                </div>

                <div class="relative w-full max-w-md">
                    <input type="text" x-model.debounce.300ms="search" placeholder="Search albums..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div id="list-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
                class="transition-opacity duration-200">
                @include('alumni.gallery.partials._list')
            </div>
        </div>
    </div>

</x-app-layout>