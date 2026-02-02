<x-app-layout>
    <x-slot name="header">
        <div class="max-w-2xl mx-auto" x-data="{ currentTab: 'all' }" 
             @feed-tab-synced.window="currentTab = $event.detail">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-black text-gray-900 tracking-tight">Community Feed</h2>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Live Updates</span>
                </div>
            </div>
            <div class="flex items-center space-x-1 bg-gray-100/80 p-1 rounded-xl border border-gray-200/50 shadow-sm">
                <button @click="currentTab = 'all'; $dispatch('switch-feed-tab', 'all')"
                    :class="currentTab === 'all' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                    class="flex-1 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all cursor-pointer">All</button>
                <button @click="currentTab = 'news'; $dispatch('switch-feed-tab', 'news')"
                    :class="currentTab === 'news' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                    class="flex-1 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all cursor-pointer">News</button>
                <button @click="currentTab = 'event'; $dispatch('switch-feed-tab', 'event')"
                    :class="currentTab === 'event' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                    class="flex-1 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all cursor-pointer">Events</button>
                <button @click="currentTab = 'announcement'; $dispatch('switch-feed-tab', 'announcement')"
                    :class="currentTab === 'announcement' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                    class="flex-1 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all cursor-pointer">Announcements</button>
            </div>
        </div>
    </x-slot>

    <div class="h-full flex flex-col overflow-hidden bg-gray-50" x-data="alumniFeed()"
        @switch-feed-tab.window="switchTab($event.detail)"
        @open-image-modal.window="imageModal.src = $event.detail.src; imageModal.open = true">

        <!-- Main Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar pb-12" id="feed-scroll-container">

            <div class="max-w-2xl mx-auto px-4 py-6">
                <div class="space-y-4" id="feed-content">
                    <div x-html="feedHtml"></div>

                    <!-- Loading Skeletons -->
                    <template x-if="loading">
                        <div class="space-y-4">
                            <template x-for="i in 2">
                                <div
                                    class="bg-white rounded-xl p-4 border border-gray-100 animate-pulse max-w-lg mx-auto">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-2 bg-gray-200 rounded w-1/4"></div>
                                            <div class="h-1.5 bg-gray-200 rounded w-1/6"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-24 bg-gray-200 rounded-lg w-full"></div>
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
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('alumniFeed', () => ({
                    tab: 'all',
                    nextCursor: null,
                    loading: false,
                    hasMore: true,
                    feedHtml: '',
                    imageModal: {
                        open: false,
                        src: ''
                    },

                    init() {
                        this.fetchFeed();

                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting && !this.loading && this.hasMore) {
                                this.loadMore();
                            }
                        }, {
                            threshold: 0.1,
                            root: document.getElementById('feed-scroll-container')
                        });

                        observer.observe(this.$refs.sentinel);
                    },

                    switchTab(newTab) {
                        if (this.tab === newTab) return;
                        this.tab = newTab;
                        this.nextCursor = null;
                        this.hasMore = true;
                        this.feedHtml = '';
                        this.fetchFeed();

                        this.$dispatch('feed-tab-synced', newTab);

                        document.getElementById('feed-scroll-container').scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    },

                    async fetchFeed() {
                        if (this.loading) return;
                        this.loading = true;

                        try {
                            let url = `{{ route('alumni.feed.fetch') }}?tab=${this.tab}`;
                            if (this.nextCursor) {
                                url += `&cursor=${this.nextCursor}`;
                            }

                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if (!this.nextCursor) {
                                this.feedHtml = data.html;
                            } else {
                                this.feedHtml += data.html;
                            }

                            this.nextCursor = data.next_cursor;
                            this.hasMore = data.has_more;

                            if (!this.nextCursor) {
                                this.hasMore = false;
                            }

                        } catch (error) {
                            console.error('Feed error:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    loadMore() {
                        if (this.hasMore && !this.loading) {
                            this.fetchFeed();
                        }
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>