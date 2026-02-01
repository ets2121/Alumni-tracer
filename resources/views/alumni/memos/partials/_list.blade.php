<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($memos as $memo)
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2 py-1 rounded uppercase">Memo
                    #{{ $memo->memo_number }}</span>
                <span class="text-xs text-gray-400">{{ $memo->date_issued->format('M d, Y') }}</span>
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-4 flex-grow">{{ $memo->title }}</h4>
            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                <a href="{{ asset('storage/' . $memo->file_path) }}" target="_blank"
                    class="flex items-center gap-2 text-brand-600 hover:text-brand-800 font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Document
                </a>
                <a href="{{ asset('storage/' . $memo->file_path) }}" download class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
            </div>
        </div>
    @empty
        <div
            class="col-span-full py-20 text-center text-gray-500 italic bg-white rounded-xl border border-dashed border-gray-200">
            No memorandums found matching your search.
        </div>
    @endforelse
</div>
<div class="mt-8 pagination-container">
    {{ $memos->links() }}
</div>