<form id="news-form"
    action="{{ isset($newsEvent) ? route('admin.news_events.update', $newsEvent->id) : route('admin.news_events.store') }}"
    method="POST" enctype="multipart/form-data" class="h-full flex flex-col" x-data="{
        postType: '{{ $newsEvent->type ?? 'news' }}',
        visibilityType: '{{ $newsEvent->visibility_type ?? 'all' }}',
        selectedDepartment: '{{ $newsEvent->department_name ?? '' }}',
        galleryModalOpen: false,
        saving: false,
        
        // Gallery Picker State
        viewMode: 'albums',
        galleryItems: [],
        galleryLoading: false,
        galleryNextPageUrl: null,
        currentAlbum: null,
        selectedGalleryPhoto: '{{ $newsEvent->image_path ?? '' }}',

        async openGalleryModal() {
            this.galleryModalOpen = true;
            if (this.galleryItems.length === 0) {
                await this.fetchGalleryContent();
            }
        },

        async fetchGalleryContent(url = null, albumId = null) {
            this.galleryLoading = true;
            let fetchUrl = url || '{{ route('admin.news_events.gallery_photos') }}';
            if (albumId && !url) fetchUrl += `?album_id=${albumId}`;

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

                this.galleryItems = url ? [...this.galleryItems, ...result.data.data] : result.data.data;
                this.galleryNextPageUrl = result.data.next_page_url;

            } catch (error) {
                console.error('Error fetching gallery content:', error);
            } finally {
                this.galleryLoading = false;
            }
        },

        openAlbum(albumId) { this.fetchGalleryContent(null, albumId); },
        backToAlbums() { this.fetchGalleryContent(); },
        selectPhoto(path) {
            this.selectedGalleryPhoto = path;
            this.galleryModalOpen = false;
            document.getElementById('image').value = '';
        }
    }">
    @csrf
    @if(isset($newsEvent))
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 h-full">
        <!-- Column 1: Configuration (4 cols) -->
        <div class="lg:col-span-4 space-y-6 lg:overflow-y-auto lg:h-full lg:pr-1 custom-scrollbar">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                <h3 class="text-sm font-black text-brand-800 uppercase tracking-widest border-b border-gray-100 pb-3">
                    Post Configuration</h3>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ $newsEvent->title ?? '' }}" required
                        class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-2.5 px-4">
                    <p class="mt-1 text-[10px] text-red-600 error-message" data-field="title"></p>
                </div>

                <!-- Type & Visibility -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-bold text-gray-700 uppercase mb-1">Type</label>
                        <select name="type" id="type" x-model="postType" required
                            class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-2.5 px-4 bg-white">
                            <option value="news">News</option>
                            <option value="event">Event</option>
                            <option value="announcement">Announcement</option>
                            <option value="job">Job Post</option>
                        </select>
                    </div>
                    <div>
                        <label for="visibility_type"
                            class="block text-xs font-bold text-gray-700 uppercase mb-1">Visibility</label>
                        <select name="visibility_type" id="visibility_type" x-model="visibilityType" required
                            class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-2.5 px-4 bg-white">
                            <option value="all">Visible to All</option>
                            <option value="department">Specific Department</option>
                        </select>
                    </div>
                </div>

                <!-- Dept Selection (Always visible if visibilityType is department) -->
                <div x-show="visibilityType === 'department'"
                    class="animate-in fade-in slide-in-from-top-2 duration-300">
                    <label for="department_name" class="block text-xs font-bold text-gray-700 uppercase mb-1">Select
                        Department</label>
                    @if(auth()->user()->role === 'dept_admin')
                        <div class="p-3 bg-brand-50 rounded-xl border border-brand-100">
                            <p class="text-[10px] font-bold text-brand-700 uppercase tracking-tighter">
                                Restricted to: {{ auth()->user()->department_name }}
                            </p>
                            <input type="hidden" name="department_name" value="{{ auth()->user()->department_name }}">
                        </div>
                    @else
                        <select name="department_name" id="department_name" x-model="selectedDepartment"
                            class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select a Department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Featured Image -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Featured Image</label>
                    <input type="hidden" name="gallery_image_path" :value="selectedGalleryPhoto">

                    <div class="space-y-3">
                        <div x-show="selectedGalleryPhoto"
                            class="relative group aspect-video rounded-xl overflow-hidden border border-gray-200">
                            <img :src="selectedGalleryPhoto ? '{{ asset('storage') }}/' + selectedGalleryPhoto : ''"
                                class="w-full h-full object-cover">
                            <button type="button" @click="selectedGalleryPhoto = ''"
                                class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <label
                                class="flex-1 cursor-pointer bg-gray-50 hover:bg-gray-100 border border-gray-200 border-dashed rounded-xl py-2 px-3 text-center transition-colors">
                                <span class="text-xs font-bold text-gray-600">Upload File</span>
                                <input type="file" name="image" id="image" class="hidden"
                                    @change="selectedGalleryPhoto = ''">
                            </label>
                            <button type="button" @click="openGalleryModal()"
                                class="px-4 py-2 bg-brand-50 text-brand-700 rounded-xl text-xs font-bold border border-brand-100 hover:bg-brand-100 transition-all">
                                Gallery
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Post Gallery (Moved from Right) -->
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase mb-2">Post Gallery</h4>
                    <div class="grid grid-cols-2 gap-3" id="photo-previews">
                        @if(isset($newsEvent) && $newsEvent->photos)
                            @foreach($newsEvent->photos as $photo)
                                <div class="aspect-square rounded-xl overflow-hidden border border-gray-200 relative group">
                                    <img src="{{ asset('storage/' . $photo->image_path) }}" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" class="text-white text-xs font-bold underline">Delete</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <label
                            class="aspect-square rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center cursor-pointer hover:border-brand-500 hover:bg-brand-50 transition-all text-gray-400 hover:text-brand-600">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span class="text-[9px] font-bold uppercase">Add</span>
                            <input type="file" name="photos[]" multiple class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>

                <!-- Author -->
                <div>
                    <label for="author" class="block text-xs font-bold text-gray-700 uppercase mb-1">Author</label>
                    <input type="text" name="author" id="author"
                        value="{{ $newsEvent->author ?? auth()->user()->name }}"
                        class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-brand-500 text-sm py-2 px-4">
                </div>
            </div>

            <!-- Contextual Fields -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5 animate-in fade-in slide-in-from-bottom-4">
                <!-- Event Fields -->
                <template x-if="postType === 'event'">
                    <div class="space-y-4">
                        <h4 class="text-[11px] font-black text-purple-700 uppercase">Event Details</h4>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Date & Time</label>
                            <input type="datetime-local" name="event_date"
                                value="{{ isset($newsEvent->event_date) ? \Carbon\Carbon::parse($newsEvent->event_date)->format('Y-m-d\TH:i') : '' }}"
                                class="w-full border-gray-200 rounded-xl text-sm px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Location</label>
                            <input type="text" name="location" value="{{ $newsEvent->location ?? '' }}"
                                class="w-full border-gray-200 rounded-xl text-sm px-4 py-2">
                        </div>
                        <div class="border-t border-gray-50 pt-3">
                            <label class="text-xs font-bold flex items-center gap-2">
                                <input id="is_pinned" name="is_pinned" type="checkbox" {{ (isset($newsEvent->is_pinned) && $newsEvent->is_pinned) ? 'checked' : '' }} class="rounded text-brand-600">
                                Pin this Event
                            </label>
                        </div>
                    </div>
                </template>

                <!-- Job Fields -->
                <template x-if="postType === 'job'">
                    <div class="space-y-4">
                        <h4 class="text-[11px] font-black text-blue-700 uppercase">Career Details</h4>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Company Name</label>
                            <input type="text" name="job_company" value="{{ $newsEvent->job_company ?? '' }}"
                                class="w-full border-gray-200 rounded-xl text-sm px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Salary Range
                                (Optional)</label>
                            <input type="text" name="job_salary" value="{{ $newsEvent->job_salary ?? '' }}"
                                placeholder="e.g. 30k - 50k"
                                class="w-full border-gray-200 rounded-xl text-sm px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Apply Link /
                                Email</label>
                            <input type="text" name="job_link" value="{{ $newsEvent->job_link ?? '' }}"
                                placeholder="URL or Email" class="w-full border-gray-200 rounded-xl text-sm px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Deadline</label>
                            <input type="date" name="job_deadline"
                                value="{{ isset($newsEvent->job_deadline) ? \Carbon\Carbon::parse($newsEvent->job_deadline)->format('Y-m-d') : '' }}"
                                class="w-full border-gray-200 rounded-xl text-sm px-4 py-2">
                        </div>
                    </div>
                </template>

                <div x-show="postType === 'news' || postType === 'announcement'"
                    class="text-[10px] text-gray-400 font-bold uppercase py-4 text-center border-2 border-dashed border-gray-50 rounded-xl">
                    No additional settings required
                </div>
            </div>

            <!-- Actions (Mobile/Tablet only) -->
            <div class="flex lg:hidden justify-end gap-3 pb-8">
                <button type="button" @click="closeModal()"
                    class="px-8 py-3 bg-gray-100 text-gray-600 rounded-xl text-sm font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                <button type="submit" @click="saving = true"
                    class="px-8 py-3 bg-brand-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-xl shadow-brand-100">
                    <span x-show="!saving">Publish Post</span>
                    <span x-show="saving">Processing...</span>
                </button>
            </div>
        </div>

        <!-- Column 2: Content (8 cols) -->
        <div class="lg:col-span-8 flex flex-col gap-6 lg:h-full lg:overflow-y-auto custom-scrollbar lg:pr-1" x-data='tiptapEditor({ 
                content: {!! json_encode($newsEvent->content ?? "") !!}, 
                placeholder: "Share your story or post details here...",
                inputName: "content"
             })'>

            <div
                class="bg-white rounded-2xl border border-gray-100 shadow-sm flex-1 flex flex-col overflow-hidden h-full min-h-[500px] lg:min-h-0">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center shrink-0">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Post Content</h3>
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex gap-1" id="tiptap-toolbar">
                            <button type="button" @click="toggleHeading(1)"
                                :class="isActive('heading', { level: 1 }) ? 'bg-brand-100 text-brand-700 border-brand-200' : 'hover:bg-gray-200 border-transparent'"
                                class="px-2 py-1 text-[10px] font-black uppercase rounded border transition-all">H1</button>
                            <button type="button" @click="toggleHeading(2)"
                                :class="isActive('heading', { level: 2 }) ? 'bg-brand-100 text-brand-700 border-brand-200' : 'hover:bg-gray-200 border-transparent'"
                                class="px-2 py-1 text-[10px] font-black uppercase rounded border transition-all">H2</button>
                            <button type="button" @click="toggleBold()"
                                :class="isActive('bold') ? 'bg-brand-100 text-brand-700 border-brand-200' : 'hover:bg-gray-200 border-transparent'"
                                class="px-2 py-1 text-[10px] font-black uppercase rounded border transition-all">Bold</button>
                            <button type="button" @click="toggleBulletList()"
                                :class="isActive('bulletList') ? 'bg-brand-100 text-brand-700 border-brand-200' : 'hover:bg-gray-200 border-transparent'"
                                class="px-2 py-1 text-[10px] font-black uppercase rounded border transition-all">Bullets</button>
                            <button type="button" @click="setLink()"
                                :class="isActive('link') ? 'bg-brand-100 text-brand-700 border-brand-200' : 'hover:bg-gray-200 border-transparent'"
                                class="px-2 py-1 text-[10px] font-black uppercase rounded border transition-all">Link</button>
                            <button type="button" @click="clearFormatting()"
                                class="px-2 py-1 text-[10px] font-black uppercase rounded hover:bg-gray-200 transition-all border border-transparent">Clear</button>
                        </div>
                        <div class="flex gap-2">
                            <div class="h-2 w-2 rounded-full bg-red-400"></div>
                            <div class="h-2 w-2 rounded-full bg-yellow-400"></div>
                            <div class="h-2 w-2 rounded-full bg-green-400"></div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 p-0 overflow-hidden relative flex flex-col h-full">
                    <!-- Tiptap Editor Container -->
                    <div x-ref="editor"
                        class="flex-1 overflow-y-auto px-8 py-6 prose prose-sm max-w-none focus:outline-none custom-scrollbar h-full">
                    </div>
                    <!-- Hidden field for actual submission -->
                    <input type="hidden" name="content" x-model="content">
                </div>
            </div>

            <!-- Actions (Desktop Sticky) -->
            <div
                class="hidden lg:flex justify-end gap-3 pb-0 sticky bottom-0 bg-white/0 backdrop-blur-sm p-4 -mx-4 -mb-4 border-t border-gray-100/50">
                <button type="button" @click="closeModal()"
                    class="px-8 py-3 bg-gray-100 text-gray-600 rounded-xl text-sm font-black uppercase tracking-widest hover:bg-gray-200 transition-all shadow-sm">Cancel</button>
                <button type="submit" @click="saving = true"
                    class="px-8 py-3 bg-brand-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-xl shadow-brand-100">
                    <span x-show="!saving">Publish Post</span>
                    <span x-show="saving">Processing...</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Gallery Library Shared Modal Logic -->
    @include('admin.news_events.partials._gallery_modal_markup')
</form>

<style>
    .ProseMirror {
        outline: none;
        min-height: 100%;
    }

    .ProseMirror p.is-editor-empty:first-child::before {
        content: attr(data-placeholder);
        float: left;
        color: #adb5bd;
        pointer-events: none;
        height: 0;
    }

    /* Basic formatting for the editor area */
    .ProseMirror h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .ProseMirror h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .ProseMirror ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .ProseMirror ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .ProseMirror a {
        color: #2563eb;
        text-decoration: underline;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }

    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #94a3b8;
    }
</style>