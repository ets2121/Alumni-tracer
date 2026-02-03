<div class="space-y-6">
<div class="space-y-6">
    <!-- Summary Header for Detail Context -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Alumni Record Repository</h2>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                Displaying {{ $data->total() }} verified records • Page {{ $data->currentPage() }} of {{ $data->lastPage() }}
            </p>
        </div>
        <div class="flex gap-2">
            <span class="px-4 py-1.5 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-gray-100 italic">Official Record</span>
        </div>
    </div>

    @if($data->isEmpty())
        <div class="bg-white p-16 rounded-[3rem] border border-gray-100 shadow-sm text-center">
            <div class="w-20 h-20 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <p class="text-lg font-black text-gray-900 uppercase tracking-tighter">No Matches Found</p>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Try adjusting your filters to broaden your search.</p>
        </div>
    @else
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alumni Name</th>
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Program</th>
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batch</th>
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Employment</th>
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Work Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($data as $alumnus)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="font-black text-gray-900 uppercase text-[11px]">{{ $alumnus->last_name }}, {{ $alumnus->first_name }} {{ $alumnus->middle_name }}</div>
                                    <div class="text-[9px] text-gray-400 font-bold uppercase">{{ $alumnus->user->email }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 bg-brand-50 text-brand-700 rounded-full text-[9px] font-black uppercase">{{ $alumnus->course->code }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="font-bold text-gray-500 text-[11px]">{{ $alumnus->batch_year }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $alumnus->employment_status === 'Employed' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                        <span class="font-black text-gray-900 uppercase text-[10px]">{{ $alumnus->employment_status }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($alumnus->employment_status === 'Employed')
                                        <div class="text-[10px] font-bold text-gray-900 uppercase">{{ $alumnus->position ?? 'N/A' }}</div>
                                        <div class="text-[9px] text-gray-400 uppercase font-bold">{{ $alumnus->company_name ?? '-' }}</div>
                                    @else
                                        <span class="text-[9px] text-gray-300 font-black uppercase italic">No active record</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Classic Pagination Section -->
        <div class="mt-8 flex items-center justify-between px-2">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} alumni
            </div>
            <div class="flex gap-2">
                @if($data->onFirstPage())
                    <button class="px-4 py-2 bg-gray-50 text-gray-300 rounded-xl text-[10px] font-black uppercase cursor-not-allowed">Previous</button>
                @else
                    <button @click="changePage({{ $data->currentPage() - 1 }})" class="px-4 py-2 bg-white border border-gray-100 text-gray-600 hover:bg-gray-50 rounded-xl text-[10px] font-black uppercase transition-all shadow-sm">Previous</button>
                @endif

                @if($data->hasMorePages())
                    <button @click="changePage({{ $data->currentPage() + 1 }})" class="px-4 py-2 bg-gray-900 text-white hover:bg-brand-600 rounded-xl text-[10px] font-black uppercase transition-all shadow-xl shadow-gray-200">Next</button>
                @else
                    <button class="px-4 py-2 bg-gray-100 text-gray-300 rounded-xl text-[10px] font-black uppercase cursor-not-allowed">Next</button>
                @endif
            </div>
        </div>
    @endif
</div>
