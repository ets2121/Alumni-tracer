<form id="chat-group-form"
    action="{{ isset($group) ? route('admin.chat-management.update', $group) : route('admin.chat-management.store') }}"
    method="POST" class="space-y-5">
    @csrf
    @if(isset($group)) @method('PUT') @endif

    <div class="space-y-5"
        x-data="{ type: '{{ $group->type ?? (auth()->user()->role === 'admin' ? 'admin_dept' : 'general') }}' }">
        <!-- Name -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Room
                Identification</label>
            <input type="text" name="name" required value="{{ $group->name ?? '' }}" placeholder="e.g., BSIT Batch 2020"
                class="w-full px-5 py-4 rounded-[20px] bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all font-bold text-gray-900 shadow-sm">
        </div>

        <div class="grid grid-cols-1 gap-5">
            <!-- Type -->
            <div>
                <label
                    class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Classification</label>
                <select name="type" required x-model="type"
                    class="w-full px-5 py-4 rounded-[20px] bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all font-bold text-gray-900 shadow-sm">
                    @if(auth()->user()->role === 'admin')
                        <option value="admin_dept">🏢 Admin Department Only (Staff Coordination)</option>
                        <option value="batch">🎓 Batch-Based Group (Global/Admins + Batch)</option>
                        <option value="course">📚 Course-Based Group (Global/Admins + Course)</option>
                    @else
                        <option value="general">🏛️ Department Name Only ({{ auth()->user()->department_name }})</option>
                        <option value="batch">🎓 Batch-Based ({{ auth()->user()->department_name }} Scoped)</option>
                        <option value="course">📚 Course-Based ({{ auth()->user()->department_name }} Scoped)</option>
                    @endif
                </select>
            </div>
        </div>

        <!-- Dynamic Fields -->
        <div x-show="type === 'batch'" x-transition x-cloak
            class="bg-brand-50/50 p-5 rounded-[24px] border border-brand-100/50">
            <label class="block text-[10px] font-black text-brand-600 uppercase tracking-[0.2em] mb-2 px-1">Specific
                Batch Year</label>
            <select name="batch_year" :required="type === 'batch'"
                class="w-full px-5 py-4 rounded-[16px] bg-white border-transparent focus:ring-2 focus:ring-brand-500 transition-all font-bold text-gray-900 shadow-sm">
                <option value="">-- Select Registered Year --</option>
                @foreach($batchYears as $year)
                    <option value="{{ $year }}" {{ (isset($group) && $group->batch_year == $year) ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
            <p class="mt-2 text-[10px] text-brand-400 font-bold uppercase tracking-widest px-1 flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                </svg>
                Automatically pulled from registered alumni
            </p>
        </div>

        <div x-show="type === 'course'" x-transition x-cloak
            class="bg-purple-50/50 p-5 rounded-[24px] border border-purple-100/50">
            <label class="block text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] mb-2 px-1">Selected
                Course Major</label>
            <select name="course_id" :required="type === 'course'"
                class="w-full px-5 py-4 rounded-[16px] bg-white border-transparent focus:ring-2 focus:ring-purple-500 transition-all font-bold text-gray-900 shadow-sm">
                <option value="">-- Select Academic Course --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ (isset($group) && $group->course_id == $course->id) ? 'selected' : '' }}>{{ $course->code }} - {{ $course->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Room
                Description</label>
            <textarea name="description" rows="3" placeholder="What should people talk about here?"
                class="w-full px-5 py-4 rounded-[20px] bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all font-bold text-gray-900 shadow-sm">{{ $group->description ?? '' }}</textarea>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.history.back()"
            class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
            Cancel
        </button>
        <button type="submit"
            class="px-10 py-4 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-xl shadow-brand-100 transform active:scale-95">
            {{ isset($group) ? 'Update Room' : 'Confirm & Create' }}
        </button>
    </div>
</form>