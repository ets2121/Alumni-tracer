<x-layouts.admin>
    <div class="py-2" x-data="alumniManager()" x-init="init()">
        <!-- Sticky Sub-header -->
        <div
            class="sticky top-0 z-10 bg-gray-50/95 backdrop-blur-sm py-4 mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-200/50">
            <h2 class="text-xl font-bold text-gray-800">Alumni Records</h2>

            <!-- Actions -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input type="text" x-model.debounce.300ms="search" placeholder="Search alumni..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm transition-all hover:bg-white">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <button @click="openModal('{{ route('admin.alumni.create') }}', 'Register New Alumni')"
                    class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-colors shadow-sm text-sm font-medium whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Alumni
                </button>
            </div>
        </div>

        <div id="flash-message"></div>

        <div id="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
            class="transition-opacity duration-200">
            @include('admin.alumni.partials._table_content')
        </div>

        <!-- Main Modal (Profile View / Edit) -->
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
            function alumniManager() {
                return {
                    search: '{{ $search ?? '' }}',
                    sort: '{{ $sortBy ?? 'name' }}',
                    direction: '{{ $sortDir ?? 'asc' }}',
                    loading: false,
                    modalOpen: false,
                    modalTitle: '',
                    deleteModalOpen: false,
                    itemToDelete: '',
                    deleteUrl: '',
                    saving: false,

                    init() {
                        this.$watch('search', () => this.fetchData());
                        this.interceptPagination();
                    },

                    sortBy(field) {
                        if (this.sort === field) {
                            this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sort = field;
                            this.direction = 'asc';
                        }
                        this.fetchData();
                    },

                    async fetchData(url = null) {
                        this.loading = true;
                        if (!url) {
                            url = new URL(window.location.origin + window.location.pathname);
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



                    showFlash(message) {
                        const flash = document.getElementById('flash-message');
                        flash.innerHTML = `<div class="bg-brand-600/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl mb-6 shadow-xl flex items-center gap-3 animate-fade-in-down"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold">${message}</span></div>`;
                        setTimeout(() => flash.innerHTML = '', 4000);
                    },

                    async submitAlumniForm(event) {
                        const form = event.target;
                        const formData = new FormData(form);
                        const submitBtn = form.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerText;

                        submitBtn.disabled = true;
                        submitBtn.innerText = 'Registering...';

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            
                            const data = await response.json();

                            if (data.success) {
                                this.showFlash('Alumni registered successfully!');
                                this.closeModal();
                                this.fetchData();
                            } else {
                                alert('Error: ' + JSON.stringify(data));
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred. Please check your inputs.');
                        } finally {
                            submitBtn.disabled = false;
                            submitBtn.innerText = originalText;
                        }
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>