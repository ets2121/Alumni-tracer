<div class="space-y-6">
    <div class="space-y-6">
        <!-- Summary Header for Detail Context -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 text-center sm:text-left">
            <div>
                <h2 class="text-lg font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tighter">
                    Alumni Record Repository</h2>
                <p class="text-[9px] font-bold text-gray-400 dark:text-dark-text-muted uppercase tracking-widest mt-1">
                    Displaying {{ $data->total() }} verified records • Page {{ $data->currentPage() }} of
                    {{ $data->lastPage() }}
                </p>
            </div>
            <div class="flex justify-center sm:justify-end gap-2">
                <span
                    class="px-4 py-1.5 bg-gray-900 dark:bg-dark-bg-subtle text-white dark:text-dark-text-primary rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-gray-100 dark:shadow-none italic">Official
                    Record</span>
            </div>
        </div>

        @if($data->isEmpty())
            <div
                class="bg-white dark:bg-dark-bg-elevated p-16 rounded-[3rem] border border-gray-100 dark:border-dark-border shadow-sm text-center">
                <div
                    class="w-20 h-20 bg-gray-50 dark:bg-dark-bg-subtle text-gray-400 dark:text-dark-text-muted rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-lg font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tighter">No
                    Matches Found</p>
                <p class="text-[10px] font-bold text-gray-400 dark:text-dark-text-muted uppercase tracking-widest mt-2">Try
                    adjusting your filters to broaden your search.</p>
            </div>
        @else
            <div
                class="bg-white dark:bg-dark-bg-elevated rounded-[2.5rem] border border-gray-100 dark:border-dark-border shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-dark-bg-subtle/50 border-b border-gray-100 dark:border-dark-border">
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alumni
                                    Name</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Program
                                </th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batch
                                </th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Employment</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Work
                                    Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-dark-border">
                            @foreach($data as $alumnus)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-dark-state-hover transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="font-black text-gray-900 dark:text-dark-text-primary uppercase text-[11px]">
                                            {{ $alumnus->last_name }}, {{ $alumnus->first_name }} {{ $alumnus->middle_name }}
                                        </div>
                                        <div class="text-[9px] text-gray-400 dark:text-dark-text-muted font-bold uppercase">
                                            {{ $alumnus->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span
                                            class="px-3 py-1 bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 rounded-full text-[9px] font-black uppercase">{{ $alumnus->course->code }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span
                                            class="font-bold text-gray-500 dark:text-dark-text-secondary text-[11px]">{{ $alumnus->batch_year }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-1.5 h-1.5 rounded-full {{ $alumnus->employment_status === 'Employed' ? 'bg-green-500' : 'bg-red-500' }}">
                                            </div>
                                            <span
                                                class="font-black text-gray-900 dark:text-dark-text-primary uppercase text-[10px]">{{ $alumnus->employment_status }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($alumnus->employment_status === 'Employed')
                                            <div class="text-[10px] font-bold text-gray-900 dark:text-dark-text-primary uppercase">
                                                {{ $alumnus->position ?? 'N/A' }}</div>
                                            <div class="text-[9px] text-gray-400 dark:text-dark-text-muted uppercase font-bold">
                                                {{ $alumnus->company_name ?? '-' }}</div>
                                        @else
                                            <span class="text-[9px] text-gray-300 font-black uppercase italic">No active
                                                record</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Classic Pagination Section -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 px-2">
                <div class="text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-widest text-center sm:text-left">
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

                    @if($data->hasMorePages())
                        <button @click="changePage({{ $data->currentPage() + 1 }})"
                            class="px-4 py-2 bg-gray-900 dark:bg-dark-bg-subtle text-white dark:text-dark-text-primary hover:bg-brand-600 rounded-xl text-[10px] font-black uppercase transition-all shadow-xl shadow-gray-200 dark:shadow-none shrink-0">Next</button>
                    @else
                        <button
                            class="px-4 py-2 bg-gray-100 dark:bg-dark-bg-subtle text-gray-300 dark:text-dark-text-disabled rounded-xl text-[10px] font-black uppercase cursor-not-allowed shrink-0">Next</button>
                    @endif
                </div>
            </div>
        @endif
    </div>