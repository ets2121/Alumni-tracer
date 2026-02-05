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
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Engagement</th>
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
                        @elseif($post->type === 'job')
                            <span
                                class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wide">Job
                                Post</span>
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
                            @elseif($post->type === 'job')
                                <div class="flex items-center text-[10px] text-blue-600 font-bold uppercase tracking-tight">
                                    {{ $post->job_company ?: 'Hiring Now' }}
                                </div>
                                @if($post->job_deadline)
                                    <div class="flex items-center text-[9px] text-gray-400 font-medium">
                                        <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Due: {{ $post->job_deadline->format('M d, Y') }}
                                    </div>
                                @endif
                            @else
                                <span class="text-[10px] text-gray-400">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 bg-white text-sm">
                        <div class="flex items-center justify-center gap-4">
                            <div class="flex items-center gap-1.5"
                                title="{{ number_format($post->reactions_count) }} Reactions">
                                <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-xs font-bold text-gray-700">{{ number_format($post->reactions_count) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5"
                                title="{{ number_format($post->comments_count) }} Comments">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-xs font-bold text-gray-700">{{ number_format($post->comments_count) }}</span>
                            </div>
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
                            @if($post->type === 'event')
                                <button
                                    @click="openModal('{{ route('admin.news_events.broadcast.form', $post->id) }}', 'Broadcast Invitations')"
                                    class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                    title="Broadcast Invitations">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.167H3.38a1.745 1.745 0 01-1.457-2.711l1.547-2.166a1.723 1.723 0 011.121-.632L11 5.882zM11 5.882c.35-.022.724.019 1 .118.475.17.888.477 1.177.876L16.273 11c.238.328.39.706.441 1.103.053.458.044.891-.026 1.285L15 15.391a2.01 2.01 0 01-1.574.882c-.183.003-.366-.02-.545-.068l-1.881-.512M11 5.882c0-.183.021-.366.068-.545L12 3m6 10l2 2m-2-4l2-2" />
                                    </svg>
                                </button>
                            @endif
                            <button
                                @click="openModal('{{ route('admin.news_events.moderate', $post->id) }}', 'Moderate Publication', 'max-w-6xl')"
                                class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                title="Moderate & Insights">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </button>
                            <button @click="openModal('{{ route('admin.news_events.edit', $post->id) }}', 'Edit Content')"
                                class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <button @click="$dispatch('open-confirmation-modal', { 
                                                        title: 'Delete Publication', 
                                                        message: 'Are you sure you want to delete {{ addslashes($post->title) }}? This action cannot be undone.', 
                                                        action: '{{ route('admin.news_events.destroy', $post->id) }}', 
                                                        method: 'DELETE', 
                                                        danger: true, 
                                                        confirmText: 'Delete' 
                                                    })"
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
                    <td colspan="6" class="px-5 py-20 border-b border-gray-100 bg-white text-center">
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