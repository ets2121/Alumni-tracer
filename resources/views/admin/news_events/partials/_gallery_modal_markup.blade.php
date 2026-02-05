<!-- Gallery Picker Modal -->
<div x-show="galleryModalOpen" style="display: none;" x-cloak class="fixed inset-0 z-[60] overflow-y-auto"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div x-show="galleryModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
            @click="galleryModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="galleryModalOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-100">

            <div class="bg-white px-6 pt-6 pb-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-50 rounded-xl text-brand-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest" id="modal-title">
                            <span x-show="viewMode === 'albums'">Select Gallery Album</span>
                            <span x-show="viewMode === 'photos'">Select Photo</span>
                        </h3>
                        <div x-show="viewMode === 'photos'"
                            class="flex items-center gap-2 text-xs font-bold text-gray-400">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <span x-text="currentAlbum?.name" class="uppercase"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button x-show="viewMode === 'photos'" @click="backToAlbums()" type="button"
                            class="text-xs font-black text-brand-600 hover:text-brand-700 uppercase tracking-wider flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 rounded-lg transition-all">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Albums
                        </button>
                        <button type="button" @click="galleryModalOpen = false"
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-xl transition-all">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto p-1 scrollbar-hide">

                    <!-- ALBUM VIEW -->
                    <template x-if="viewMode === 'albums'">
                        <template x-for="album in galleryItems" :key="album.id">
                            <div class="relative aspect-[4/3] group cursor-pointer border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-gray-50"
                                @click="openAlbum(album.id)">
                                <img :src="album.cover_image ? '{{ asset('storage') }}/' + album.cover_image : '{{ asset('images/default-album.png') }}'"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent p-4 flex flex-col justify-end">
                                    <h4 class="text-white font-black text-xs uppercase tracking-wider truncate"
                                        x-text="album.name"></h4>
                                    <p class="text-gray-300 text-[10px] font-bold mt-1 uppercase"
                                        x-text="album.photos_count + ' Photos'"></p>
                                </div>
                                <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md rounded-lg px-2 py-1 text-[9px] font-black uppercase tracking-tighter text-brand-700 border border-brand-100 shadow-sm"
                                    x-text="album.category"></div>
                            </div>
                        </template>
                    </template>

                    <!-- PHOTO VIEW -->
                    <template x-if="viewMode === 'photos'">
                        <template x-for="photo in galleryItems" :key="photo.id">
                            <div class="relative aspect-square group cursor-pointer border-4 rounded-2xl overflow-hidden transition-all duration-300"
                                :class="selectedGalleryPhoto === photo.image_path ? 'border-brand-500 shadow-xl shadow-brand-100 scale-95' : 'border-transparent hover:border-brand-100'"
                                @click="selectPhoto(photo.image_path)">
                                <img :src="'{{ asset('storage') }}/' + photo.image_path"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div
                                    class="absolute inset-0 bg-brand-600/0 group-hover:bg-brand-600/10 transition-colors">
                                </div>
                                <div x-show="selectedGalleryPhoto === photo.image_path"
                                    class="absolute top-2 right-2 bg-brand-500 text-white rounded-full p-1 shadow-lg">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </template>

                    <!-- Loading State -->
                    <template x-if="galleryLoading">
                        <div class="col-span-full py-20 flex flex-col items-center justify-center gap-4">
                            <div class="relative">
                                <div class="w-12 h-12 border-4 border-brand-100 rounded-full animate-spin"></div>
                                <div
                                    class="absolute top-0 left-0 w-12 h-12 border-4 border-brand-500 rounded-full animate-spin border-t-transparent">
                                </div>
                            </div>
                            <p class="text-[10px] font-black text-brand-700 uppercase tracking-widest animate-pulse">
                                Scanning Gallery...</p>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!galleryLoading && galleryItems.length === 0">
                        <div class="col-span-full py-24 text-center flex flex-col items-center">
                            <div class="p-5 bg-gray-50 rounded-full mb-4">
                                <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-tight"
                                x-text="viewMode === 'albums' ? 'No albums found.' : 'This album is empty.'"></p>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex justify-center border-t border-gray-50 pt-6" x-show="galleryNextPageUrl">
                    <button type="button" @click="fetchGalleryContent(galleryNextPageUrl)"
                        class="px-8 py-2.5 bg-brand-50 text-brand-700 rounded-full text-xs font-black uppercase tracking-widest hover:bg-brand-100 transition-all border border-brand-100">
                        Discover More
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>