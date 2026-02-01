<div class="space-y-8 p-4">
    <div class="bg-brand-50 rounded-3xl p-6 border border-brand-100 flex items-start gap-4">
        <div class="p-3 bg-white rounded-2xl shadow-sm">
            <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.167H3.38a1.745 1.745 0 01-1.457-2.711l1.547-2.166a1.723 1.723 0 011.121-.632L11 5.882zM11 5.882c.35-.022.724.019 1 .118.475.17.888.477 1.177.876L16.273 11c.238.328.39.706.441 1.103.053.458.044.891-.026 1.285L15 15.391a2.01 2.01 0 01-1.574.882c-.183.003-.366-.02-.545-.068l-1.881-.512M11 5.882c0-.183.021-.366.068-.545L12 3m6 10l2 2m-2-4l2-2" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-black text-brand-900 uppercase tracking-widest leading-none mb-1">Broadcasting Event
            </h4>
            <p class="text-lg font-bold text-brand-600">{{ $news_event->title }}</p>
            <p class="text-xs text-brand-400 mt-2 line-clamp-2">{{ Str::limit(strip_tags($news_event->content), 120) }}
            </p>
        </div>
    </div>

    <form id="news-form" action="{{ route('admin.news_events.broadcast', $news_event->id) }}" method="POST"
        class="space-y-6">
        @csrf

        <div x-data="{ 
            targetType: '{{ $news_event->target_type ?? 'all' }}',
            targetBatch: '{{ $news_event->target_batch ?? '' }}',
            targetCourseId: '{{ $news_event->target_course_id ?? '' }}'
        }" class="space-y-6">

            <div class="space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Target
                    Audience</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="target_type" value="all" x-model="targetType" class="peer sr-only">
                        <div
                            class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-brand-500 peer-checked:bg-brand-50 transition-all text-center">
                            <span
                                class="block text-[10px] font-black uppercase text-gray-500 peer-checked:text-brand-600">All
                                Alumni</span>
                        </div>
                    </label>
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="target_type" value="batch" x-model="targetType" class="peer sr-only">
                        <div
                            class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-brand-500 peer-checked:bg-brand-50 transition-all text-center">
                            <span
                                class="block text-[10px] font-black uppercase text-gray-500 peer-checked:text-brand-600">Specific
                                Batch</span>
                        </div>
                    </label>
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="target_type" value="course" x-model="targetType" class="peer sr-only">
                        <div
                            class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-brand-500 peer-checked:bg-brand-50 transition-all text-center">
                            <span
                                class="block text-[10px] font-black uppercase text-gray-500 peer-checked:text-brand-600">Specific
                                Course</span>
                        </div>
                    </label>
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="target_type" value="batch_course" x-model="targetType"
                            class="peer sr-only">
                        <div
                            class="p-4 rounded-2xl border-2 border-gray-100 bg-white peer-checked:border-brand-500 peer-checked:bg-brand-50 transition-all text-center">
                            <span
                                class="block text-[10px] font-black uppercase text-gray-500 peer-checked:text-brand-600">Batch
                                & Course</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Batch Selection -->
                <div x-show="targetType === 'batch' || targetType === 'batch_course'"
                    x-transition:enter="duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Select
                        Graduation Year</label>
                    <select name="target_batch" x-model="targetBatch"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-brand-500/20 focus:ring-4 focus:ring-brand-500/5 font-bold text-gray-700 shadow-inner">
                        <option value="">Choose Year...</option>
                        @for($year = date('Y'); $year >= 1970; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Course Selection -->
                <div x-show="targetType === 'course' || targetType === 'batch_course'"
                    x-transition:enter="duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Select
                        Program/Course</label>
                    <select name="target_course_id" x-model="targetCourseId"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-brand-500/20 focus:ring-4 focus:ring-brand-500/5 font-bold text-gray-700 shadow-inner">
                        <option value="">Choose Course...</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100">
                <p class="text-[10px] text-amber-700 font-bold uppercase tracking-widest leading-relaxed">
                    <span class="inline-block p-1 bg-amber-200 rounded-md mr-1 uppercase">Warning:</span>
                    This will send automated invitations to the selected alumni group via email and system
                    notifications. This action is processed in the background.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="closeModal()"
                    class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-gray-900 transition-colors">
                    Discard
                </button>
                <button type="submit" :disabled="saving"
                    class="group px-12 py-4 bg-gray-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl flex items-center gap-3 disabled:opacity-50">
                    <span x-show="!saving">Execute Broadcast</span>
                    <span x-show="saving">Processing...</span>
                    <svg x-show="!saving" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>