<x-layouts.admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-gray-900 leading-tight">Analytics: {{ $evaluation->title }}</h2>
                <p class="text-xs text-brand-600 font-bold uppercase tracking-wider mt-1">
                    v{{ $evaluation->version }} &bull; {{ ucfirst($evaluation->type) }}
                </p>
            </div>
            <a href="{{ route('admin.evaluations.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        @include('admin.evaluations.partials.analytics_body')
    </div>

    @include('admin.evaluations.partials.analytics_script')
</x-layouts.admin>x-layouts.admin>