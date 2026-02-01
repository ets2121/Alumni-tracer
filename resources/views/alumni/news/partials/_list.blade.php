<div class="space-y-6">
    @forelse($newsEvents as $post)
        <article
            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <!-- Header -->
            <div class="p-4 flex items-center justify-between border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-sm border-2 border-white shadow-sm">
                        A
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Alumni Admin</h4>
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            {{ $post->created_at->diffForHumans() }}
                            <span>•</span>
                            <span
                                class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wide {{ $post->type === 'news' ? 'bg-blue-50 text-blue-600' : ($post->type === 'event' ? 'bg-purple-50 text-purple-600' : 'bg-amber-50 text-amber-600') }}">
                                {{ $post->type }}
                            </span>
                        </p>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-4 pb-2">
                <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight">
                    <a href="{{ route('alumni.news.show', $post->id) }}" class="hover:text-brand-600 transition-colors">
                        {{ $post->title }}
                    </a>
                </h3>
                <div class="text-gray-600 text-sm leading-relaxed mb-4">
                    {{ Str::limit(strip_tags($post->content), 200) }}
                    @if(strlen(strip_tags($post->content)) > 200)
                        <a href="{{ route('alumni.news.show', $post->id) }}"
                            class="text-brand-600 font-medium hover:underline ml-1">See more</a>
                    @endif
                </div>
            </div>

            <!-- Media/Image -->
            @if($post->image_path)
                <div class="w-full relative bg-gray-100">
                    <a href="{{ route('alumni.news.show', $post->id) }}">
                        <img src="{{ asset('storage/' . $post->image_path) }}" class="w-full h-auto max-h-[500px] object-cover"
                            alt="{{ $post->title }}">
                    </a>
                </div>
            @endif

            <!-- Footer / Interactions -->
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex gap-4">
                    <button class="flex items-center gap-2 text-gray-500 hover:text-brand-600 transition-colors group">
                        <div class="p-2 rounded-full group-hover:bg-brand-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Like</span>
                    </button>
                    <button class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors group">
                        <div class="p-2 rounded-full group-hover:bg-blue-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M19 12h.01M6 10V4a2 2 0 00-2-2h8a2 2 0 00-2 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Comment</span>
                    </button>
                    <button class="flex items-center gap-2 text-gray-500 hover:text-green-600 transition-colors group">
                        <div class="p-2 rounded-full group-hover:bg-green-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Share</span>
                    </button>
                </div>
            </div>
        </article>
    @empty
        <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">No updates yet</h3>
            <p class="text-gray-500 text-sm mt-1">Check back later for new stories or events.</p>
        </div>
    @endforelse

    <div class="mt-8 pagination-container">
        {{ $newsEvents->links() }}
    </div>
</div>