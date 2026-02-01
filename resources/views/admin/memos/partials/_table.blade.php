<div class="overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Memo Details</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Category / Description</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                    Date Issued</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($memos as $memo)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900">{{ $memo->memo_number }}</span>
                            <span class="text-gray-600 font-medium">{{ $memo->title }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex flex-col gap-1">
                            <span
                                class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full w-fit">
                                {{ $memo->category }}
                            </span>
                            @if($memo->description)
                                <p class="text-gray-500 text-xs mt-1">{{ Str::limit($memo->description, 80) }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-gray-500 whitespace-nowrap">
                        {{ $memo->date_issued ? $memo->date_issued->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex items-center gap-3">
                            @if($memo->file_path)
                                <a href="{{ asset('storage/' . $memo->file_path) }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                            @endif
                            <button @click="openModal('{{ route('admin.memos.edit', $memo->id) }}', 'Edit Memo')"
                                class="text-brand-600 hover:text-brand-900 font-medium">Edit</button>
                            <button
                                @click="confirmDelete('{{ route('admin.memos.destroy', $memo->id) }}', '{{ $memo->memo_number }}')"
                                class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="px-5 py-10 border-b border-gray-200 bg-white text-sm text-center text-gray-500 italic">No
                        memorandums found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pagination-container">
    {{ $memos->links() }}
</div>