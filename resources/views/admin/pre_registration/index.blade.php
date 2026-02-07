<x-layouts.admin>
    <div class="py-6" x-data="preRegManager()" x-init="init()">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-dark-text-primary">Pre-Registration Management</h2>

            <!-- Search Form -->
            <div class="relative max-w-sm w-full">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" x-model.debounce.300ms="search" placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm dark:border-dark-border-subtle dark:bg-dark-bg-subtle dark:text-dark-text-primary">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24 ">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="flash-message"></div>

        <div id="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
            class="transition-opacity duration-200">
            @include('admin.pre_registration.partials._table_content')
        </div>

        <!-- Review Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop" @click="closeModal()" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="modal-content-container sm:max-w-4xl sm:w-full" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-8">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight" x-text="modalTitle"></h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-brand-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="modal-content"></div>
                </div>
            </div>
        </div>


    </div>

    @push('scripts')
        <script>
            function preRegManager() {
                return {
                    tab: '{{ $activeTab }}',
                    search: '{{ $search }}',
                    sort: '{{ $sortBy }}',
                    direction: '{{ $sortDir }}',
                    loading: false,
                    modalOpen: false,
                    modalTitle: '',
                    deleteModalOpen: false,
                    itemToDelete: '',
                    deleteUrl: '',
                    saving: false,

                    init() {
                        this.$watch('search', () => this.fetchData());
                        this.$watch('tab', () => this.fetchData());
                        this.interceptPagination();
                    },

                    setTab(newTab) {
                        this.tab = newTab;
                    },

                    async fetchData(url = null) {
                        this.loading = true;
                        if (!url) {
                            url = new URL(window.location.origin + window.location.pathname);
                            url.searchParams.set('tab', this.tab);
                            if (this.search) url.searchParams.set('search', this.search);
                            url.searchParams.set('sort', this.sort);
                            url.searchParams.set('direction', this.direction);
                        }
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await response.text();
                            document.getElementById('table-wrapper').innerHTML = html;
                            window.history.pushState({}, '', url);
                            this.interceptPagination();
                        } catch (error) { console.error('Fetch failed:', error); }
                        finally { this.loading = false; }
                    },

                    interceptPagination() {
                        document.querySelectorAll('.pagination-container a').forEach(link => {
                            link.onclick = (e) => {
                                e.preventDefault();
                                this.fetchData(e.currentTarget.href);
                            };
                        });
                    },

                    async openModal(url, title) {
                        this.modalTitle = title;
                        this.modalOpen = true;
                        document.getElementById('modal-content').innerHTML = '<div class="flex justify-center py-20"><svg class="animate-spin h-10 w-10 text-brand-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await response.text();
                            document.getElementById('modal-content').innerHTML = html;
                        } catch (error) { this.modalOpen = false; }
                    },

                    closeModal() { this.modalOpen = false; },

                    async saveReview(form) {
                        this.saving = true;
                        const formData = new FormData(form);
                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            if (response.ok) {
                                this.closeModal();
                                this.fetchData();
                                this.showFlash(data.success);
                            }
                        } catch (error) { console.error('Save failed:', error); }
                        finally { this.saving = false; }
                    },



                    showFlash(message) {
                        const flash = document.getElementById('flash-message');
                        flash.innerHTML = `<div class="bg-brand-600/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl mb-6 shadow-xl flex items-center gap-3 animate-fade-in-down"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold">${message}</span></div>`;
                        setTimeout(() => flash.innerHTML = '', 4000);
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>