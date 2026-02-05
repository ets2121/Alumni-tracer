<x-app-layout>

    <div class="py-8 bg-gray-100 min-h-screen" x-data="feedHandler()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- Left Sidebar (Filters & Profile Snippet) -->
                <div class="hidden lg:block space-y-6">
                    <!-- Profile Card -->
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 text-center sticky top-24">
                        <div class="relative mx-auto w-24 h-24 mb-4">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                    class="w-24 h-24 rounded-full object-cover border-4 border-brand-50 shadow-sm">
                            @else
                                <div
                                    class="w-24 h-24 rounded-full bg-brand-100 flex items-center justify-center border-4 border-white shadow-sm mx-auto">
                                    <span
                                        class="text-3xl font-bold text-brand-600">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ Auth::user()->email }}</p>
                        <a href="{{ route('alumni.profile.edit') }}"
                            class="inline-block px-4 py-2 bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                            View Profile
                        </a>
                    </div>
                </div>

                <!-- Main Feed (Center) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Create Post Placeholder (Visual Only) -->
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                    class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div
                            class="flex-1 bg-gray-50 rounded-full px-4 py-2.5 text-gray-500 text-sm cursor-not-allowed">
                            Only admins can post updates...
                        </div>
                    </div>

                    <!-- Category Tabs -->
                    <div
                        class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 flex justify-between gap-2 overflow-x-auto no-scrollbar sticky top-20 z-10">
                        <button @click="setCategory('all')"
                            :class="category === 'all' ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-2 px-4 rounded-lg text-sm transition-all whitespace-nowrap">
                            All Updates
                        </button>
                        <button @click="setCategory('pinned')"
                            :class="category === 'pinned' ? 'bg-red-50 text-red-700 font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-2 px-4 rounded-lg text-sm transition-all whitespace-nowrap flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            Pinned
                        </button>
                        <button @click="setCategory('news')"
                            :class="category === 'news' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-2 px-4 rounded-lg text-sm transition-all whitespace-nowrap">
                            News
                        </button>
                        <button @click="setCategory('event')"
                            :class="category === 'event' ? 'bg-purple-50 text-purple-700 font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-2 px-4 rounded-lg text-sm transition-all whitespace-nowrap">
                            Events
                        </button>
                        <button @click="setCategory('announcement')"
                            :class="category === 'announcement' ? 'bg-amber-50 text-amber-700 font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-2 px-4 rounded-lg text-sm transition-all whitespace-nowrap">
                            Announcements
                        </button>
                        <button @click="setCategory('job')"
                            :class="category === 'job' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-2 px-4 rounded-lg text-sm transition-all whitespace-nowrap">
                            Career
                        </button>
                    </div>

                    <!-- Feed Content -->
                    <div id="feed-container" :class="{ 'opacity-50': loading }" class="space-y-6 min-h-[500px]">
                        @include('alumni.news.partials._list')
                    </div>

                    <!-- Loading State -->
                    <div x-show="loading" class="flex justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Right Sidebar (Trending/Info) -->
                <div class="hidden lg:block space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h3 class="font-bold text-gray-900 mb-4">About Feed</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mb-4">
                            Welcome to the Alumni News Feed. Here you can find the latest updates, upcoming events,
                            and important announcements from the university.
                        </p>
                        <div class="border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filters</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">#Latest</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">#Events</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">#University</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function feedHandler() {
                return {
                    category: 'all',
                    loading: false,

                    init() {
                        // Optional: Infinite scroll observer could go here
                    },

                    async setCategory(cat) {
                        this.category = cat;
                        this.fetchFeed();
                    },

                    async fetchFeed() {
                        this.loading = true;
                        const url = new URL(window.location.href);
                        if (this.category !== 'all') {
                            url.searchParams.set('type', this.category);
                        } else {
                            url.searchParams.delete('type');
                        }

                        try {
                            const res = await fetch(url.toString(), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const html = await res.text();
                            document.getElementById('feed-container').innerHTML = html;
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loading = false;
                        }
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>