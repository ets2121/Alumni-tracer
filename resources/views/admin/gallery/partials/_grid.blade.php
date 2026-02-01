<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
    @forelse($albums as $album)
        <div
            class="group relative flex flex-col bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2">
            <!-- Cover Image -->
            <div class="aspect-[4/5] relative overflow-hidden bg-gray-50">
                @if($album->cover_image)
                    <img src="{{ asset('storage/' . $album->cover_image) }}" alt="{{ $album->name }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                @else
                    <div class="w-full h-full flex items-center justify-center opacity-10">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif

                <!-- Hover Actions -->
                <div
                    class="absolute inset-0 bg-gray-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-[2px] flex flex-col items-center justify-center gap-4">
                    <a href="{{ route('admin.gallery.show', $album->id) }}"
                        class="w-12 h-12 bg-white text-gray-900 rounded-full flex items-center justify-center hover:bg-brand-500 hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <button @click="openModal('{{ route('admin.gallery.edit', $album->id) }}', 'Modify Album')"
                        class="px-5 py-2 bg-white/20 text-white rounded-full border border-white/30 text-[10px] font-bold uppercase tracking-wider hover:bg-white hover:text-gray-900 transition-all transform translate-y-4 group-hover:translate-y-0 duration-300 delay-75">
                        Edit
                    </button>
                </div>

                <div class="absolute top-4 left-4">
                    <span
                        class="px-3 py-1.5 bg-white/90 backdrop-blur-sm text-[8px] font-bold uppercase tracking-wider text-gray-900 rounded-full shadow-sm">{{ $album->category }}</span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1">
                    {{ $album->name }}
                </h3>
                <p class="text-[10px] text-gray-500 font-medium line-clamp-2 h-8 leading-relaxed mb-4">
                    {{ $album->description }}
                </p>

                <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider">Assets</span>
                        <span class="text-xs font-bold text-gray-900">{{ $album->photos_count }}</span>
                    </div>
                    <button @click="$dispatch('open-confirmation-modal', { 
                                title: 'Delete Album', 
                                message: 'Are you sure you want to delete {{ addslashes($album->name) }}? This will also delete all photos in this album.', 
                                action: '{{ route('admin.gallery.destroy', $album->id) }}', 
                                method: 'DELETE', 
                                danger: true, 
                                confirmText: 'Delete Album' 
                            })" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                        title="Delete Album">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-32 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
            <h3 class="text-2xl font-bold text-gray-400 mb-2">Void Archive</h3>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Initialize first record</p>
        </div>
    @endforelse
</div>
<div class="mt-20 pagination-container">
    {{ $albums->links() }}
</div>