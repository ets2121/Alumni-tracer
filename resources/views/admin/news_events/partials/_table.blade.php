<div class="overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Content</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Type</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Pinned</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Details</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Posted</th>
                <th
                    class="sticky right-0 top-0 bg-gray-50 z-10 px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-bold text-gray-500 uppercase tracking-wider shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.05)]">
                    Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($newsEvents as $post)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-5 py-4 bg-white text-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 mr-4 relative">
                                @if($post->image_path)
                                    <img class="w-full h-full rounded-lg shadow-sm object-cover"
                                        src="{{ asset('storage/' . $post->image_path) }}" alt="" />
                                @else
                                    <div
                                        class="w-full h-full rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                @if($post->is_pinned)
                                    <div
                                        class="absolute -top-1.5 -right-1.5 bg-brand-500 text-white p-0.5 rounded-full shadow-md">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.news_events.show', $post->id) }}"
                                    class="font-bold text-gray-900 line-clamp-1 text-sm hover:text-brand-600 transition-colors">{{ $post->title }}</a>
                                <div class="text-[10px] text-gray-500 line-clamp-1">
                                    {{ Str::limit(strip_tags($post->content), 50) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 bg-white text-sm">
                        @if($post->type === 'news')
                            <span
                                class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wide">News</span>
                        @elseif($post->type === 'event')
                            <span
                                class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-50 text-purple-600 border border-purple-100 uppercase tracking-wide">Event</span>
                        @elseif($post->type === 'announcement')
                            <span
                                class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-wide">Announce</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 bg-white text-sm text-center">
                        @if($post->is_pinned)
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 bg-brand-50 text-brand-600 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </span>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 bg-white text-sm">
                        <div class="flex flex-col gap-1">
                            @if($post->type === 'event')
                                <div class="flex items-center text-[10px] text-gray-600 font-medium">
                                    <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $post->event_date ? $post->event_date->format('M d, Y h:i A') : 'N/A' }}
                                </div>
                            @elseif($post->type === 'announcement' && $post->expires_at)
                                <div class="flex items-center text-[10px] text-amber-600 font-medium">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Exp: {{ $post->expires_at->format('M d, Y') }}
                                </div>
                            @elseif($post->type === 'news' && $post->author)
                                <div class="flex items-center text-[10px] text-gray-500">
                                    <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $post->author }}
                                </div>
                            @else
                                <span class="text-[10px] text-gray-400">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 bg-white text-sm text-gray-500 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span
                                class="text-[11px] font-bold text-gray-700">{{ $post->created_at->format('M d, Y') }}</span>
                            <span class="text-[9px] text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </td>
                    <td
                        class="sticky right-0 bg-white group-hover:bg-gray-50 shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.05)] px-5 py-4 text-sm text-right z-10">
                        <div
                            class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                            <button @click="openModal('{{ route('admin.news_events.edit', $post->id) }}', 'Edit Content')"
                                class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete('{{ route('admin.news_events.destroy', $post->id) }}', '{{ addslashes($post->title) }}')"
                                class="p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors"
                                title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-20 border-b border-gray-100 bg-white text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No content found</h3>
                            <p class="text-sm text-gray-400 mt-1">Get started by creating a new post.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pagination-container px-6 pb-6">
    {{ $newsEvents->links() }}
</div>