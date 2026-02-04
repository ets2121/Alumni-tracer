<x-layouts.admin>
    <x-slot name="header">
        Group Chat Management
    </x-slot>

    <div x-data="groupManager()" x-init="init()" x-cloak class="space-y-6">
        <!-- Top Actions & Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-2xl font-black text-gray-800 tracking-tight">Communication Channels</h3>
                <p class="text-sm text-gray-500 font-medium">Manage batch, course, and general alumni conversation
                    rooms.</p>
            </div>

            <div class="flex gap-4">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.chat-management.banned-words.index') }}"
                        class="bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 px-6 rounded-2xl transition-all shadow-sm border border-gray-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Banned Words
                    </a>
                @endif

                <button @click="openModal('{{ route('admin.chat-management.create') }}', 'Create New Group')"
                    class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-2xl transition-all shadow-xl shadow-brand-100 flex items-center justify-center gap-2 transform hover:-translate-y-0.5 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Group
                </button>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="search" placeholder="Search rooms by name or type..."
                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm font-medium">
            </div>

            <div class="flex gap-2 min-w-max">
                @php
                    $types = auth()->user()->role === 'admin'
                        ? ['all', 'admin_dept', 'batch', 'course']
                        : ['all', 'general', 'batch', 'course'];
                @endphp
                <template x-for="type in {{ json_encode($types) }}">
                    <button @click="filterType = type"
                        :class="filterType === type ? 'bg-brand-600 text-white shadow-lg shadow-brand-100' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                        class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all"
                        x-text="type === 'admin_dept' ? 'Admin Only' : type">
                    </button>
                </template>
            </div>
        </div>

        <!-- Groups Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="groups-grid">
            <template x-for="group in filteredGroups" :key="group.id">
                <div
                    class="bg-white border border-gray-100 rounded-[32px] p-6 hover:shadow-2xl transition-all duration-500 group relative flex flex-col h-full hover:border-brand-200">
                    <!-- Decor -->
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                        </svg>
                    </div>

                    <div class="flex items-start justify-between mb-6 relative">
                        <div :class="{
                            'bg-brand-600': group.type === 'batch',
                            'bg-purple-600': group.type === 'course',
                            'bg-blue-600': group.type === 'general'
                        }"
                            class="w-16 h-16 rounded-[24px] flex items-center justify-center text-white font-black text-2xl shadow-xl transform group-hover:rotate-6 transition-transform">
                            <span x-text="group.name.charAt(0)"></span>
                        </div>

                        <div
                            class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                            <a :href="'{{ url('admin/chat-management') }}/' + group.id"
                                class="p-2.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <form :action="'{{ url('admin/chat-management') }}/' + group.id" method="POST"
                                @submit.prevent="if(confirm('Delete this room?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="flex-1">
                        <h4 class="font-black text-gray-900 text-lg line-clamp-1 mb-1" x-text="group.name"></h4>
                        <div class="flex items-center gap-2 mb-4">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border border-gray-100 text-gray-400 group-hover:border-brand-200 group-hover:text-brand-600 transition-colors"
                                x-text="group.type"></span>
                            <span x-show="group.is_private"
                                class="text-[10px] bg-amber-50 text-amber-600 px-2 py-1 rounded-full font-black uppercase tracking-tighter">Private</span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-3 mb-6 leading-relaxed font-medium"
                            x-text="group.description || 'System generated channel for community discussion.'"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-6 border-t border-gray-50 mt-auto">
                        <div class="bg-gray-50 rounded-2xl p-3 group-hover:bg-brand-50 transition-colors">
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter mb-1">Potential
                            </p>
                            <p class="text-lg font-black text-gray-900 group-hover:text-brand-600 transition-colors"
                                x-text="group.members_count"></p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-3">
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter mb-1">Messages</p>
                            <p class="text-lg font-black text-gray-900" x-text="group.messages_count"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredGroups.length === 0"
            class="text-center py-24 bg-white rounded-[40px] border border-gray-100 shadow-sm">
            <div
                class="w-24 h-24 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-6 text-brand-600">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <h4 class="text-xl font-black text-gray-900">No matching channels found</h4>
            <p class="text-gray-500 max-w-sm mx-auto mt-2 font-medium">Try adjusting your search query or type filters
                to see other rooms.</p>
        </div>

        <!-- Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-12 text-center sm:p-0">
                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" @click="closeModal()"
                    class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

                <div x-show="modalOpen" x-transition:enter="ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-20 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    class="inline-block w-full max-w-2xl text-left align-middle transition-all transform bg-white shadow-3xl rounded-[40px] overflow-hidden relative">

                    <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 leading-tight" x-text="modalTitle"></h3>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Configure Room
                                Settings</p>
                        </div>
                        <button @click="closeModal()"
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-2xl transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-8 sm:p-10" id="modal-content">
                        <!-- Content loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function groupManager() {
                return {
                    modalOpen: false,
                    modalTitle: '',
                    saving: false,
                    search: '',
                    filterType: 'all',
                    groups: {!! json_encode($groups) !!},

                    init() {
                        console.log('Group Manager Polished initialized');
                    },

                    get filteredGroups() {
                        return this.groups.filter(g => {
                            const matchesSearch = g.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                g.type.toLowerCase().includes(this.search.toLowerCase());
                            const matchesType = this.filterType === 'all' || g.type === this.filterType;
                            return matchesSearch && matchesType;
                        });
                    },

                    async openModal(url, title) {
                        this.modalTitle = title;
                        this.modalOpen = true;
                        document.getElementById('modal-content').innerHTML = `
                                <div class="flex flex-col items-center justify-center py-20 text-center">
                                    <div class="relative w-16 h-16 mb-4">
                                        <div class="absolute inset-0 border-4 border-brand-100 rounded-full"></div>
                                        <div class="absolute inset-0 border-4 border-brand-600 rounded-full border-t-transparent animate-spin"></div>
                                    </div>
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Loading Configuration...</p>
                                </div>
                            `;
                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            document.getElementById('modal-content').innerHTML = await response.text();

                            const form = document.getElementById('chat-group-form');
                            if (form) {
                                form.onsubmit = async (e) => {
                                    e.preventDefault();
                                    await this.saveForm(e.target);
                                };
                            }
                        } catch (error) { this.modalOpen = false; }
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    async saveForm(form) {
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
                            if (response.ok) {
                                window.location.reload();
                            }
                        } catch (error) { console.error('Save failed:', error); }
                        finally { this.saving = false; }
                    }
                }
            }
        </script>
    @endpush

    <style>
        [x-cloak] {
            display: none !important;
        }

        .shadow-3xl {
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
        }
    </style>
</x-layouts.admin>