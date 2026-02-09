<x-layouts.admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('admin.gallery.index') }}"
                    class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em] hover:text-brand-800 flex items-center gap-2 mb-4 transition-all group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-2 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                    Archive Index
                </a>
                <div class="flex items-center gap-6">
                    <h2 class="text-5xl font-black text-gray-900 tracking-tighter">{{ $gallery->name }}</h2>
                    <span
                        class="px-5 py-2 bg-gray-50 border border-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 rounded-full">{{ $gallery->category }}</span>
                </div>
                <p
                    class="text-xs text-gray-400 font-bold uppercase mt-3 italic tracking-widest max-w-2xl leading-relaxed">
                    {{ $gallery->description }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <button @click="$dispatch('open-ingest-modal')"
                    class="bg-gray-900 hover:bg-black text-white px-10 py-5 rounded-[2.5rem] shadow-2xl shadow-gray-200 transition-all flex items-center gap-4 group">
                    <div class="p-2 bg-brand-500 rounded-full group-hover:rotate-180 transition-transform duration-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="font-black uppercase text-[11px] tracking-[0.2em]">Ingest Media</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="photoManager()" x-init="init()" x-on:open-ingest-modal.window="openUploadModal()"
        x-cloak>
        <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6" id="photo-grid">
            @forelse($gallery->photos as $photo)
                <div
                    class="group relative aspect-square rounded-[2.5rem] overflow-hidden bg-white border border-gray-50 shadow-sm hover:shadow-2xl transition-all duration-1000 transform hover:-translate-y-3">
                    <img src="{{ asset('storage/' . $photo->image_path) }}"
                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                        @click="openLightbox({{ $loop->index }}, {{ json_encode($gallery->photos->map(fn($p) => ['id' => $p->id, 'url' => asset('storage/' . $p->image_path), 'caption' => $p->caption ?? 'No caption provided'])) }})">

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/60 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-6 sm:p-8">
                        <p class="text-[10px] font-black text-brand-400 uppercase tracking-[0.3em] mb-3 sm:mb-2 scale-90 origin-left opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-700 delay-100"
                            id="photo-caption-{{ $photo->id }}">
                            {{ $photo->caption ?? 'Untitled Asset' }}
                        </p>

                        <div
                            class="flex items-center justify-between transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-75">
                            <div class="flex gap-2">
                                <button @click="confirmDelete('{{ route('admin.gallery.photo.destroy', $photo) }}')"
                                    class="w-10 h-10 sm:w-12 sm:h-12 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <button @click="editCaption({{ $photo->id }}, '{{ addslashes($photo->caption ?? '') }}')"
                                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-all flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                            <button
                                @click="openLightbox({{ $loop->index }}, {{ json_encode($gallery->photos->map(fn($p) => ['id' => $p->id, 'url' => asset('storage/' . $p->image_path), 'caption' => $p->caption ?? 'No caption provided'])) }})"
                                class="w-10 h-10 sm:w-12 sm:h-12 bg-white text-gray-950 rounded-full hover:bg-brand-500 hover:text-white transition-all flex items-center justify-center shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-48 text-center bg-white rounded-[4rem] border-4 border-dashed border-gray-50">
                    <div
                        class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-10 opacity-40">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-4xl font-black text-gray-100 uppercase tracking-tighter">Repository Empty</h3>
                    <p class="text-[10px] text-gray-300 font-black uppercase tracking-[0.3em] mt-4">Begin ingestion to
                        populate this archival sector</p>
                </div>
            @endforelse
        </div>

        <!-- System Lightbox -->
        <div x-show="lightboxOpen"
            class="fixed inset-0 z-[200] flex items-center justify-center bg-gray-950/98 backdrop-blur-2xl"
            x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-110"
            x-transition:enter-end="opacity-100 scale-100" @keydown.window.escape="lightboxOpen = false"
            @keydown.window.left="prevPhoto()" @keydown.window.right="nextPhoto()">

            <button @click="lightboxOpen = false"
                class="absolute top-6 right-6 sm:top-12 sm:right-12 z-[210] p-4 sm:p-6 text-white/30 hover:text-white transition-all transform hover:rotate-90">
                <svg class="w-8 h-8 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="relative w-full h-full flex items-center justify-center p-4 sm:p-8 md:p-32 overflow-hidden">
                <button @click="prevPhoto()"
                    class="absolute left-4 sm:left-12 z-20 w-14 h-14 sm:w-20 sm:h-20 bg-white/5 hover:bg-white text-white/30 hover:text-gray-950 rounded-full transition-all flex items-center justify-center group shadow-2xl">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 transform group-hover:-translate-x-1 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div class="relative max-w-7xl w-full h-full flex flex-col items-center justify-center gap-6 sm:gap-12">
                    <img :src="currentPhoto.url"
                        class="max-w-full max-h-[60vh] sm:max-h-[75vh] object-contain rounded-[1.5rem] sm:rounded-[2rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] animate-in fade-in zoom-in duration-1000">
                    <div class="text-center max-w-3xl px-6 sm:px-12">
                        <p
                            class="text-[9px] sm:text-[10px] font-black text-brand-500 uppercase tracking-[0.3em] sm:tracking-[0.5em] mb-2 sm:mb-4">
                            Memory Insight
                        </p>
                        <h4 class="text-xl sm:text-3xl font-black text-white tracking-tighter leading-tight"
                            x-text="currentPhoto.caption"></h4>
                    </div>
                </div>

                <button @click="nextPhoto()"
                    class="absolute right-4 sm:right-12 z-20 w-14 h-14 sm:w-20 sm:h-20 bg-white/5 hover:bg-white text-white/30 hover:text-gray-950 rounded-full transition-all flex items-center justify-center group shadow-2xl">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 transform group-hover:translate-x-1 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Upload Photos Modal -->
        <div x-show="uploadModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop" @click="uploadModalOpen = false" x-show="uploadModalOpen"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="modal-content-container sm:max-w-3xl sm:w-full relative z-10" x-show="uploadModalOpen"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-8">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Upload Photos</h3>
                        <button @click="uploadModalOpen = false"
                            class="text-gray-400 hover:text-brand-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.gallery.upload', $gallery) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="relative">
                            <input type="file" name="photos[]" id="photos-upload" multiple required accept="image/*"
                                class="hidden" onchange="previewImages(event)">
                            <label for="photos-upload"
                                class="cursor-pointer block border-2 border-dashed border-gray-300 rounded-2xl p-8 sm:p-12 text-center hover:border-brand-500 hover:bg-brand-50/50 transition-all duration-300 group">
                                <div
                                    class="w-16 h-16 sm:w-20 sm:h-20 bg-brand-50 border-2 border-brand-200 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:bg-brand-100 transition-transform">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-brand-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <p class="text-sm sm:text-base font-bold text-gray-900 mb-1">Click to upload or drag and
                                    drop</p>
                                <p class="text-xs text-gray-500">PNG, JPG up to 5MB each</p>
                            </label>

                            <!-- Image Previews -->
                            <div id="image-previews"
                                class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 hidden">
                                <!-- Previews will be inserted here by JavaScript -->
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="uploadModalOpen = false"
                                class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 bg-white hover:bg-gray-50 transition-all">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-100">
                                Upload Photos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



</x-layouts.admin>