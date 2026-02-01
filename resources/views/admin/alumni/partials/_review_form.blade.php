<form id="alumni-form" action="{{ route('admin.alumni.update', $alumni->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Profile Data Section -->
        <div class="space-y-6">
            <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] border-b pb-3">Registrant Profile</h4>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1">Full Name</label>
                    <input type="text" name="name" value="{{ $alumni->name }}" required
                        class="w-full border-gray-100 rounded-[1.25rem] focus:ring-brand-500 focus:border-brand-500 text-sm py-4 px-5 bg-gray-50/50 font-bold text-gray-800 shadow-inner">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1">Email Connection</label>
                    <input type="email" name="email" value="{{ $alumni->email }}" required
                        class="w-full border-gray-100 rounded-[1.25rem] focus:ring-brand-500 focus:border-brand-500 text-sm py-4 px-5 bg-gray-50/50 font-bold text-gray-800 shadow-inner">
                </div>
            </div>

            @if($alumni->alumniProfile)
            <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-3">
                    <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Academic Program</p>
                        <p class="text-xs font-black text-gray-900">{{ $alumni->alumniProfile->course->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400 font-bold text-[10px] uppercase">Graduation Batch</span>
                    <span class="px-3 py-1 bg-brand-50 text-brand-700 rounded-full font-black text-[10px]">{{ $alumni->alumniProfile->batch_year }}</span>
                </div>

                <div class="pt-4 border-t border-gray-50 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Employment</span>
                        <span class="text-xs font-black text-gray-700">{{ $alumni->alumniProfile->employment_status }}</span>
                    </div>
                    @if($alumni->alumniProfile->field_of_work)
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Field of Work</span>
                        <span class="text-xs font-black text-gray-700">{{ $alumni->alumniProfile->field_of_work }}</span>
                    </div>
                    @endif
                    @if($alumni->alumniProfile->work_status)
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Status</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md font-bold text-[9px] uppercase">{{ $alumni->alumniProfile->work_status }}</span>
                    </div>
                    @endif
                    @if($alumni->alumniProfile->establishment_type)
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Sector</span>
                        <span class="text-xs font-bold text-gray-600">{{ $alumni->alumniProfile->establishment_type }}</span>
                    </div>
                    @endif
                    @if($alumni->alumniProfile->work_location)
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Location</span>
                        <span class="flex items-center gap-1 text-xs font-bold text-gray-600">
                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            {{ $alumni->alumniProfile->work_location }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Verification & Decision Section -->
        <div class="space-y-6">
            <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] border-b pb-3">Verification Suite</h4>

            @if($alumni->alumniProfile && $alumni->alumniProfile->proof_path)
            <div class="group relative bg-gray-900 rounded-[1.5rem] overflow-hidden aspect-video shadow-2xl border-4 border-gray-100">
                <img src="{{ asset('storage/' . $alumni->alumniProfile->proof_path) }}" alt="Verification Proof" class="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                    <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl mb-4 border border-white/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <a href="{{ asset('storage/' . $alumni->alumniProfile->proof_path) }}" target="_blank" 
                        class="bg-white text-gray-900 px-6 py-2.5 rounded-xl text-xs font-black shadow-xl hover:bg-gray-100 transition-all uppercase tracking-widest">Open High-Res Proof</a>
                </div>
            </div>
            @else
            <div class="bg-amber-50 rounded-[1.5rem] p-8 border-2 border-dashed border-amber-200 text-center">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <p class="text-sm font-black text-amber-700">Missing Documents</p>
                <p class="text-[10px] text-amber-600/70 uppercase font-bold mt-1">Contact registrant for proof</p>
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Decision Remarks</label>
                    <textarea name="admin_remarks" rows="3" placeholder="Explain your approval or rejection reason..."
                        class="w-full border-gray-100 rounded-[1.25rem] focus:ring-brand-500 focus:border-brand-500 text-sm py-4 px-5 bg-gray-50/50 font-medium text-gray-800 shadow-inner resize-none">{{ $alumni->admin_remarks }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Application Status</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-[1.25rem] cursor-pointer transition-all hover:border-green-200"
                            :class="status === 'active' ? 'border-green-500 bg-green-50 shadow-md ring-2 ring-green-500/10' : 'border-gray-100 bg-gray-50/30'"
                            x-data="{ status: '{{ $alumni->status }}' }" @click="status = 'active'">
                            <input type="radio" name="status" value="active" {{ $alumni->status === 'active' ? 'checked' : '' }} class="hidden">
                            <div class="text-center">
                                <div class="text-xs font-black uppercase tracking-widest" :class="status === 'active' ? 'text-green-700' : 'text-gray-400'">Approve</div>
                                <div class="text-[9px] font-bold" :class="status === 'active' ? 'text-green-600/70' : 'text-gray-300'">Grant access</div>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-[1.25rem] cursor-pointer transition-all hover:border-red-200"
                            :class="status === 'rejected' ? 'border-red-500 bg-red-50 shadow-md ring-2 ring-red-500/10' : 'border-gray-100 bg-gray-50/30'"
                            x-data="{ status: '{{ $alumni->status }}' }" @click="status = 'rejected'">
                            <input type="radio" name="status" value="rejected" {{ $alumni->status === 'rejected' ? 'checked' : '' }} class="hidden">
                            <div class="text-center">
                                <div class="text-xs font-black uppercase tracking-widest" :class="status === 'rejected' ? 'text-red-700' : 'text-gray-400'">Reject</div>
                                <div class="text-[9px] font-bold" :class="status === 'rejected' ? 'text-red-600/70' : 'text-gray-300'">Deny request</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
        <button type="button" @click="closeModal()"
            class="px-8 py-4 bg-gray-100 text-gray-600 rounded-[1.25rem] text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel Review</button>
        <button type="submit"
            class="px-10 py-4 bg-brand-600 text-white rounded-[1.25rem] text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-brand-100 hover:bg-brand-700 hover:-translate-y-1 transition-all flex items-center gap-3">
            <span x-show="!saving">Finalize Decision</span>
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
<script>
    // Alpine.js helper for radio selections if needed, 
    // but the inline x-data handles visual toggling.
</script>