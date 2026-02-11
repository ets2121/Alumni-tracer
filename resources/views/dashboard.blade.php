<x-app-layout>
    <x-slot name="header">
        <div class="max-w-2xl mx-auto" x-data="{ currentTab: 'all' }"
            @feed-tab-synced.window="currentTab = $event.detail">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-black text-gray-900 dark:text-dark-text-primary tracking-tight">Community Feed
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span
                        class="text-[10px] font-bold text-gray-400 dark:text-dark-text-muted uppercase tracking-widest">Live
                        Updates</span>
                </div>
            </div>
            @if(Auth::user()->status === 'active')
                <div
                    class="flex items-center space-x-1 bg-gray-200 dark:bg-dark-bg-subtle p-1 rounded-xl border border-gray-300/50 dark:border-dark-border shadow-inner">
                    <button @click="currentTab = 'all'; $dispatch('switch-feed-tab', 'all')"
                        :class="currentTab === 'all' ? 'bg-brand-600 dark:bg-brand-500 text-white shadow-md' : 'text-gray-600 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-primary hover:bg-white/50 dark:hover:bg-white/5'"
                        class="flex-1 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all cursor-pointer">All</button>
                    <button @click="currentTab = 'news'; $dispatch('switch-feed-tab', 'news')"
                        :class="currentTab === 'news' ? 'bg-brand-600 dark:bg-brand-500 text-white shadow-md' : 'text-gray-600 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-primary hover:bg-white/50 dark:hover:bg-white/5'"
                        class="flex-1 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all cursor-pointer">News</button>
                    <button @click="currentTab = 'event'; $dispatch('switch-feed-tab', 'event')"
                        :class="currentTab === 'event' ? 'bg-brand-600 dark:bg-brand-500 text-white shadow-md' : 'text-gray-600 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-primary hover:bg-white/50 dark:hover:bg-white/5'"
                        class="flex-1 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all cursor-pointer">Events</button>
                    <button @click="currentTab = 'announcement'; $dispatch('switch-feed-tab', 'announcement')"
                        :class="currentTab === 'announcement' ? 'bg-brand-600 dark:bg-brand-500 text-white shadow-md' : 'text-gray-600 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-primary hover:bg-white/50 dark:hover:bg-white/5'"
                        class="flex-1 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all cursor-pointer">Announcements</button>
                    <button @click="currentTab = 'job'; $dispatch('switch-feed-tab', 'job')"
                        :class="currentTab === 'job' ? 'bg-brand-600 dark:bg-brand-500 text-white shadow-md' : 'text-gray-600 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-primary hover:bg-white/50 dark:hover:bg-white/5'"
                        class="flex-1 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all cursor-pointer">Jobs</button>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="h-full flex flex-col overflow-hidden bg-gray-50 dark:bg-dark-bg-deep"
        @if(Auth::user()->status === 'active') x-data="alumniFeed({
                endpoint: '{{ route('alumni.feed.fetch') }}'
            })" x-on:switch-feed-tab.window="switchTab($event.detail)"
        x-on:open-image-modal.window="imageModal.src = $event.detail.src; imageModal.open = true" @endif>

        <!-- Main Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar pb-12" id="feed-scroll-container">

            <div class="max-w-2xl mx-auto px-4 py-6">
                @if(Auth::user()->status === 'active')
                    <div class="space-y-4" id="feed-content">
                        <div x-html="feedHtml"></div>

                        <!-- Loading Skeletons -->
                        <template x-if="loading">
                            <div class="space-y-4">
                                <template x-for="i in 2">
                                    <div
                                        class="bg-white dark:bg-dark-bg-elevated rounded-xl p-4 border border-gray-100 dark:border-dark-border animate-pulse max-w-lg mx-auto">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-8 h-8 bg-gray-200 dark:bg-dark-bg-deep rounded-full"></div>
                                            <div class="flex-1 space-y-2">
                                                <div class="h-2 bg-gray-200 rounded w-1/4"></div>
                                                <div class="h-1.5 bg-gray-200 rounded w-1/6"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="h-3 bg-gray-200 dark:bg-dark-bg-deep rounded w-3/4"></div>
                                            <div class="h-24 bg-gray-200 dark:bg-dark-bg-deep rounded-lg w-full"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Infinite Scroll Sentinel -->
                    <div x-ref="sentinel" class="py-10 flex items-center justify-center">
                        <div x-show="loading && nextCursor" class="flex items-center space-x-1.5">
                            <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:-0.15s]">
                            </div>
                            <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                        </div>
                        <div x-show="!hasMore && feedHtml"
                            class="text-gray-400 text-[11px] font-bold uppercase tracking-widest flex items-center space-x-2">
                            <span class="w-8 h-px bg-gray-200"></span>
                            <span>No more updates</span>
                            <span class="w-8 h-px bg-gray-200"></span>
                        </div>
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center min-h-[50vh] text-center px-4 bg-white dark:bg-dark-bg-elevated rounded-3xl border border-gray-100 dark:border-dark-border shadow-xl">
                        <div
                            class="w-24 h-24 bg-brand-50 dark:bg-brand-900/20 rounded-full flex items-center justify-center mb-8">
                            <svg class="w-12 h-12 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m0 0v2m0-2h2m-2 0H10m11 0a9 9 0 11-18 0 9 9 0 0118 0zM15 7h3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h3m6 0a2 2 0 00-2-2H9a2 2 0 00-2 2m6 0v4a2 2 0 01-2 2H9a2 2 0 01-2-2V7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-dark-text-primary mb-3">Verification Pending
                        </h3>
                        <p class="text-gray-600 dark:text-dark-text-muted max-w-sm mb-10 text-sm leading-relaxed">
                            Your account is currently under review by the administrator. Access to the community feed is
                            restricted until your application is verified.
                        </p>
                        <a href="{{ route('alumni.profile.edit') }}"
                            class="inline-flex items-center px-8 py-4 bg-brand-600 hover:bg-brand-700 text-white font-black rounded-2xl transition-all shadow-xl hover:shadow-brand-500/25 active:scale-95 group">
                            <svg class="w-5 h-5 mr-3 transition-transform group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Complete Your Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div x-show="imageModal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="imageModal.open = false"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/95" x-cloak>
            <button @click="imageModal.open = false"
                class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors bg-white/10 p-2 rounded-full backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img :src="imageModal.src" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl object-contain">
        </div>

        <!-- Discussion Modal -->
        <div x-show="discussionModal.open" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @keydown.escape.window="discussionModal.open = false"
            class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-cloak>
            <div @click.away="discussionModal.open = false"
                class="bg-white dark:bg-dark-bg-elevated w-full max-w-2xl h-[90vh] rounded-3xl overflow-hidden shadow-2xl relative">
                <button @click="discussionModal.open = false"
                    class="absolute top-4 right-4 text-gray-400 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-primary transition-colors bg-gray-100 dark:bg-dark-bg-subtle p-2 rounded-full z-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="h-full" x-html="discussionModal.html" x-ref="discussionModalContent"></div>
            </div>
        </div>
    </div>

</x-app-layout>