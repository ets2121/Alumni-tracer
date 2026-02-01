<form id="course-form"
    action="{{ isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.store') }}"
    method="POST">
    @csrf
    @if(isset($course))
        @method('PUT')
    @endif

    <div class="space-y-4">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Course Code</label>
            <input type="text" name="code" id="code" value="{{ $course->code ?? '' }}" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
            <p class="mt-1 text-xs text-red-600 error-message" data-field="code"></p>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Course Name</label>
            <input type="text" name="name" id="name" value="{{ $course->name ?? '' }}" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
            <p class="mt-1 text-xs text-red-600 error-message" data-field="name"></p>
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Course Category</label>
            <select name="category" id="category" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
                <option value="Undergraduate" {{ (isset($course) && $course->category === 'Undergraduate') ? 'selected' : '' }}>Undergraduate programs</option>
                <option value="Graduate" {{ (isset($course) && $course->category === 'Graduate') ? 'selected' : '' }}>
                    Graduate programs</option>
                <option value="Certificate" {{ (isset($course) && $course->category === 'Certificate') ? 'selected' : '' }}>Certificate programs</option>
            </select>
            <p class="mt-1 text-xs text-red-600 error-message" data-field="category"></p>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">{{ $course->description ?? '' }}</textarea>
            <p class="mt-1 text-xs text-red-600 error-message" data-field="description"></p>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" @click="closeModal()"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
            Cancel
        </button>
        <button type="submit"
            class="px-4 py-2 bg-brand-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-brand-700 focus:outline-none flex items-center gap-2">
            <span x-show="!saving">{{ isset($course) ? 'Update Course' : 'Create Course' }}</span>
            <span x-show="saving" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Saving...
            </span>
        </button>
    </div>
</form>