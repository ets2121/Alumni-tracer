<x-layouts.admin>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Edit CHED Memorandum</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('admin.memos.update', $memo) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Memo Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $memo->title) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="memo_number" class="block text-sm font-medium text-gray-700">Memo Number</label>
                        <input type="text" name="memo_number" id="memo_number"
                            value="{{ old('memo_number', $memo->memo_number) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                        @error('memo_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="date_issued" class="block text-sm font-medium text-gray-700">Date Issued</label>
                        <input type="date" name="date_issued" id="date_issued"
                            value="{{ old('date_issued', $memo->date_issued->format('Y-m-d')) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                        @error('date_issued') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Update Document (Leave blank
                        to keep current)</label>

                    <div
                        class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-xs text-gray-600 truncate max-w-[200px]">Current File:
                                {{ basename($memo->file_path) }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $memo->file_path) }}" target="_blank"
                            class="text-xs font-bold text-brand-600 hover:text-brand-800">Preview</a>
                    </div>

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
                                <label for="file"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-brand-600 hover:text-brand-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-500">
                                    <span>Upload a new file</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">Accepted: PDF, DOCX (Max 5MB)</p>
                        </div>
                    </div>
                    @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50">
                    <a href="{{ route('admin.memos.index') }}"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors">
                        Update Memorandum
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>