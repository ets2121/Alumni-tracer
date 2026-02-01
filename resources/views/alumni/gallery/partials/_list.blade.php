<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($albums as $album)
        <a href="{{ route('alumni.gallery.show', $album->id) }}"
            class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col">
            <div class="aspect-video relative overflow-hidden bg-gray-200">
                @if($album->cover_image_path)
                    <img src="{{ asset('storage/' . $album->cover_image_path) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        alt="{{ $album->title }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <div
                    class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg">
                    {{ $album->photos_count }} PHOTOS
                </div>
            </div>
            <div class="p-5 flex-grow">
                <h4 class="font-bold text-gray-800 group-hover:text-brand-600 transition-colors mb-2 line-clamp-1">
                    {{ $album->title }}</h4>
                <p class="text-xs text-gray-500 line-clamp-2">{{ $album->description }}</p>
            </div>
            <div class="px-5 pb-5 flex items-center justify-between">
                <span
                    class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">{{ $album->created_at->format('M Y') }}</span>
                <span
                    class="text-brand-600 font-bold text-xs group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                    View Album
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </div>
        </a>
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