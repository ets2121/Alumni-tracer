<x-layouts.admin>
    <x-slot name="header">
        Edit Alumni: {{ $alumni->name }}
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-2xl mx-auto">
        <div class="p-6 text-gray-900">
            <form action="{{ route('admin.alumni.update', $alumni->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $alumni->name) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $alumni->email) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                @if($alumni->alumniProfile)
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3 mb-6">
                        <h4 class="font-bold text-gray-800 border-b pb-1">Profile Details</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <p><span class="text-gray-500">Course:</span>
                                {{ $alumni->alumniProfile->course->code ?? 'N/A' }}</p>
                            <p><span class="text-gray-500">Batch:</span> {{ $alumni->alumniProfile->batch_year ?? 'N/A' }}
                            </p>
                            <p><span class="text-gray-500">Employment:</span>
                                {{ $alumni->alumniProfile->employment_status ?? 'N/A' }}</p>
                            <p><span class="text-gray-500">Contact:</span>
                                {{ $alumni->alumniProfile->contact_number ?? 'N/A' }}</p>
                        </div>

                        @if($alumni->alumniProfile->proof_path)
                            <div class="mt-4 pt-3 border-t">
                                <p class="text-sm font-medium text-gray-700 mb-2">Verification Proof:</p>
                                <a href="{{ asset('storage/' . $alumni->alumniProfile->proof_path) }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-800 font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    View Document
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-amber-600 mt-2 font-medium italic">No verification proof uploaded yet.</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-6 italic">This user has not set up their profile yet.</p>
                @endif

                @if($alumni->employmentHistories->count() > 0)
                    <div class="bg-indigo-50/50 p-4 rounded-lg space-y-3 mb-6 border border-indigo-100">
                        <h4 class="font-bold text-indigo-900 border-b border-indigo-200 pb-1">Work History</h4>
                        <div class="space-y-3">
                            @foreach($alumni->employmentHistories as $history)
                                <div class="text-sm">
                                    <div class="font-bold text-gray-900">{{ $history->position }}</div>
                                    <div class="text-xs text-brand-600 font-medium">{{ $history->company_name }}</div>
                                    <div class="text-[10px] text-gray-500 italic">
                                        {{ $history->start_date->format('M Y') }} -
                                        {{ $history->is_current ? 'Present' : ($history->end_date ? $history->end_date->format('M Y') : 'N/A') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status (Approval)</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                        <option value="pending" {{ $alumni->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ $alumni->status == 'active' ? 'selected' : '' }}>Active (Approved)
                        </option>
                        <option value="rejected" {{ $alumni->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.alumni.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        Update Alumni
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>