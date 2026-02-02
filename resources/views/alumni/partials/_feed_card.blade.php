<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 transition-all hover:shadow-md">
    <!-- Card Header -->
    <div class="p-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="relative">
                @if($post->author_avatar)
                    <img src="{{ asset('storage/' . $post->author_avatar) }}" class="w-10 h-10 rounded-full object-cover">
                @else
                    <div
                        class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold">
                        {{ substr($post->author ?? 'A', 0, 1) }}
                    </div>
                @endif
                <div
                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center
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
            <div>
                <h4 class="text-sm font-bold text-gray-900">{{ $post->author ?? 'System Admin' }}</h4>
                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @if($post->is_pinned)
            <span
                class="flex items-center text-xs font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded-full uppercase tracking-wider">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.98a1 1 0 00-1.458-1.366l-1 1.07a1 1 0 101.458 1.365l1-1.07zM14.116 6.98a1 1 0 011.459-1.366l1 1.07a1 1 0 11-1.459 1.365l-1-1.07zM4.502 12a1 1 0 100-2H3a1 1 0 100 2h1.502zM17 12a1 1 0 100-2h-1.502a1 1 0 100 2H17zM6.403 16.29a1 1 0 01-1.414-1.414l1.07-1.07a1 1 0 111.414 1.414l-1.07 1.07zM11 19a1 1 0 102 0v-1a1 1 0 10-2 0v1zM14.667 15.222a1 1 0 101.414-1.414l-1.07-1.07a1 1 0 10-1.414 1.414l1.07 1.07z" />
                </svg>
                Pinned
            </span>
        @endif
    </div>

    <!-- Card Content -->
    <div class="px-4 pb-4">
        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-tight">{{ $post->title }}</h3>
        <p class="text-gray-700 text-sm mb-4 leading-relaxed">
            {!! nl2br(e(Str::limit($post->content, 200))) !!}
        </p>

        @if($post->image_path)
            <div class="rounded-xl overflow-hidden mb-4 border border-gray-100">
                <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                    class="w-full h-auto max-h-[400px] object-cover hover:scale-105 transition-transform duration-500 cursor-pointer"
                    @click="$dispatch('open-image-modal', { src: '{{ asset('storage/' . $post->image_path) }}' })">
            </div>
        @endif

        @if($post->type === 'event' && $post->event_date)
            <div class="flex items-center space-x-4 mb-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                <div
                    class="flex-shrink-0 w-12 h-12 bg-white rounded-lg border border-gray-200 flex flex-col items-center justify-center shadow-sm">
                    <span
                        class="text-[10px] font-bold text-gray-400 uppercase leading-none">{{ $post->event_date->format('M') }}</span>
                    <span class="text-xl font-black text-gray-900 leading-none">{{ $post->event_date->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Event Detail</p>
                    <p class="text-sm font-bold text-gray-900 truncate">{{ $post->location ?? 'Virtual Event' }}</p>
                    <p class="text-xs text-gray-500">{{ $post->event_date->format('l, h:i A') }}</p>
                </div>
                @if($post->registration_link)
                    <a href="{{ $post->registration_link }}" target="_blank"
                        class="px-4 py-2 bg-brand-600 text-white rounded-lg text-xs font-bold hover:bg-brand-700 transition-colors shadow-sm">
                        Apply Now
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Card Footer -->
    <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between mt-auto">
        <div class="flex items-center space-x-4">
            <button class="flex items-center space-x-1 text-gray-400 hover:text-brand-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-xs font-medium">Interest</span>
            </button>
            <button class="flex items-center space-x-1 text-gray-400 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.827-1.233L3 20l1.341-5.022A9 9 0 1121 12z" />
                </svg>
                <span class="text-xs font-medium">Comment</span>
            </button>
        </div>
        <a href="{{ route('alumni.news.show', $post) }}"
            class="text-xs font-bold text-gray-400 hover:text-brand-600 transition-colors flex items-center">
            View Post
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>