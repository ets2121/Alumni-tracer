<div class="space-y-6">
    <div
        class="bg-white dark:bg-dark-bg-elevated rounded-xl shadow-sm border border-gray-100 dark:border-dark-border p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-border">
                <thead class="bg-gray-50 dark:bg-dark-bg-subtle">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            ID</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider whitespace-nowrap">
                            <button @click="sortBy('name')"
                                class="flex items-center gap-1 hover:text-brand-600 uppercase">
                                Name
                                <template x-if="sort === 'name'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path :d="direction === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z'" />
                                    </svg>
                                </template>
                            </button>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider whitespace-nowrap">
                            Course</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider whitespace-nowrap">
                            Year Graduated</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider whitespace-nowrap">
                            Current Work</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider whitespace-nowrap">
                            Contact Number</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider whitespace-nowrap">
                            Status</th>
                        <th
                            class="sticky right-0 top-0 bg-gray-50 dark:bg-dark-bg-subtle z-10 px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.05)]">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-dark-bg divide-y divide-gray-100 dark:divide-dark-border relative">
                    <!-- Skeleton Loader -->
                    <template x-if="loading && !data">
                        <template x-for="i in 5">
                            <tr class="animate-pulse">
                                <td class="px-6 py-3">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-12 rounded"></div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 mr-3"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 w-32 rounded"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-16 rounded"></div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-12 rounded"></div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-24 rounded"></div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-24 rounded"></div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="h-6 bg-gray-200 dark:bg-gray-700 w-16 rounded-full"></div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-8 ml-auto rounded"></div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <!-- Data Rows -->
                    <template x-if="data">
                        <template x-for="user in data.data" :key="user.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-state-hover transition-colors group">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-dark-text-muted font-mono"
                                    x-text="'#' + String(user.id).padStart(5, '0')"></td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-8 h-8 me-3">
                                            <template x-if="user.avatar">
                                                <img class="w-8 h-8 rounded-full object-cover border border-gray-200 shadow-sm"
                                                    :src="'/storage/' + user.avatar" alt="">
                                            </template>
                                            <template x-if="!user.avatar">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-300 dark:text-brand-400">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-dark-text-primary group-hover:text-brand-600 transition-colors"
                                            x-text="user.name"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm">
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 uppercase"
                                        x-text="user.alumni_profile?.course?.code || 'N/A'"></span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-dark-text-secondary font-medium"
                                    x-text="user.alumni_profile?.batch_year || 'N/A'"></td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-dark-text-muted"
                                    x-text="user.alumni_profile?.position || 'Not specified'"></td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-dark-text-muted"
                                    x-text="user.alumni_profile?.contact_number || 'N/A'"></td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-black rounded-full bg-green-100 text-green-700 uppercase tracking-wider">Active</span>
                                </td>
                                <td
                                    class="sticky right-0 bg-white dark:bg-dark-bg-elevated group-hover:bg-gray-50 dark:group-hover:bg-dark-state-hover z-10 px-6 py-3 whitespace-nowrap text-right text-sm shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.05)]">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <button
                                            @click="openModal(`{{ url('admin/alumni') }}/${user.id}`, 'Alumni Profile Detail')"
                                            class="p-1.5 text-gray-400 dark:text-dark-text-muted hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 rounded-lg transition-all"
                                            title="View Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="openModal(`{{ url('admin/alumni') }}/${user.id}/edit`, 'Edit Alumni Record', true)"
                                            class="p-1.5 text-gray-400 dark:text-dark-text-muted hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 rounded-lg transition-all"
                                            title="Edit Record">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="$dispatch('open-confirmation-modal', { title: 'Delete Alumni Record', message: `Are you sure you want to delete ${user.name}?`, action: `{{ url('admin/alumni') }}/${user.id}`, method: 'DELETE', danger: true, confirmText: 'Delete Record' })"
                                            class="p-1.5 text-gray-400 dark:text-dark-text-muted hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                            title="Delete Record">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template x-if="data && data.data.length === 0">
                        <tr>
                            <td colspan="8"
                                class="px-6 py-12 text-center text-gray-500 dark:text-dark-text-muted italic bg-gray-50/50 dark:bg-dark-bg-subtle/50 rounded-xl">
                                No active alumni records found.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <template x-if="data && data.links">
            <div class="mt-4">
                <div class="flex justify-between items-center sm:hidden">
                    <button x-show="data.prev_page_url" @click="fetchData(data.prev_page_url)"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                    <button x-show="data.next_page_url" @click="fetchData(data.next_page_url)"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-dark-text-muted">
                            Showing <span class="font-medium" x-text="data.from"></span> to <span class="font-medium"
                                x-text="data.to"></span> of <span class="font-medium" x-text="data.total"></span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <template x-for="(link, index) in data.links" :key="index">
                                <button @click="link.url ? fetchData(link.url) : null"
                                    :disabled="!link.url || link.active"
                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                    :class="link.active 
                                    ? 'z-10 bg-brand-50 border-brand-500 text-brand-600' 
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-dark-bg dark:border-dark-border dark:text-dark-text-muted'"
                                    x-html="link.label">
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>