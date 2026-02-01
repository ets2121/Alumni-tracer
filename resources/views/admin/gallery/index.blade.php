<x-layouts.admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter">Institutional Archive</h2>
                <p class="text-xs text-gray-400 font-bold uppercase mt-1 tracking-widest italic">Preserving the legacy
                    of excellence</p>
            </div>
            <button
                @click="$dispatch('open-gallery-create-modal', { url: '{{ route('admin.gallery.create') }}', title: 'Initialize New Archive' })"
                class="bg-gray-900 hover:bg-black text-white px-8 py-4 rounded-[2rem] shadow-2xl shadow-gray-200 transition-all flex items-center gap-3 group">
                <div class="p-1.5 bg-brand-500 rounded-full group-hover:rotate-90 transition-transform duration-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="font-black uppercase text-[10px] tracking-widest">Create Album</span>
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="galleryManager()" x-init="init()"
        x-on:open-gallery-create-modal.window="openModal($event.detail.url, $event.detail.title)" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Category & Search Bar -->
            <div
                class="sticky top-0 z-10 bg-gray-50/95 backdrop-blur-sm py-4 mb-8 border-b border-gray-200/50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 transition-all">
                <div
                    class="flex bg-white/50 backdrop-blur-md p-1.5 rounded-[1.5rem] border border-gray-100 shadow-sm w-full lg:w-auto overflow-x-auto no-scrollbar">
                    <button @click="setCategory('all')"
                        :class="category === 'all' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:text-gray-900'"
                        class="px-6 py-2.5 rounded-[1.2rem] text-[9px] font-black uppercase tracking-[0.2em] transition-all shrink-0">All
                        Archive</button>
                    @foreach(['Graduation photos', 'Alumni events', 'Reunions', 'Campus activities'] as $cat)
                        <button @click="setCategory('{{ $cat }}')"
                            :class="category === '{{ $cat }}' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:text-gray-900'"
                            class="px-6 py-2.5 rounded-[1.2rem] text-[9px] font-black uppercase tracking-[0.2em] transition-all shrink-0">{{ str_replace(' photos', '', str_replace(' activities', '', $cat)) }}</button>
                    @endforeach
                </div>

                <div class="relative w-full lg:w-80 group">
                    <input type="text" x-model.debounce.300ms="search" placeholder="Query archive database..."
                        class="w-full pl-12 pr-6 py-3 bg-white border border-gray-100 rounded-[1.5rem] focus:border-brand-500 focus:ring-0 text-sm font-medium transition-all shadow-sm group-hover:shadow-md">
                    <div
                        class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-300 group-hover:text-brand-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div id="flash-message"></div>

            <!-- Album Grid -->
            <div id="album-grid" :class="{ 'opacity-30 blur-sm pointer-events-none': loading }"
                class="transition-all duration-700">
                @include('admin.gallery.partials._grid')
            </div>
        </div>

        <!-- System Modals -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop" @click="closeModal()" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="modal-content-container sm:max-w-2xl sm:w-full" x-show="modalOpen"
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
                    <div id="modal-content" class="min-h-[200px]"></div>
                </div>
            </div>
        </div>

        <!-- Deletion Confirmation -->
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
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Delete Album?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-4">Removing <span class="font-bold text-gray-800"
                            x-text="itemToDelete"></span>. This will also delete all photos in this album.</p>
                    <div class="flex justify-center gap-3">
                        <button @click="deleteModalOpen = false"
                            class="flex-1 px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 bg-white hover:bg-gray-50 transition-all">Cancel</button>
                        <button @click="executeDelete()"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-100">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function galleryManager() {
                return {
                    search: '{{ $search ?? '' }}',
                    category: 'all',
                    loading: false,
                    modalOpen: false,
                    modalTitle: '',
                    deleteModalOpen: false,
                    itemToDelete: '',
                    deleteUrl: '',

                    init() {
                        this.$watch('search', () => this.fetchData());
                        this.interceptPagination();
                    },

                    setCategory(cat) {
                        this.category = cat;
                        this.fetchData();
                    },

                    async fetchData(url = null) {
                        this.loading = true;
                        if (!url) {
                            url = new URL(window.location.origin + window.location.pathname);
                            if (this.search) url.searchParams.set('search', this.search);
                            if (this.category !== 'all') url.searchParams.set('category', this.category);
                        }
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await response.text();

                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newGrid = doc.getElementById('album-grid');
                            if (newGrid) {
                                document.getElementById('album-grid').innerHTML = newGrid.innerHTML;
                            } else {
                                document.getElementById('album-grid').innerHTML = html;
                            }

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
                        document.getElementById('modal-content').innerHTML = '<div class="flex flex-col items-center justify-center py-32 gap-6"><div class="w-16 h-16 border-4 border-gray-100 border-t-brand-600 rounded-full animate-spin"></div><p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">Accessing Server...</p></div>';
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await response.text();
                            document.getElementById('modal-content').innerHTML = html;

                            const form = document.getElementById('album-form');
                            if (form) {
                                form.onsubmit = async (e) => {
                                    e.preventDefault();
                                    const formData = new FormData(form);
                                    try {
                                        const saveRes = await fetch(form.action, {
                                            method: 'POST',
                                            body: formData,
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                            }
                                        });
                                        const data = await saveRes.json();
                                        if (saveRes.ok) {
                                            this.modalOpen = false;
                                            this.fetchData();
                                            this.showFlash(data.success);
                                        }
                                    } catch (err) { console.error(err); }
                                };
                            }
                        } catch (error) { this.modalOpen = false; }
                    },

                    closeModal() { this.modalOpen = false; },

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
                        flash.innerHTML = `<div class="fixed top-8 right-8 z-[300] bg-gray-900 border border-white/10 backdrop-blur-xl text-white px-10 py-5 rounded-[2rem] shadow-2xl animate-in slide-in-from-right duration-500 flex items-center gap-6">
                                                    <div class="p-3 bg-green-500 rounded-2xl shadow-lg shadow-green-500/20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg></div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Transmission Successful</span>
                                                        <span class="font-black uppercase text-[11px] tracking-widest">${message}</span>
                                                    </div>
                                                </div>`;
                        setTimeout(() => flash.innerHTML = '', 4000);
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>