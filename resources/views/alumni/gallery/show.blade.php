<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('alumni.gallery.index') }}" class="text-gray-500 hover:text-brand-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $album->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ expanded: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Album Hero Banner -->
            <div class="relative h-[400px] rounded-3xl overflow-hidden mb-12 shadow-2xl group">
                @if($album->cover_image)
                    <img src="{{ asset('storage/' . $album->cover_image) }}" alt="{{ $album->name }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                @else
                    <div
                        class="w-full h-full bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif

                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-8 md:p-12">
                    <div class="max-w-4xl">
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                class="px-3 py-1 bg-brand-500 text-white text-[10px] font-bold uppercase tracking-widest rounded-full">
                                {{ $album->category ?? 'General' }}
                            </span>
                            <span class="text-white/60 text-[10px] font-bold uppercase tracking-widest">
                                • {{ $album->photos->count() }} Photos
                            </span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4 tracking-tight">
                            {{ $album->name }}</h1>
                        <p class="text-lg text-white/80 line-clamp-3 leading-relaxed max-w-2xl">
                            {{ $album->description }}</p>
                    </div>
                </div>
            </div>

            <div
                class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-100 pb-6">
                <div class="max-w-3xl">
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight mb-2">Photo Collection</h2>
                    <p class="text-gray-500">Browsing through shared memories and special moments.</p>
                </div>
                <div class="flex items-center text-gray-400 text-sm font-medium italic translate-y-[-8px]">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Posted on {{ $album->created_at->format('M d, Y') }}
                </div>
            </div>

            <!-- Masonry-like Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($album->photos as $photo)
                    <div class="relative overflow-hidden rounded-2xl cursor-zoom-in group aspect-[4/5]"
                        @click="expanded = '{{ asset('storage/' . $photo->image_path) }}'">
                        <img src="{{ asset('storage/' . $photo->image_path) }}" alt="{{ $photo->caption }}"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105 group-hover:brightness-90">
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-white text-sm font-medium">{{ $photo->caption ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($album->photos->isEmpty())
                <div class="py-20 text-center bg-white rounded-3xl shadow-sm border border-gray-100">
                    <p class="text-gray-500 italic">This album is currently empty. Check back later!</p>
                </div>
            @endif
        </div>

        <!-- Lightbox -->
        <template x-if="expanded">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 p-4 md:p-10"
                @keydown.escape.window="expanded = null" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                <button @click="expanded = null"
                    class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors z-[60]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                <div class="relative max-w-7xl max-h-full flex items-center justify-center"
                    @click.away="expanded = null">
                    <img :src="expanded" class="max-w-full max-h-[85vh] object-contain rounded shadow-2xl">
                </div>
            </div>
        </template>
    </div>
</x-app-layout>