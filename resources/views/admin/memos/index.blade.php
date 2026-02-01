<x-layouts.admin>
    <x-slot name="header">
        CHED Memorandums
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="memoManager()" x-init="init()">
        <div class="p-6 text-gray-900">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <div class="flex flex-col md:flex-row items-center gap-4 w-full">
                    <h3 class="text-lg font-bold whitespace-nowrap min-w-fit">Memorandums List</h3>

                    <div class="w-full md:w-auto flex-1 flex flex-col md:flex-row gap-2">
                        <!-- Search -->
                        <div class="relative w-full md:max-w-xs">
                            <input type="text" x-model.debounce.300ms="search" placeholder="Search title or memo #..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <select x-model="category"
                            class="w-full md:w-48 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>

                        <!-- Year Filter -->
                        <select x-model="year"
                            class="w-full md:w-32 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
                            <option value="">All Years</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button @click="openModal('{{ route('admin.memos.create') }}', 'Upload New Memo')"
                    class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-6 rounded-lg transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload New Memo
                </button>
            </div>

            <div id="flash-message">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm transition-all">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
            </div>

            <div id="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
                class="transition-opacity duration-200">
                @include('admin.memos.partials._table')
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop" @click="closeModal()" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="modal-content-container sm:max-w-xl sm:w-full" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900" x-text="modalTitle"></h3>
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

        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop" @click="deleteModalOpen = false" x-show="deleteModalOpen"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="modal-content-container sm:max-w-md sm:w-full text-center" x-show="deleteModalOpen"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-8">
                    <div
                        class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 text-red-600 mb-6 border border-red-100">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Delete Memorandum?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-4">Are you sure you want to delete <span
                            class="font-bold text-gray-900" x-text="itemToDelete"></span>?</p>
                    <div class="flex justify-center gap-3">
                        <button @click="deleteModalOpen = false"
                            class="flex-1 px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all">Cancel</button>
                        <button @click="executeDelete()"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-100">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function memoManager() {
                return {
                    search: '{{ $search ?? '' }}',
                    category: '{{ request('category') ?? '' }}',
                    year: '{{ request('year') ?? '' }}',
                    loading: false,
                    saving: false,
                    modalOpen: false,
                    modalTitle: '',
                    deleteModalOpen: false,
                    itemToDelete: '',
                    deleteUrl: '',

                    init() {
                        this.$watch('search', () => this.fetchData());
                        this.$watch('category', () => this.fetchData());
                        this.$watch('year', () => this.fetchData());
                        this.interceptPagination();
                        document.addEventListener('submit', async (e) => {
                            if (e.target.id === 'memo-form') {
                                e.preventDefault();
                                await this.saveForm(e.target);
                            }
                        });
                    },

                    async fetchData(url = null) {
                        this.loading = true;
                        if (!url) {
                            url = new URL(window.location.origin + window.location.pathname);
                            if (this.search) url.searchParams.set('search', this.search);
                            if (this.category) url.searchParams.set('category', this.category);
                            if (this.year) url.searchParams.set('year', this.year);
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
                            link.addEventListener('click', (e) => {
                                e.preventDefault();
                                this.fetchData(e.currentTarget.href);
                            });
                        });
                    },

                    async openModal(url, title) {
                        this.modalTitle = title;
                        this.modalOpen = true;
                        document.getElementById('modal-content').innerHTML = '<div class="flex justify-center py-10"><svg class="animate-spin h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await response.text();
                            document.getElementById('modal-content').innerHTML = html;
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
                                Object.keys(data.errors).forEach(field => {
                                    const errorEl = document.querySelector(`.error-message[data-field="${field}"]`);
                                    if (errorEl) errorEl.textContent = data.errors[field][0];
                                });
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
                        flash.innerHTML = `<div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm"><p>${message}</p></div>`;
                        setTimeout(() => flash.innerHTML = '', 3000);
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>