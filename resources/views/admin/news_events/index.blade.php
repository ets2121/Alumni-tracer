<x-layouts.admin>
    <x-slot name="header">
        News & Events Management
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="newsManager()" x-init="init()">
        <div class="p-6 text-gray-900">
            <div
                class="sticky top-0 z-20 bg-white/90 backdrop-blur-md -mx-6 px-6 py-4 mb-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 transition-all">
                <div class="flex flex-col w-full md:w-auto gap-4">
                    <h3 class="text-xl font-black text-gray-800">Content Management</h3>

                    <!-- Tabs -->
                    <div class="flex p-1 space-x-1 bg-gray-100/50 rounded-xl w-fit">
                        <button @click="setTab('all')"
                            :class="currentTab === 'all' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-bold rounded-lg transition-all">All</button>
                        <button @click="setTab('news')"
                            :class="currentTab === 'news' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-bold rounded-lg transition-all">News</button>
                        <button @click="setTab('event')"
                            :class="currentTab === 'event' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Events</button>
                        <button @click="setTab('announcement')"
                            :class="currentTab === 'announcement' ? 'bg-white text-amber-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Announcements</button>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative">
                        <select x-model="sort" @change="fetchData()"
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-200 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm rounded-xl shadow-sm">
                            <option value="latest">Latest Created</option>
                            <option value="oldest">Oldest Created</option>
                            <option value="event_date_asc">Event Date (Asc)</option>
                            <option value="event_date_desc">Event Date (Desc)</option>
                        </select>
                    </div>

                    <div class="relative w-full max-w-xs">
                        <input type="text" x-model.debounce.300ms="search" placeholder="Search..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button @click="openModal('{{ route('admin.news_events.create') }}', 'Create New Post')"
                        class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-lg shadow-brand-100 flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Post
                    </button>
                </div>
            </div>

            <div id="flash-message">
                @if(session('success'))
                    <div
                        class="fixed top-8 right-8 z-[100] bg-gray-900 border border-white/10 backdrop-blur-xl text-white px-8 py-4 rounded-2xl shadow-2xl animate-in slide-in-from-right duration-500 flex items-center gap-4">
                        <div class="p-2 bg-green-500 rounded-full text-white"><svg class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg></div>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                @endif
            </div>

            <div id="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
                class="transition-opacity duration-200 min-h-[400px]">
                @include('admin.news_events.partials._table')
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
                    @click="closeModal()" x-show="modalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="modal-content-container inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                    x-show="modalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6 pl-2 border-l-4 border-brand-500">
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight" x-text="modalTitle"></h3>
                            <button @click="closeModal()"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none transition-colors">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

        <!-- Delete Modal -->
        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
                    @click="deleteModalOpen = false" x-show="deleteModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    x-show="deleteModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Delete Publication</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete <span class="font-bold text-gray-900"
                                            x-text="itemToDelete"></span>? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button @click="executeDelete()" type="button"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button @click="deleteModalOpen = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function newsManager() {
                return {
                    search: '{{ $search ?? '' }}',
                    currentTab: 'all',
                    sort: 'latest',
                    loading: false,
                    saving: false,
                    modalOpen: false,
                    modalTitle: '',
                    deleteModalOpen: false,
                    itemToDelete: '',
                    deleteUrl: '',

                    init() {
                        this.$watch('search', () => this.fetchData());
                        this.interceptPagination();
                    },

                    setTab(tab) {
                        this.currentTab = tab;
                        this.fetchData();
                    },

                    async fetchData(url = null) {
                        this.loading = true;
                        if (!url) {
                            url = new URL(window.location.origin + window.location.pathname);
                            if (this.search) url.searchParams.set('search', this.search);
                            if (this.currentTab !== 'all') url.searchParams.set('type', this.currentTab);
                            if (this.sort !== 'latest') url.searchParams.set('sort', this.sort);
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
                        document.getElementById('modal-content').innerHTML = '<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-600"></div></div>';
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            document.getElementById('modal-content').innerHTML = await response.text();

                            // Initialize new form listener
                            const form = document.getElementById('news-form');
                            if (form) {
                                form.onsubmit = async (e) => {
                                    e.preventDefault();
                                    await this.saveForm(e.target);
                                };
                            }
                        } catch (error) { this.modalOpen = false; }
                    },

                    closeModal() { this.modalOpen = false; },

                    async saveForm(form) {
                        this.saving = true;
                        const formData = new FormData(form);
                        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
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
                            } else if (response.status === 422) {
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        // Handle array generic errors (photos.0 -> photos)
                                        const cleanField = field.split('.')[0];
                                        const errorEl = document.querySelector(`.error-message[data-field="${cleanField}"]`);
                                        if (errorEl) errorEl.textContent = data.errors[field][0];
                                    });
                                }
                            }
                        } catch (error) { console.error('Save failed:', error); }
                        finally { this.saving = false; }
                    },

                    confirmDelete(url, name) {
                        this.deleteUrl = url;
                        this.itemToDelete = name;
                        this.deleteModalOpen = true;
                    },

                    async executeDelete() {
                        try {
                            const response = await fetch(this.deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            if (response.ok) {
                                this.deleteModalOpen = false;
                                this.fetchData();
                                this.showFlash(data.success);
                            }
                        } catch (error) { console.error('Delete failed:', error); }
                    },

                    showFlash(message) {
                        const flash = document.getElementById('flash-message');
                        flash.innerHTML = `<div class="fixed top-8 right-8 z-[100] bg-gray-900 border border-white/10 backdrop-blur-xl text-white px-8 py-4 rounded-2xl shadow-2xl animate-in slide-in-from-right duration-500 flex items-center gap-4">
                                            <div class="p-2 bg-green-500 rounded-full text-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg></div>
                                            <span class="font-bold text-sm">${message}</span>
                                        </div>`;
                        setTimeout(() => flash.innerHTML = '', 4000);
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>