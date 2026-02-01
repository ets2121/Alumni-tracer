<div class="space-y-8 animate-fade-in">
    <!-- Header Section: Avatar & Basic Info -->
    <div class="flex flex-col md:flex-row items-center gap-8 bg-brand-50/50 p-8 rounded-[2rem] border border-brand-100">
        <div class="relative">
            @if($alumni->avatar)
                <img src="{{ asset('storage/' . $alumni->avatar) }}" alt="{{ $alumni->name }}"
                    class="w-32 h-32 rounded-3xl object-cover shadow-2xl border-4 border-white">
            @else
                <div
                    class="w-32 h-32 rounded-3xl bg-white flex items-center justify-center text-brand-200 shadow-xl border-4 border-white">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
            @endif
            <div
                class="absolute -bottom-2 -right-2 bg-green-500 text-white p-1.5 rounded-xl shadow-lg border-2 border-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <div class="text-center md:text-left">
            <h4 class="text-3xl font-black text-gray-900 tracking-tight mb-1">
                {{ $alumni->alumniProfile->first_name ?? '' }} {{ $alumni->alumniProfile->middle_name ?? '' }}
                {{ $alumni->alumniProfile->last_name ?? $alumni->name }}
            </h4>
            <p class="text-brand-600 font-bold uppercase tracking-widest text-sm mb-4">
                {{ $alumni->alumniProfile->course->name ?? 'Course Not Specified' }}
            </p>
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                <span
                    class="px-4 py-1.5 bg-white rounded-full text-xs font-bold text-gray-600 shadow-sm border border-gray-100">Batch
                    {{ $alumni->alumniProfile->batch_year ?? 'N/A' }}</span>
                <span
                    class="px-4 py-1.5 bg-green-100 rounded-full text-xs font-bold text-green-700 shadow-sm border border-green-200 uppercase tracking-tighter">Verified
                    Alumni</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- PERSONAL INFORMATION -->
        <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6 border-b pb-4">
                <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h5 class="font-black text-gray-900 uppercase tracking-tight">Personal Details</h5>
            </div>

            <dl class="space-y-4">
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase">Gender</dt>
                    <dd class="text-sm font-bold text-gray-800">{{ ucfirst($alumni->alumniProfile->gender ?? 'N/A') }}
                    </dd>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase">Date of Birth</dt>
                    <dd class="text-sm font-bold text-gray-800">{{ $alumni->alumniProfile->dob ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase">Civil Status</dt>
                    <dd class="text-sm font-bold text-gray-800">
                        {{ ucfirst($alumni->alumniProfile->civil_status ?? 'N/A') }}
                    </dd>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase">Contact</dt>
                    <dd class="text-sm font-bold text-gray-800">{{ $alumni->alumniProfile->contact_number ?? 'N/A' }}
                    </dd>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase">Email</dt>
                    <dd class="text-sm font-bold text-brand-600">{{ $alumni->email }}</dd>
                </div>
                <div class="pt-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Address</dt>
                    <dd class="text-sm font-medium text-gray-800">{{ $alumni->alumniProfile->address ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- EMPLOYMENT & EDUCATION -->
        <div class="space-y-8">
            <!-- EDUCATIONAL BACKGROUND -->
            <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-6 border-b pb-4">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <h5 class="font-black text-gray-900 uppercase tracking-tight">Education</h5>
                </div>

                <dl class="space-y-2">
                    <dt class="text-sm font-black text-gray-800">
                        {{ $alumni->alumniProfile->course->name ?? 'Course N/A' }}
                    </dt>
                    <dd class="text-xs font-bold text-brand-600 uppercase">Class of
                        {{ $alumni->alumniProfile->batch_year ?? 'N/A' }}
                    </dd>
                    <dd class="text-xs font-medium text-gray-500">Mindanao State University - Main Campus</dd>
                </dl>
            </div>

            <!-- EMPLOYMENT INFORMATION -->
            <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-6 border-b pb-4">
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h5 class="font-black text-gray-900 uppercase tracking-tight">Employment</h5>
                </div>

                <dl class="space-y-4">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <dt class="text-xs font-bold text-gray-400 uppercase">Status</dt>
                        <dd class="text-sm font-bold text-purple-600 uppercase">
                            {{ $alumni->alumniProfile->employment_status ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="pt-1">
                        <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Company</dt>
                        <dd class="text-sm font-bold text-gray-800">{{ $alumni->alumniProfile->company_name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Position</dt>
                        <dd class="text-sm font-bold text-gray-800">{{ $alumni->alumniProfile->position ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Work Address</dt>
                        <dd class="text-sm font-medium text-gray-600">
                            {{ $alumni->alumniProfile->work_address ?? 'N/A' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- PROFESSIONAL WORK HISTORY -->
    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-6 border-b pb-4">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h5 class="font-black text-gray-900 uppercase tracking-tight">Professional Work History</h5>
        </div>

        <div class="space-y-4">
            @forelse($alumni->employmentHistories as $history)
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex justify-between items-start">
                    <div>
                        <h6 class="text-sm font-black text-gray-900">{{ $history->position }}</h6>
                        <p class="text-xs font-bold text-brand-600 uppercase">{{ $history->company_name }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                {{ $history->start_date->format('M Y') }} -
                                {{ $history->is_current ? 'PRESENT' : ($history->end_date ? $history->end_date->format('M Y') : 'N/A') }}
                            </span>
                            @if($history->is_current)
                                <span
                                    class="px-2 py-0.5 bg-green-100 text-green-700 text-[9px] font-black rounded-full uppercase">Current</span>
                            @endif
                        </div>
                    </div>
                    @if($history->location)
                        <div class="text-[10px] font-bold text-gray-400 text-right uppercase">
                            <svg class="w-3 h-3 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $history->location }}
                        </div>
                    @endif
                </div>
            @empty
                <div
                    class="text-center py-4 text-gray-400 text-xs italic bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    No professional history records found.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="flex justify-end gap-3 pt-6 border-t">
        <button @click="closeModal()"
            class="px-8 py-3 bg-gray-100 text-gray-600 rounded-2xl text-sm font-bold hover:bg-gray-200 transition-all">Close
            Profile</button>
        <button @click="openModal('{{ route('admin.alumni.edit', $alumni->id) }}', 'Edit Alumni Record')"
            class="px-8 py-3 bg-brand-600 text-white rounded-2xl text-sm font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-100">Update
            Info</button>
    </div>
</div>