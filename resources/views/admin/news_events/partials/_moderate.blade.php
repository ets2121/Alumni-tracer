<div class="h-[80vh] flex flex-col" x-data="moderationDashboard({ postId: {{ $news_event->id }} })" x-init="init()">
    <!-- Header with Tabs -->
    <div class="px-6 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
        <div class="flex gap-8">
            <button @click="tab = 'post'"
                :class="tab === 'post' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-400 hover:text-gray-600'"
                class="py-4 border-b-2 font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Post Preview
            </button>
            <button @click="setTab('reactions')"
                :class="tab === 'reactions' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-400 hover:text-gray-600'"
                class="py-4 border-b-2 font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Reactions
                <span
                    class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded-full text-[10px]">{{ $news_event->reactions_count ?? 0 }}</span>
            </button>
            <button @click="setTab('comments')"
                :class="tab === 'comments' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-400 hover:text-gray-600'"
                class="py-4 border-b-2 font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Comments
                <span
                    class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-full text-[10px]">{{ $news_event->comments_count ?? 0 }}</span>
            </button>
            <button @click="setTab('insights')"
                :class="tab === 'insights' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-400 hover:text-gray-600'"
                class="py-4 border-b-2 font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Insights & Engagement
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div class="flex-1 overflow-y-auto bg-gray-50/50">
        <!-- Tab: Post Preview -->
        <div x-show="tab === 'post'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="p-6">
            <div class="max-w-xl mx-auto bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                <!-- Feed Header -->
                <div class="p-5 flex items-center justify-between border-b border-gray-50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-brand-900 flex items-center justify-center overflow-hidden border-2 border-brand-50 shadow-inner">
                            <img src="{{ asset('images/logo-1.png') }}" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-sm font-black text-gray-900">
                                    {{ $news_event->author ?? config('app.university_name') }}</h3>
                                <svg class="w-4 h-4 text-blue-500 fill-current" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                                </svg>
                            </div>
                            <div
                                class="flex items-center gap-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                <span>{{ $news_event->created_at->diffForHumans() }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span class="text-brand-600">{{ $news_event->type }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Media -->
                <div class="p-0">
                    @if($news_event->image_path)
                        <div class="aspect-[16/9] w-full bg-gray-100 overflow-hidden relative group">
                            <img src="{{ asset('storage/' . $news_event->image_path) }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                </div>

                <!-- Feed Body -->
                <div class="p-6">
                    <h2 class="text-xl font-black text-gray-900 mb-3 leading-tight">{{ $news_event->title }}</h2>

                    <div class="prose prose-sm prose-brand max-w-none text-gray-600 mb-6 line-clamp-6">
                        {!! $news_event->content !!}
                    </div>

                    <!-- Enhanced Metadata Cards -->
                    @if($news_event->type === 'event' || $news_event->type === 'job')
                        <div class="mb-6">
                            @if($news_event->type === 'event')
                                <div class="bg-brand-50/50 p-4 rounded-2xl border border-brand-100 flex flex-col gap-3">
                                    @if($news_event->event_date)
                                        <div class="flex items-center gap-3 text-sm">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-brand-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase leading-none">Schedule
                                                </div>
                                                <div class="font-black text-gray-800">
                                                    {{ $news_event->event_date->format('M d, Y • h:i A') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($news_event->location)
                                        <div class="flex items-center gap-3 text-sm">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase leading-none">Venue</div>
                                                <div class="font-black text-gray-800">{{ $news_event->location }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @elseif($news_event->type === 'job')
                                <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 space-y-3">
                                    <div class="flex items-center gap-3 text-sm">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-emerald-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase leading-none">Hiring
                                                Company</div>
                                            <div class="font-black text-gray-800">{{ $news_event->job_company }}</div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        @if($news_event->job_salary)
                                            <div class="p-2.5 bg-white rounded-xl border border-emerald-50">
                                                <div class="text-[9px] text-gray-400 font-bold uppercase leading-none mb-1">
                                                    Estimated</div>
                                                <div class="text-xs font-black text-emerald-600">{{ $news_event->job_salary }}</div>
                                            </div>
                                        @endif
                                        @if($news_event->job_deadline)
                                            <div class="p-2.5 bg-white rounded-xl border border-emerald-50">
                                                <div class="text-[9px] text-gray-400 font-bold uppercase leading-none mb-1">Deadline
                                                </div>
                                                <div class="text-xs font-black text-red-500">
                                                    {{ $news_event->job_deadline->format('M d, Y') }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Photos Grid -->
                    @if($news_event->photos && $news_event->photos->count() > 0)
                        <div class="grid grid-cols-2 gap-2 mb-6 rounded-2xl overflow-hidden shadow-sm">
                            @foreach($news_event->photos->take(4) as $index => $photo)
                                <div
                                    class="aspect-square relative group cursor-pointer {{ ($news_event->photos->count() === 1) ? 'col-span-2 aspect-video' : (($news_event->photos->count() === 3 && $index === 0) ? 'row-span-2 aspect-auto' : '') }}">
                                    <img src="{{ asset('storage/' . $photo->image_path) }}" class="w-full h-full object-cover">
                                    @if($index === 3 && $news_event->photos->count() > 4)
                                        <div
                                            class="absolute inset-0 bg-black/50 backdrop-blur-sm flex flex-col items-center justify-center text-white">
                                            <span class="text-xl font-black">+{{ $news_event->photos->count() - 3 }}</span>
                                            <span class="text-[8px] font-bold uppercase tracking-widest mt-1">Photos</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Feed Interaction Bar -->
                    <div class="pt-5 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-5">
                            <div class="flex items-center gap-1.5 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span
                                    class="text-xs font-black">{{ number_format($news_event->reactions_count ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <span
                                    class="text-xs font-black">{{ number_format($news_event->comments_count ?? 0) }}</span>
                            </div>
                        </div>
                        <div class="flex -space-x-2">
                            <div
                                class="w-6 h-6 rounded-full bg-brand-100 border-2 border-white ring-1 ring-brand-50 shadow-sm">
                            </div>
                            <div
                                class="w-6 h-6 rounded-full bg-brand-200 border-2 border-white ring-1 ring-brand-50 shadow-sm">
                            </div>
                            <div
                                class="w-6 h-6 rounded-full bg-brand-900 border-2 border-white ring-1 ring-brand-50 shadow-sm flex items-center justify-center text-[8px] font-bold text-white uppercase italic">
                                AU</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Reactions -->
        <div x-show="tab === 'reactions'" class="p-6 h-full flex flex-col">
            <div class="max-w-2xl mx-auto w-full">
                <div class="space-y-3" id="reactions-container">
                    <template x-for="reaction in reactions" :key="reaction.id">
                        <div
                            class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-100 hover:border-red-100 hover:shadow-sm transition-all">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 font-bold overflow-hidden border border-red-100">
                                    <template x-if="reaction.user.avatar">
                                        <img :src="'/storage/' + reaction.user.avatar"
                                            class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!reaction.user.avatar">
                                        <span x-text="reaction.user.name.charAt(0)"></span>
                                    </template>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900" x-text="reaction.user.name"></div>
                                    <div class="text-[10px] text-gray-400"
                                        x-text="reaction.user.department_name || 'Alumni'"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-gray-400" x-text="formatDate(reaction.created_at)"></span>
                                <div class="p-2 bg-red-50 text-red-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Pagination Trigger & Loading State -->
                <div x-intersect="loadMoreReactions()" x-show="hasMoreReactions" class="py-10 flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                </div>

                <div x-show="!hasMoreReactions && reactions.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-gray-400">
                    <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <p class="text-sm italic">No reactions yet</p>
                </div>
            </div>
        </div>

        <!-- Tab: Comments -->
        <div x-show="tab === 'comments'" class="p-6 h-full flex flex-col">
            <div class="max-w-3xl mx-auto w-full">
                <div class="space-y-6">
                    <template x-for="comment in comments" :key="comment.id">
                        <div
                            class="group relative bg-white p-5 rounded-2xl border border-gray-100 hover:border-blue-100 transition-all shadow-sm">
                            <div class="flex gap-4">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 font-bold overflow-hidden border border-blue-100 flex-shrink-0">
                                    <template x-if="comment.user.avatar">
                                        <img :src="'/storage/' + comment.user.avatar"
                                            class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!comment.user.avatar">
                                        <span x-text="comment.user.name.charAt(0)"></span>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900"
                                                x-text="comment.user.name"></span>
                                            <template
                                                x-if="comment.user.role === 'admin' || comment.user.role === 'dept_admin'">
                                                <span
                                                    class="px-1.5 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded uppercase">Staff</span>
                                            </template>
                                            <span class="text-[10px] text-gray-400"
                                                x-text="formatDate(comment.created_at)"></span>
                                        </div>
                                        <div
                                            class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="replyTo(comment)"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Reply">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l5 5m-5-5l5-5" />
                                                </svg>
                                            </button>
                                            <button @click="confirmDeleteComment(comment.id)"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"
                                        x-text="comment.content"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Pagination Trigger & Loading State -->
                <div x-intersect="loadMoreComments()" x-show="hasMoreComments" class="py-10 flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <div x-show="!hasMoreComments && comments.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-gray-400">
                    <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <p class="text-sm italic">No comments yet</p>
                </div>
            </div>
        </div>

        <!-- Tab: Insights & Engagement -->
        <div x-show="tab === 'insights'" class="p-6 h-full flex flex-col overflow-y-auto">
            <template x-if="loadingInsights && !insights">
                <div class="flex-1 flex items-center justify-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-600"></div>
                </div>
            </template>

            <template x-if="insights">
                <div class="max-w-5xl mx-auto w-full space-y-8 pb-20">
                    <!-- Metrics Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="p-2 bg-red-50 text-red-500 rounded-xl">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Reactions</span>
                            </div>
                            <div class="text-3xl font-black text-gray-900" x-text="insights.metrics.total_reactions"></div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="p-2 bg-blue-50 text-blue-500 rounded-xl">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Comments</span>
                            </div>
                            <div class="text-3xl font-black text-gray-900" x-text="insights.metrics.total_comments"></div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Alumni Engaged</span>
                            </div>
                            <div class="text-3xl font-black text-gray-900" x-text="insights.metrics.unique_interactors"></div>
                        </div>
                    </div>

                    <!-- Demographic Breakdown -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Reactions by Department -->
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Reactions by Department</h4>
                            <div class="space-y-4">
                                <template x-for="dept in insights.demographics.reactions">
                                    <div>
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[11px] font-bold text-gray-600 uppercase" x-text="dept.department_name || 'Others'"></span>
                                            <span class="text-xs font-black text-gray-900" x-text="dept.count"></span>
                                        </div>
                                        <div class="w-full bg-gray-50 rounded-full h-2 overflow-hidden">
                                            <div class="bg-red-500 h-full rounded-full transition-all duration-1000" 
                                                :style="'width: ' + (dept.count / (insights.metrics.total_reactions || 1) * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="insights.demographics.reactions.length === 0">
                                    <div class="text-center py-10">
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No reaction data yet</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Comments by Department -->
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Comments by Department</h4>
                            <div class="space-y-4">
                                <template x-for="dept in insights.demographics.comments">
                                    <div>
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[11px] font-bold text-gray-600 uppercase" x-text="dept.department_name || 'Others'"></span>
                                            <span class="text-xs font-black text-gray-900" x-text="dept.count"></span>
                                        </div>
                                        <div class="w-full bg-gray-50 rounded-full h-2 overflow-hidden">
                                            <div class="bg-blue-500 h-full rounded-full transition-all duration-1000" 
                                                :style="'width: ' + (dept.count / (insights.metrics.total_comments || 1) * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="insights.demographics.comments.length === 0">
                                    <div class="text-center py-10">
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No comment data yet</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-gray-900 p-8 rounded-[3rem] text-white">
                        <h4 class="text-xs font-black uppercase tracking-[0.3em] text-brand-400 mb-6">Recent Peak Engagement</h4>
                        <div class="divide-y divide-white/5">
                            <template x-for="activity in insights.recent_activity">
                                <div class="py-4 flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-lg font-black text-brand-500 group-hover:bg-brand-500 group-hover:text-white transition-all">
                                            <template x-if="activity.user.avatar">
                                                <img :src="'/storage/' + activity.user.avatar" class="w-full h-full object-cover rounded-2xl">
                                            </template>
                                            <template x-if="!activity.user.avatar">
                                                <span x-text="activity.user.name.charAt(0)"></span>
                                            </template>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black" x-text="activity.user.name"></div>
                                            <div class="text-[10px] text-white/50 font-bold uppercase tracking-widest" x-text="activity.user.department_name || 'Alumni'"></div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] font-black text-brand-400 uppercase tracking-widest">Reacted</div>
                                        <div class="text-[9px] text-white/30" x-text="formatDate(activity.created_at)"></div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="insights.recent_activity.length === 0">
                                <div class="text-center py-10 text-white/20">
                                    <p class="text-xs font-bold uppercase tracking-[0.2em]">Still waiting for first interaction</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Reply Drawer (Mini Overlay) -->
    <div x-show="replyMode" x-transition
        class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-2xl z-20 p-4 max-w-lg mx-auto rounded-t-3xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                        clip-rule="evenodd" />
                </svg>
                Reply to <span x-text="selectedComment?.user?.name" class="text-blue-600"></span>
            </h3>
            <button @click="replyMode = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="relative">
            <textarea x-model="replyContent"
                class="w-full rounded-2xl border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500 min-h-[100px] py-3 pr-20"
                placeholder="Write your reply..."></textarea>
            <button @click="submitReply()" :disabled="!replyContent.trim() || submitting"
                class="absolute bottom-3 right-3 bg-blue-600 text-white px-4 py-1.5 rounded-xl text-xs font-bold hover:bg-blue-700 transition-all disabled:opacity-50">
                <span x-show="!submitting">Reply</span>
                <span x-show="submitting" class="flex items-center gap-1">
                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Sending...
                </span>
            </button>
        </div>
    </div>
</div>