<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($albums as $album)
        <div class="group relative flex flex-col bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2"
            @click="window.location.href = '{{ route('alumni.gallery.show', $album) }}'">
            <div class="aspect-[4/5] relative overflow-hidden bg-gray-50">
                @if($album->cover_image)
                    <img src="{{ asset('storage/' . $album->cover_image) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        alt="{{ $album->name }}">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-brand-50/30">
                        <svg class="w-12 h-12 text-brand-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                @endif

                <div
                    class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-gray-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                    <span class="text-white text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                        View Album
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3">
                            </path>
                        </svg>
                    </span>
                </div>

                <div class="absolute top-4 left-4">
                    <span
                        class="px-3 py-1 bg-white/90 backdrop-blur-md text-[10px] font-bold text-gray-700 rounded-full shadow-sm border border-gray-100 uppercase tracking-tighter">
                        {{ $album->category ?? 'General' }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-brand-600 transition-colors">
                    {{ $album->name }}
                </h4>
                <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest gap-3">
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ $album->photos_count }} Items
                    </span>
                    <span>•</span>
                    <span>{{ $album->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    @empty
        <div
            class="col-span-full py-20 text-center text-gray-500 italic bg-white rounded-2xl border border-dashed border-gray-200">
            No gallery albums found matching your search.
        </div>
    @endforelse
</div>
<div class="mt-8 pagination-container">
    {{ $albums->links() }}
</div>