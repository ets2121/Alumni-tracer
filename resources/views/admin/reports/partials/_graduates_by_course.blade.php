<div class="space-y-8">
    <div class="border-b dark:border-dark-border pb-6">
        <h2 class="text-xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest">Graduates by
            Course and Year</h2>
        <p class="text-xs text-gray-500 dark:text-dark-text-secondary font-bold mt-1">
            Displaying {{ $data->firstItem() }}-{{ $data->lastItem() }} of {{ $data->total() }} Records
        </p>
    </div>

    @if(!$data->isEmpty())
        <!-- Top Pagination Quick Access -->
        <div
            class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 mb-6 bg-brand-50/50 dark:bg-brand-900/10 rounded-[1.5rem] border border-brand-100 dark:border-brand-900/30 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></div>
                <span class="text-[10px] font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest">
                    Page {{ $data->currentPage() }} of {{ $data->lastPage() }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                @if($data->onFirstPage())
                    <button
                        class="px-4 py-2 bg-white dark:bg-dark-bg-subtle text-gray-300 dark:text-dark-text-disabled rounded-xl text-[9px] font-black uppercase cursor-not-allowed border border-gray-100 dark:border-dark-border">Prev</button>
                @else
                    <button @click="changePage({{ $data->currentPage() - 1 }})"
                        class="px-4 py-2 bg-white dark:bg-dark-bg-subtle border border-gray-200 dark:border-dark-border text-gray-700 dark:text-dark-text-primary hover:bg-gray-50 dark:hover:bg-dark-state-hover rounded-xl text-[9px] font-black uppercase transition-all shadow-sm">Prev</button>
                @endif

                @if($data->hasMorePages())
                    <button @click="changePage({{ $data->currentPage() + 1 }})"
                        class="px-6 py-2 bg-brand-600 text-white hover:bg-brand-700 rounded-xl text-[9px] font-black uppercase transition-all shadow-lg shadow-brand-100 flex items-center gap-2 group">
                        Next Page
                        <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <button
                        class="px-6 py-2 bg-gray-100 dark:bg-dark-bg-subtle text-gray-400 dark:text-dark-text-disabled rounded-xl text-[9px] font-black uppercase cursor-not-allowed border border-gray-200 dark:border-dark-border">End
                        of List</button>
                @endif
            </div>
        </div>
    @endif

    <div
        class="bg-white dark:bg-dark-bg-elevated rounded-[2.5rem] border border-gray-100 dark:border-dark-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-dark-bg-subtle/50 border-b-2 border-gray-200 dark:border-dark-border">
                        <th
                            class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                            Course Code</th>
                        <th
                            class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                            Batch Year</th>
                        <th
                            class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                            Alumni Name</th>
                        <th
                            class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                            Contact</th>
                        <th
                            class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                            Employment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
                    @php $currentCourse = null; @endphp
                    @foreach($data as $profile)
                        @if($currentCourse !== $profile->course_id)
                            <tr class="bg-brand-50/30 dark:bg-brand-900/10">
                                <td colspan="5"
                                    class="px-4 py-2 text-xs font-black text-brand-700 dark:text-brand-300 uppercase tracking-tighter">
                                    {{ $profile->course->name }} ({{ $profile->course->code }})
                                </td>
                            </tr>
                            @php $currentCourse = $profile->course_id; @endphp
                        @endif
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-dark-state-hover transition-colors">
                            <td
                                class="px-4 py-4 text-[11px] font-bold text-gray-500 dark:text-dark-text-secondary uppercase">
                                {{ $profile->course->code }}
                            </td>
                            <td class="px-4 py-4 text-[11px] font-black text-gray-900 dark:text-dark-text-primary">
                                {{ $profile->batch_year }}
                            </td>
                            <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-dark-text-primary">
                                {{ $profile->first_name }} {{ $profile->last_name }}
                            </td>
                            <td class="px-4 py-4 text-[11px] text-gray-500 dark:text-dark-text-muted">
                                {{ $profile->user->email }}<br>{{ $profile->contact_number }}
                            </td>
                            <td class="px-4 py-4">
                                <span
                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $profile->employment_status === 'Employed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                    {{ $profile->employment_status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Classic Pagination Section -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2 mt-4">
        <div
            class="text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-widest text-center sm:text-left">
            Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} alumni
        </div>
        <div class="flex gap-2 w-full sm:w-auto overflow-x-auto justify-center">
            @if($data->onFirstPage())
                <button
                    class="px-4 py-2 bg-gray-50 dark:bg-dark-bg-subtle text-gray-300 dark:text-dark-text-disabled rounded-xl text-[10px] font-black uppercase cursor-not-allowed shrink-0">Previous</button>
            @else
                <button @click="changePage({{ $data->currentPage() - 1 }})"
                    class="px-4 py-2 bg-white dark:bg-dark-bg-subtle border border-gray-100 dark:border-dark-border text-gray-600 dark:text-dark-text-secondary hover:bg-gray-50 dark:hover:bg-dark-state-hover rounded-xl text-[10px] font-black uppercase transition-all shadow-sm shrink-0">Previous</button>
            @endif

            <div
                class="flex items-center px-4 text-[10px] font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest">
                Page {{ $data->currentPage() }} / {{ $data->lastPage() }}
            </div>

            @if($data->hasMorePages())
                <button @click="changePage({{ $data->currentPage() + 1 }})"
                    class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-[10px] font-black uppercase transition-all shadow-xl shadow-brand-100 shrink-0 flex items-center gap-2">
                    <span>Next Page</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button
                    class="px-6 py-2 bg-gray-100 dark:bg-dark-bg-subtle text-gray-400 dark:text-dark-text-disabled rounded-xl text-[10px] font-black uppercase cursor-not-allowed shrink-0 border border-gray-200 dark:border-dark-border">Next
                    Page</button>
            @endif
        </div>
    </div>
</div>