<div
    x-data="{ coverPreview: '{{ isset($gallery) && $gallery->cover_image ? asset('storage/' . $gallery->cover_image) : '' }}', coverFileName: '' }">
    <form id="album-form"
        action="{{ isset($gallery) ? route('admin.gallery.update', $gallery->id) : route('admin.gallery.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($gallery))
            @method('PUT')
        @endif

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Album Name</label>
                    <input type="text" name="name" id="name" value="{{ $gallery->name ?? '' }}" required
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm px-4 py-3">
                    <p class="mt-1 text-xs text-red-600 error-message" data-field="name"></p>
                </div>

                <div>
                    <label for="category" class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                    <select name="category" id="category" required
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm px-4 py-3">
                        <option value="Graduation photos" {{ (isset($gallery) && $gallery->category == 'Graduation photos') ? 'selected' : '' }}>Graduation Photos</option>
                        <option value="Alumni events" {{ (isset($gallery) && $gallery->category == 'Alumni events') ? 'selected' : '' }}>Alumni Events</option>
                        <option value="Reunions" {{ (isset($gallery) && $gallery->category == 'Reunions') ? 'selected' : '' }}>Reunions</option>
                        <option value="Campus activities" {{ (isset($gallery) && $gallery->category == 'Campus activities') ? 'selected' : (isset($gallery) ? '' : 'selected') }}>Campus Activities
                        </option>
                    </select>
                    <p class="mt-1 text-xs text-red-600 error-message" data-field="category"></p>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm px-4 py-3">{{ $gallery->description ?? '' }}</textarea>
                <p class="mt-1 text-xs text-red-600 error-message" data-field="description"></p>
            </div>

            <!-- Interactive Cover Image Upload -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Cover Image</label>
                <input type="file" name="cover_image" id="cover_image" accept="image/*" class="hidden" @change="
                        const file = $event.target.files[0];
                        if (file) {
                            coverFileName = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => coverPreview = e.target.result;
                            reader.readAsDataURL(file);
                        }
                    ">

                <div class="relative">
                    <!-- Upload Area -->
                    <div x-show="!coverPreview" @click="document.getElementById('cover_image').click()"
                        class="cursor-pointer border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-brand-500 hover:bg-brand-50/50 transition-all duration-300 group">
                        <div
                            class="w-16 h-16 bg-brand-50 border-2 border-brand-200 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 group-hover:bg-brand-100 transition-transform">
                            <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900 mb-1">Click to upload cover image</p>
                        <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                    </div>

                    <!-- Image Preview -->
                    <div x-show="coverPreview" class="relative group" style="display: none;" x-cloak>
                        <img :src="coverPreview" class="w-full h-48 object-cover rounded-2xl border-2 border-gray-200">
                        <div
                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center gap-3">
                            <button type="button" @click.stop="document.getElementById('cover_image').click()"
                                class="px-4 py-2 bg-white text-gray-900 rounded-xl text-sm font-bold hover:bg-gray-100 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Change
                            </button>
                            <button type="button"
                                @click.stop="coverPreview = ''; coverFileName = ''; document.getElementById('cover_image').value = ''"
                                class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Remove
                            </button>
                        </div>
                        <p x-show="coverFileName" class="mt-2 text-xs text-gray-600 font-medium" x-text="coverFileName">
                        </p>
                    </div>
                </div>
                <p class="mt-1 text-xs text-red-600 error-message" data-field="cover_image"></p>
            </div>
        </div>

        <!-- Fixed Button Layout with Proper Spacing -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" @click="closeModal()"
                class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 bg-white hover:bg-gray-50 transition-all">
                Cancel
            </button>
            <button type="submit"
                class="px-6 py-3 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-100">
                {{ isset($gallery) ? 'Update Album' : 'Create Album' }}
            </button>
        </div>
    </form>
</div>