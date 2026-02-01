<form id="memo-form" action="{{ isset($memo) ? route('admin.memos.update', $memo->id) : route('admin.memos.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($memo))
        @method('PUT')
    @endif

    <div class="space-y-4">
        <div>
            <label for="memo_number" class="block text-sm font-medium text-gray-700 mb-1">Memorandum Number</label>
            <input type="text" name="memo_number" id="memo_number" value="{{ $memo->memo_number ?? '' }}" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm"
                placeholder="e.g. CMO No. 01 Series of 2024">
            <p class="mt-1 text-xs text-red-600 error-message" data-field="memo_number"></p>
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title / Subject</label>
            <input type="text" name="title" id="title" value="{{ $memo->title ?? '' }}" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
            <p class="mt-1 text-xs text-red-600 error-message" data-field="title"></p>
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category" id="category" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
                <option value="">-- Select Category --</option>
                <option value="Graduate tracer study guidelines" {{ (isset($memo) && $memo->category == 'Graduate tracer study guidelines') ? 'selected' : '' }}>Graduate tracer study guidelines</option>
                <option value="Alumni tracking requirements" {{ (isset($memo) && $memo->category == 'Alumni tracking requirements') ? 'selected' : '' }}>Alumni tracking requirements</option>
                <option value="Institutional policies" {{ (isset($memo) && $memo->category == 'Institutional policies') ? 'selected' : '' }}>Institutional policies</option>
            </select>
            <p class="mt-1 text-xs text-red-600 error-message" data-field="category"></p>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description / Summary</label>
            <textarea name="description" id="description" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">{{ $memo->description ?? '' }}</textarea>
            <p class="mt-1 text-xs text-red-600 error-message" data-field="description"></p>
        </div>

        <div>
            <label for="date_issued" class="block text-sm font-medium text-gray-700 mb-1">Date Issued</label>
            <input type="date" name="date_issued" id="date_issued"
                value="{{ isset($memo->date_issued) ? \Carbon\Carbon::parse($memo->date_issued)->format('Y-m-d') : '' }}"
                required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
            <p class="mt-1 text-xs text-red-600 error-message" data-field="date_issued"></p>
        </div>

        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Document File (PDF, DOC,
                DOCX)</label>
            @if(isset($memo) && $memo->file_path)
                <div class="mb-2 flex items-center gap-2 text-xs text-brand-600 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Current file exists
                </div>
            @endif
            <input type="file" name="file" id="file" {{ isset($memo) ? '' : 'required' }} accept=".pdf,.doc,.docx"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
            <p class="mt-1 text-xs text-red-600 error-message" data-field="file"></p>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="button" @click="closeModal()"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
            Cancel
        </button>
        <button type="submit"
            class="px-4 py-2 bg-brand-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-brand-700 focus:outline-none flex items-center gap-2">
            <span x-show="!saving">{{ isset($memo) ? 'Update Memo' : 'Upload Memo' }}</span>
            <span x-show="saving" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Processing...
            </span>
        </button>
    </div>
</form>