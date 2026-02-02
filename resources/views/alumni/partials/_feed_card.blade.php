<div
    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4 transition-all hover:shadow-md max-w-lg mx-auto">
    <!-- Card Header -->
    <div class="px-4 py-3 flex items-center justify-between border-b border-gray-50">
        <div class="flex items-center space-x-2.5">
            <div class="relative">
                @if($post->author_avatar)
                    <img src="{{ asset('storage/' . $post->author_avatar) }}" class="w-8 h-8 rounded-full object-cover"
                        loading="lazy">
                @else
                    <div
                        class="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-xs border border-brand-100">
                        {{ substr($post->author ?? 'A', 0, 1) }}
                    </div>
                @endif
                <div
                    class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white flex items-center justify-center
                    {{ $post->type === 'news' ? 'bg-blue-500' : ($post->type === 'event' ? 'bg-purple-500' : 'bg-amber-500') }}">
                    @if($post->type === 'news')
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" />
                        </svg>
                    @elseif($post->type === 'event')
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" />
                        </svg>
                    @else
                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                        </svg>
                    @endif
                </div>
            </div>
            <div class="min-w-0">
                <h4 class="text-[13px] font-bold text-gray-900 truncate tracking-tight">
                    {{ $post->author ?? 'System Admin' }}</h4>
                <p class="text-[10px] text-gray-400 font-medium">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @if($post->is_pinned)
            <span
                class="flex items-center text-[10px] font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full uppercase tracking-tighter">
                <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.98a1 1 0 00-1.458-1.366l-1 1.07a1 1 0 101.458 1.365l1-1.07zM14.116 6.98a1 1 0 011.459-1.366l1 1.07a1 1 0 11-1.459 1.365l-1-1.07zM4.502 12a1 1 0 100-2H3a1 1 0 100 2h1.502zM17 12a1 1 0 100-2h-1.502a1 1 0 100 2H17zM6.403 16.29a1 1 0 01-1.414-1.414l1.07-1.07a1 1 0 111.414 1.414l-1.07 1.07zM11 19a1 1 0 102 0v-1a1 1 0 10-2 0v1zM14.667 15.222a1 1 0 101.414-1.414l-1.07-1.07a1 1 0 10-1.414 1.414l1.07 1.07z" />
                </svg>
                Pinned
            </span>
        @endif
    </div>

    <!-- Card Content -->
    <div class="px-4 py-3">
        <h3 class="text-sm font-bold text-gray-900 mb-1.5 leading-snug tracking-tight">{{ $post->title }}</h3>
        <p class="text-gray-600 text-[13px] mb-3 leading-relaxed">
            {!! nl2br(e(Str::limit($post->content, 150))) !!}
        </p>

        @if($post->image_path)
            <div class="rounded-lg overflow-hidden mb-3 border border-gray-100 bg-gray-50 aspect-video relative group">
                <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer"
                    loading="lazy"
                    @click="$dispatch('open-image-modal', { src: '{{ asset('storage/' . $post->image_path) }}' })">
            </div>
        @endif

        @if($post->type === 'event' && $post->event_date)
            <div class="flex items-center space-x-3 mb-3 bg-indigo-50/30 p-2.5 rounded-lg border border-indigo-100/50">
                <div
                    class="flex-shrink-0 w-10 h-10 bg-white rounded-md border border-indigo-100 flex flex-col items-center justify-center shadow-sm">
                    <span
                        class="text-[9px] font-bold text-brand-500 uppercase leading-none">{{ $post->event_date->format('M') }}</span>
                    <span
                        class="text-base font-black text-gray-900 leading-none mt-0.5">{{ $post->event_date->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-bold text-gray-900 truncate leading-none mb-1">
                        {{ $post->location ?? 'Virtual Event' }}</p>
                    <p class="text-[11px] text-gray-500 font-medium">{{ $post->event_date->format('l, h:i A') }}</p>
                </div>
                @if($post->registration_link)
                    <a href="{{ $post->registration_link }}" target="_blank"
                        class="px-3 py-1.5 bg-brand-600 text-white rounded-md text-[11px] font-bold hover:bg-brand-700 transition-colors shadow-sm whitespace-nowrap">
                        Join Event
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Card Footer -->
    <div class="px-3 py-2 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <button class="flex items-center space-x-1 text-gray-400 hover:text-brand-600 transition-colors group">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-[11px] font-semibold">Interest</span>
            </button>
            <button class="flex items-center space-x-1 text-gray-400 hover:text-blue-600 transition-colors group">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.827-1.233L3 20l1.341-5.022A9 9 0 1121 12z" />
                </svg>
                <span class="text-[11px] font-semibold">Comment</span>
            </button>
        </div>
        <a href="{{ route('alumni.news.show', $post) }}"
            class="text-[11px] font-bold text-gray-400 hover:text-brand-600 transition-colors flex items-center tracking-tight">
            Details
            <svg class="w-2.5 h-2.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>