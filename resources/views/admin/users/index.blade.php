<x-layouts.admin>
    <div class="py-6" x-data="userManager()" x-init="init()">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Admin & Moderator Accounts</h2>
                <p class="text-sm text-gray-500">Manage system-wide and department-specific administrative users.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input type="text" x-model.debounce.300ms="search" placeholder="Search by name or email..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm transition-all text-gray-700">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <button @click="openModal('{{ route('admin.users.create') }}', 'New User Account')"
                    class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2 rounded-xl text-sm font-bold transition-all shadow-lg shadow-brand-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Account
                </button>
            </div>
        </div>

        <div id="flash-message" class="fixed top-6 right-6 z-[100] w-full max-w-sm"></div>

        <div id="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
            class="transition-opacity duration-200">
            @include('admin.users.partials._table')
        </div>

        <!-- User Form Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="modal-backdrop" @click="closeModal()" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="modal-content-container sm:max-w-lg sm:w-full" x-show="modalOpen"
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
                    <div id="modal-content">
                        @include('admin.users.partials._form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function userManager() {
                return {
                    search: '{{ $search ?? '' }}',
                    loading: false,
                    saving: false,
                    modalOpen: false,
                    modalTitle: '',
                    editMode: false,
                    actionUrl: '{{ route('admin.users.store') }}',
                    formData: {
                        id: '',
                        name: '',
                        email: '',
                        role: 'admin',
                        department_name: '',
                        password: '',
                        password_confirmation: ''
                    },

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

                    openModal(url, title, userId = null) {
                        this.modalTitle = title;
                        this.modalOpen = true;
                        this.resetForm();

                        if (userId) {
                            this.editMode = true;
                            this.actionUrl = url;
                            this.fetchUser(userId);
                        } else {
                            this.editMode = false;
                            this.actionUrl = '{{ route('admin.users.store') }}';
                        }
                    },

                    async fetchUser(id) {
                        try {
                            const response = await fetch(`{{ url('admin/users') }}/${id}/edit`);
                            const user = await response.json();
                            this.formData.id = user.id;
                            this.formData.name = user.name;
                            this.formData.email = user.email;
                            this.formData.role = user.role;
                            this.formData.department_name = user.department_name ?? '';
                        } catch (error) { console.error('Failed to fetch user:', error); }
                    },

                    resetForm() {
                        this.formData = {
                            id: '',
                            name: '',
                            email: '',
                            role: 'admin',
                            department_name: '',
                            password: '',
                            password_confirmation: ''
                        };
                        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                    },

                    closeModal() { this.modalOpen = false; },

                    async saveForm() {
                        this.saving = true;
                        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

                        const method = this.editMode ? 'PUT' : 'POST';

                        try {
                            const response = await fetch(this.actionUrl, {
                                method: 'POST',
                                body: JSON.stringify({
                                    ...this.formData,
                                    _method: method
                                }),
                                headers: {
                                    'Content-Type': 'application/json',
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
                                        const errorEl = document.querySelector(`.error-message[data-field="${field}"]`);
                                        if (errorEl) errorEl.textContent = data.errors[field][0];
                                    });
                                }
                            }
                        } catch (error) { console.error('Save failed:', error); }
                        finally { this.saving = false; }
                    },

                    showFlash(message, type = 'success') {
                        const flash = document.getElementById('flash-message');
                        const bgClass = type === 'success' ? 'bg-brand-600/90' : 'bg-red-600/90';
                        flash.innerHTML = `<div class="${bgClass} backdrop-blur-md text-white px-6 py-4 rounded-2xl mb-6 shadow-2xl flex items-center gap-3 animate-fade-in-down border border-white/20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold">${message}</span></div>`;
                        setTimeout(() => flash.innerHTML = '', 4000);
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>