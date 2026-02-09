<x-layouts.admin>
    <div class="py-6" x-data="courseManager()" x-init="init()">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Academic Programs</h2>
                <p class="text-sm text-gray-500">Manage course offerings and track alumni per program.</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Search Form -->
                <div class="relative max-w-xs w-full">
                    <input type="text" x-model.debounce.300ms="search" placeholder="Search by name or code..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm transition-all text-gray-700">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <button @click="openModal('{{ route('admin.courses.create') }}', 'Initialize Program')"
                    class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2 rounded-xl text-sm font-bold transition-all shadow-lg shadow-brand-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Program
                </button>
            </div>
        </div>

        <div id="flash-message" class="fixed top-6 right-6 z-[100] w-full max-w-sm"></div>

        <div id="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }"
            class="transition-opacity duration-200">
            @include('admin.courses.partials._table')
        </div>

        <!-- Course Form Modal -->
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
                    <div id="modal-content"></div>
                </div>
            </div>
        </div>


    </div>

    @push('scripts')
        <!-- Migrated to course-manager.js -->
    @endpush
</x-layouts.admin>