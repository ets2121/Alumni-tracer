<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="alumniFeed()"
        @open-image-modal.window="imageModal.src = $event.detail.src; imageModal.open = true">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Welcome Section (Simplified) -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 leading-tight">Feed</h2>
                    <p class="text-sm text-gray-500 font-medium">Updates from your alma mater</p>
                </div>
                <div class="relative">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                            class="w-12 h-12 rounded-full border-2 border-white shadow-sm ring-2 ring-brand-100">
                    @else
                        <div
                            class="w-12 h-12 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold shadow-sm ring-2 ring-brand-100">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab Navigation (Sticky) -->
            <div class="sticky top-[73px] z-20 bg-gray-50/80 backdrop-blur-md pb-4 mb-2">
                <div class="flex items-center space-x-1 bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100">
                    <button @click="switchTab('all')"
                        :class="tab === 'all' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">All</button>
                    <button @click="switchTab('news')"
                        :class="tab === 'news' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">News</button>
                    <button @click="switchTab('event')"
                        :class="tab === 'event' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Events</button>
                    <button @click="switchTab('announcement')"
                        :class="tab === 'announcement' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Prtcl</button>
                </div>
            </div>

            <!-- Feed Content -->
            <div class="space-y-6" id="feed-content">
                <div x-html="feedHtml"></div>

                <!-- Loading Skeletons -->
                <template x-if="loading">
                    <div class="space-y-6">
                        <template x-for="i in 3">
                            <div class="bg-white rounded-2xl p-4 border border-gray-100 animate-pulse">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                        <div class="h-2 bg-gray-200 rounded w-1/6"></div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                    <div class="h-48 bg-gray-200 rounded-xl w-full"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Empty State (handled by fetch response) -->

            <!-- Infinite Scroll Sentinel -->
            <div x-ref="sentinel" class="h-8 shadow-sm rounded-full bg-transparent flex items-center justify-center">
                <div x-show="loading && page > 1" class="flex items-center space-x-2">
                    <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                    <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div x-show="imageModal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="imageModal.open = false"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/90" x-cloak>
            <button @click="imageModal.open = false"
                class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img :src="imageModal.src" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('alumniFeed', () => ({
                    tab: 'all',
                    page: 1,
                    loading: false,
                    hasMore: true,
                    feedHtml: '',
                    imageModal: {
                        open: false,
                        src: ''
                    },

                    init() {
                        this.fetchFeed();

                        // Infinite Scroll Setup
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting && !this.loading && this.hasMore) {
                                this.loadMore();
                            }
                        }, { threshold: 0.1 });

                        observer.observe(this.$refs.sentinel);

                    },

                    switchTab(newTab) {
                        if (this.tab === newTab) return;
                        this.tab = newTab;
                        this.page = 1;
                        this.hasMore = true;
                        this.feedHtml = '';
                        this.fetchFeed();

                        // Scroll to top of feed content
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    },

                    async fetchFeed() {
                        if (this.loading) return;
                        this.loading = true;

                        try {
                            const response = await fetch(`{{ route('alumni.feed.fetch') }}?tab=${this.tab}&page=${this.page}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const html = await response.text();

                            if (this.page === 1) {
                                this.feedHtml = html;
                            } else {
                                this.feedHtml += html;
                            }

                            // Check if we reached the end (simple check for now)
                            // In a real app, the backend should return if there's more
                            if (html.trim().length === 0 || html.includes('No posts found')) {
                                this.hasMore = false;
                            } else {
                                // Basic heuristic: if we got less than 10 cards, maybe no more
                                // But usually best to check backend header or JSON
                            }

                        } catch (error) {
                            console.error('Feed error:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    loadMore() {
                        this.page++;
                        this.fetchFeed();
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>