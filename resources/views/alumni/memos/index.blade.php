<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CHED Memorandums') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="memoViewer()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Policy & Guidelines</h3>
                    <p class="text-gray-500">Access the latest CHED memorandums and institutional policies.</p>
                </div>

                <div class="relative w-full max-w-md">
                    <input type="text" x-model.debounce.300ms="search" placeholder="Search memorandums..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div id="list-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
                class="transition-opacity duration-200">
                @include('alumni.memos.partials._list')
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function memoViewer() {
                return {
                    search: '{{ $search ?? '' }}',
                    loading: false,

                    init() {
                        this.$watch('search', () => this.fetchData());
                        this.interceptPagination();
                    },

                    async fetchData(url = null) {
                        this.loading = true;
                        if (!url) {
                            url = new URL(window.location.origin + window.location.pathname);
                            if (this.search) url.searchParams.set('search', this.search);
                        }

                        try {
                            const response = await fetch(url, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const html = await response.text();
                            document.getElementById('list-wrapper').innerHTML = html;
                            window.history.pushState({}, '', url);
                            this.interceptPagination();
                        } catch (error) {
                            console.error('Failed to fetch memos:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    interceptPagination() {
                        document.querySelectorAll('.pagination-container a').forEach(link => {
                            link.addEventListener('click', (e) => {
                                e.preventDefault();
                                this.fetchData(e.currentTarget.href);
                            });
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>