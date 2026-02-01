<x-layouts.admin>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Edit Album: {{ $gallery->name }}</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('admin.gallery.update', $gallery) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Album Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $gallery->name) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">{{ old('description', $gallery->description) }}</textarea>
                    @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>

                    @if($gallery->cover_image)
                        <div class="mb-4 relative w-48 h-32 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                            <img src="{{ asset('storage/' . $gallery->cover_image) }}" alt="Current Cover"
                                class="w-full h-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center text-white text-xs font-bold uppercase tracking-wider">
                                Current Cover</div>
                        </div>
                    @endif

                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-brand-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="cover_image"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-brand-600 hover:text-brand-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-500">
                                    <span>Upload a new file</span>
                                    <input id="cover_image" name="cover_image" type="file" class="sr-only"
                                        accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">Leave blank to keep current</p>
                        </div>
                    </div>
                    @error('cover_image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50">
                    <a href="{{ route('admin.gallery.index') }}"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors">
                        Update Album
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>