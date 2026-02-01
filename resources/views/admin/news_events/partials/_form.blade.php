<form id="news-form"
    action="{{ isset($newsEvent) ? route('admin.news_events.update', $newsEvent->id) : route('admin.news_events.store') }}"
    method="POST" enctype="multipart/form-data" x-data="{
        postType: '{{ $newsEvent->type ?? 'news' }}',
        galleryModalOpen: false,
        
        // Gallery Picker State
        viewMode: 'albums', // 'albums' or 'photos'
        galleryItems: [], // Stores albums or photos depending on mode
        galleryLoading: false,
        galleryNextPageUrl: null,
        currentAlbum: null,
        selectedGalleryPhoto: '{{ $newsEvent->image_path ?? '' }}',
        targetType: '{{ $newsEvent->target_type ?? 'all' }}',
        targetBatch: '{{ $newsEvent->target_batch ?? '' }}',
        targetCourseId: '{{ $newsEvent->target_course_id ?? '' }}',

        async openGalleryModal() {
            this.galleryModalOpen = true;
            if (this.galleryItems.length === 0) {
                await this.fetchGalleryContent();
            }
        },

        async fetchGalleryContent(url = null, albumId = null) {
            this.galleryLoading = true;
            let fetchUrl = url || '{{ route('admin.news_events.gallery_photos') }}';
            
            // Append album_id if browsing a specific album
            if (albumId && !url) {
                fetchUrl += `?album_id=${albumId}`;
            }

            try {
                const response = await fetch(fetchUrl);
                const result = await response.json();
                
                if (result.type === 'albums') {
                    this.viewMode = 'albums';
                    this.currentAlbum = null;
                } else if (result.type === 'photos') {
                    this.viewMode = 'photos';
                    this.currentAlbum = result.album;
                }

                if (url) {
                    this.galleryItems = [...this.galleryItems, ...result.data.data];
                } else {
                    this.galleryItems = result.data.data;
                }
                this.galleryNextPageUrl = result.data.next_page_url;

            } catch (error) {
                console.error('Error fetching gallery content:', error);
            } finally {
                this.galleryLoading = false;
            }
        },

        openAlbum(albumId) {
            this.fetchGalleryContent(null, albumId);
        },

        backToAlbums() {
            this.fetchGalleryContent();
        },

        selectPhoto(path) {
            this.selectedGalleryPhoto = path;
            this.galleryModalOpen = false;
            // Clear file input if gallery photo is selected
            document.getElementById('image').value = '';
        }
    }">
    @csrf
    @if(isset($newsEvent))
        @method('PUT')
    @endif

    <div class="space-y-6">
        <!-- Title & Type -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" value="{{ $newsEvent->title ?? '' }}" required
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
                <p class="mt-1 text-xs text-red-600 error-message" data-field="title"></p>
            </div>
            <div>
                <label for="type" class="block text-sm font-bold text-gray-700 mb-1">Post Type</label>
                <select name="type" id="type" x-model="postType" required
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4 bg-white">
                    <option value="news">News</option>
                    <option value="event">Event</option>
                    <option value="announcement">Announcement</option>
                </select>
                <p class="mt-1 text-xs text-red-600 error-message" data-field="type"></p>
            </div>
        </div>

        <!-- News Specific Fields -->
        <div x-show="postType === 'news'"
            class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in fade-in slide-in-from-top-2 duration-300">
            <div>
                <label for="author" class="block text-sm font-bold text-gray-700 mb-1">Author</label>
                <input type="text" name="author" id="author" value="{{ $newsEvent->author ?? Auth::user()->name }}"
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
            </div>
            <div>
                <label for="category" class="block text-sm font-bold text-gray-700 mb-1">Category Tags (Comma
                    separated)</label>
                <input type="text" name="category" id="category" value="{{ $newsEvent->category_string ?? '' }}"
                    placeholder="e.g. Sports, Academic, General"
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
            </div>
        </div>

        <!-- Event Specific Fields -->
        <div x-show="postType === 'event'"
            class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in fade-in slide-in-from-top-2 duration-300"
            style="display: none;">
            <div>
                <label for="event_date" class="block text-sm font-bold text-gray-700 mb-1">Event Date & Time</label>
                <input type="datetime-local" name="event_date" id="event_date"
                    value="{{ isset($newsEvent->event_date) ? \Carbon\Carbon::parse($newsEvent->event_date)->format('Y-m-d\TH:i') : '' }}"
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
            </div>
            <div>
                <label for="location" class="block text-sm font-bold text-gray-700 mb-1">Venue / Location</label>
                <input type="text" name="location" id="location" value="{{ $newsEvent->location ?? '' }}"
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
            </div>
            <div class="md:col-span-2">
                <label for="registration_link" class="block text-sm font-bold text-gray-700 mb-1">Registration Link
                    (Optional)</label>
                <input type="url" name="registration_link" id="registration_link"
                    value="{{ $newsEvent->registration_link ?? '' }}" placeholder="https://..."
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
            </div>

            <!-- Automated Invitation Notice -->
            <div class="md:col-span-2 bg-purple-50 rounded-2xl p-4 border border-purple-100 flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-full text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-[10px] font-black text-purple-700 uppercase tracking-widest leading-relaxed">
                    Automated Event Invitations: Initial invitations will be sent automatically to the targeted audience
                    upon publication.
                </p>
            </div>

            <!-- Event Photos Multi-Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">Event Photos (Post-Activity Gallery)</label>
                <div
                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-brand-500 transition-colors bg-gray-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                            viewBox="0 0 48 48">
                            <path
                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="photos"
                                class="relative cursor-pointer bg-white rounded-md font-medium text-brand-600 hover:text-brand-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-500">
                                <span>Upload photos</span>
                                <input id="photos" name="photos[]" type="file" multiple class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG up to 5MB each</p>
                    </div>
                </div>
                @if(isset($newsEvent) && $newsEvent->photos && $newsEvent->photos->count() > 0)
                    <div class="mt-4 grid grid-cols-4 sm:grid-cols-6 gap-2">
                        @foreach($newsEvent->photos as $photo)
                            <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 relative group">
                                <img src="{{ asset('storage/' . $photo->image_path) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Visibility & Targeting -->
            <div class="md:col-span-2 border-t border-dashed border-gray-200 pt-6">
                <h4 class="text-sm font-black text-brand-800 uppercase tracking-widest mb-4">Visibility & Targeting</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="target_type" class="block text-sm font-bold text-gray-700 mb-1">Target
                            Audience</label>
                        <select name="target_type" id="target_type" x-model="targetType" required
                            class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4 bg-white">
                            <option value="all">All Alumni</option>
                            <option value="batch">Specific Batch</option>
                            <option value="course">Specific Course</option>
                            <option value="batch_course">Course within a Batch</option>
                        </select>
                    </div>

                    <div x-show="targetType === 'batch' || targetType === 'batch_course'"
                        class="animate-in fade-in zoom-in-95 duration-200">
                        <label for="target_batch" class="block text-sm font-bold text-gray-700 mb-1">Target Batch
                            Year</label>
                        <select name="target_batch" id="target_batch" x-model="targetBatch"
                            class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4 bg-white">
                            <option value="">Select Year</option>
                            @for($year = date('Y'); $year >= 1990; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div x-show="targetType === 'course' || targetType === 'batch_course'"
                        class="animate-in fade-in zoom-in-95 duration-200">
                        <label for="target_course_id" class="block text-sm font-bold text-gray-700 mb-1">Target
                            Course</label>
                        <select name="target_course_id" id="target_course_id" x-model="targetCourseId"
                            class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4 bg-white">
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 italic">Targeted events will only be visible in the feed and sent
                    as invitations to the selected group.</p>
            </div>
        </div>

        <!-- Announcement Specific Fields -->
        <div x-show="postType === 'announcement'"
            class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in fade-in slide-in-from-top-2 duration-300"
            style="display: none;">
            <div>
                <label for="expires_at" class="block text-sm font-bold text-gray-700 mb-1">Expiration Date</label>
                <input type="date" name="expires_at" id="expires_at"
                    value="{{ isset($newsEvent->expires_at) ? \Carbon\Carbon::parse($newsEvent->expires_at)->format('Y-m-d') : '' }}"
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-3 px-4">
                <p class="text-xs text-gray-500 mt-1">Post will be automatically archived after this date</p>
            </div>
            <div class="flex items-center h-full pt-6">
                <div class="flex items-center">
                    <input id="is_pinned" name="is_pinned" type="checkbox" {{ (isset($newsEvent->is_pinned) && $newsEvent->is_pinned) ? 'checked' : '' }}
                        class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                    <label for="is_pinned" class="ml-3 block text-sm font-bold text-gray-700">
                        Pin to Dashboard
                    </label>
                </div>
            </div>
        </div>

        <!-- Content (Rich Editor Placeholder) -->
        <div>
            <label for="content" class="block text-sm font-bold text-gray-700 mb-1">Content / Body</label>
            <div
                class="border border-gray-300 rounded-xl overflow-hidden shadow-sm focus-within:ring-1 focus-within:ring-brand-500 focus-within:border-brand-500">
                <div class="bg-gray-50 border-b border-gray-200 px-3 py-2 flex gap-2 text-gray-600">
                    <span class="text-xs font-bold uppercase tracking-wider">Rich Text Editor</span>
                    <!-- Placeholder Toolbar -->
                    <div class="flex gap-2 ml-4 opacity-50 pointer-events-none">
                        <span class="font-bold">B</span>
                        <span class="italic">I</span>
                        <span class="underline">U</span>
                    </div>
                </div>
                <textarea name="content" id="content" rows="6" required
                    class="w-full border-0 focus:ring-0 text-sm p-4 resize-y"
                    placeholder="Write your content here...">{{ $newsEvent->content ?? '' }}</textarea>
            </div>
            <p class="mt-1 text-xs text-red-600 error-message" data-field="content"></p>
        </div>

        <!-- Featured Image -->
        <div>
            <label for="image" class="block text-sm font-bold text-gray-700 mb-1">Featured Image</label>
            <input type="hidden" name="gallery_image_path" :value="selectedGalleryPhoto">

            <div class="flex items-center gap-6">
                <!-- Preview -->
                <div class="shrink-0" x-show="selectedGalleryPhoto" x-cloak>
                    <img :src="'{{ asset('storage') }}/' + selectedGalleryPhoto"
                        class="h-24 w-32 object-cover rounded-xl shadow-sm border border-gray-200">
                </div>

                <div class="flex-1 space-y-3">
                    <!-- File Upload -->
                    <div>
                        <input type="file" name="image" id="image" accept="image/*"
                            @change="selectedGalleryPhoto = null"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-colors">
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400 font-bold uppercase">OR</span>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>

                    <!-- Gallery Button -->
                    <button type="button" @click="openGalleryModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Select from Gallery
                    </button>

                    <p class="mt-1 text-xs text-gray-500">Recommended size: 1200x630px</p>
                </div>
            </div>
            <p class="mt-1 text-xs text-red-600 error-message" data-field="image"></p>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
        <button type="button" @click="closeModal()"
            class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 bg-white hover:bg-gray-50 transition-all">
            Cancel
        </button>
        <button type="submit" :disabled="saving"
            class="px-6 py-3 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-100 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="!saving">{{ isset($newsEvent) ? 'Update Post' : 'Create Post' }}</span>
            <span x-show="saving" style="display: none;" x-cloak>Saving...</span>
        </button>
    </div>

    <!-- Gallery Picker Modal -->
    <div x-show="galleryModalOpen" style="display: none;" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="galleryModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true" @click="galleryModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="galleryModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <span x-show="viewMode === 'albums'">Select Gallery Album</span>
                                <span x-show="viewMode === 'photos'">Select Photo</span>
                            </h3>
                            <div x-show="viewMode === 'photos'" class="flex items-center gap-2 text-sm text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span x-text="currentAlbum?.name"></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button x-show="viewMode === 'photos'" @click="backToAlbums()" type="button"
                                class="text-sm text-gray-600 hover:text-brand-600 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Albums
                            </button>
                            <button type="button" @click="galleryModalOpen = false"
                                class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-5 gap-3 max-h-[60vh] overflow-y-auto p-1">

                        <!-- ALBUM VIEW -->
                        <template x-if="viewMode === 'albums'">
                            <template x-for="album in galleryItems" :key="album.id">
                                <div class="relative aspect-[4/3] group cursor-pointer border rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all bg-gray-50"
                                    @click="openAlbum(album.id)">
                                    <img :src="album.cover_image ? '{{ asset('storage') }}/' + album.cover_image : '{{ asset('images/default-album.png') }}'"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div
                                        class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4">
                                        <h4 class="text-white font-bold text-sm truncate" x-text="album.name"></h4>
                                        <p class="text-gray-300 text-xs mt-0.5" x-text="album.photos_count + ' Photos'">
                                        </p>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-700"
                                        x-text="album.category"></div>
                                </div>
                            </template>
                        </template>

                        <!-- PHOTO VIEW -->
                        <template x-if="viewMode === 'photos'">
                            <template x-for="photo in galleryItems" :key="photo.id">
                                <div class="relative aspect-square group cursor-pointer border-2 rounded-lg overflow-hidden transition-all"
                                    :class="selectedGalleryPhoto === photo.image_path ? 'border-brand-500 ring-2 ring-brand-500 ring-offset-2' : 'border-transparent hover:border-gray-300'"
                                    @click="selectPhoto(photo.image_path)">
                                    <img :src="'{{ asset('storage') }}/' + photo.image_path"
                                        class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors">
                                    </div>
                                </div>
                            </template>
                        </template>

                        <!-- Loading State -->
                        <template x-if="galleryLoading">
                            <div class="col-span-full py-12 flex justify-center">
                                <svg class="animate-spin h-8 w-8 text-brand-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="!galleryLoading && galleryItems.length === 0">
                            <div class="col-span-full py-16 text-center text-gray-500 flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p x-text="viewMode === 'albums' ? 'No albums found.' : 'No photos in this album.'"></p>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex justify-center" x-show="galleryNextPageUrl">
                        <button type="button" @click="fetchGalleryContent(galleryNextPageUrl)"
                            class="text-sm font-bold text-brand-600 hover:text-brand-700">
                            Load More
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>