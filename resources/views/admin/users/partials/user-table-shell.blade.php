<div class="overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Name / Email
                </th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Role
                </th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Department
                </th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Status
                </th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="relative">
            <!-- Loading Skeleton -->
            <template x-if="loading && !data">
                <template x-for="i in 5">
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg">
                            <div class="flex items-center animate-pulse">
                                <div class="rounded-full bg-gray-200 dark:bg-gray-700 h-10 w-10"></div>
                                <div class="ml-3 space-y-2">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 w-24 rounded"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 w-32 rounded"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 w-16 rounded-full animate-pulse"></div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 w-20 rounded animate-pulse"></div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 w-12 rounded-full animate-pulse"></div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg">
                            <div class="flex gap-2 justify-end animate-pulse">
                                <div class="h-4 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-4 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </div>
                        </td>
                    </tr>
                </template>
            </template>

            <!-- Data Rows -->
            <template x-if="data">
                <template x-for="user in data.data" :key="user.id">
                    <tr>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <img class="w-full h-full rounded-full object-cover border-2 border-brand-50 dark:border-dark-border"
                                        :src="user.avatar ? `/storage/${user.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=F9FAFB&background=10B981`"
                                        alt="">
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 dark:text-dark-text-primary whitespace-no-wrap font-bold capitalize"
                                        x-text="user.name"></p>
                                    <p class="text-gray-500 dark:text-dark-text-muted whitespace-no-wrap text-xs"
                                        x-text="user.email"></p>
                                </div>
                            </div>
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                            <span class="px-3 py-1 text-[10px] font-bold rounded-full uppercase" :class="user.role === 'admin' 
                                    ? 'bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300' 
                                    : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'"
                                x-text="user.role === 'admin' ? 'System Admin' : 'Dept Admin'">
                            </span>
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                            <template x-if="user.department_name">
                                <span
                                    class="text-[11px] font-black text-gray-900 dark:text-dark-text-primary border border-gray-900 dark:border-dark-text-primary px-2 py-0.5 rounded italic"
                                    x-text="user.department_name"></span>
                            </template>
                            <template x-if="!user.department_name">
                                <span class="text-gray-400 dark:text-dark-text-muted text-xs italic">System Wide</span>
                            </template>
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                            <span
                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 uppercase"
                                x-text="user.status"></span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-dark-bg text-sm text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button
                                    @click="openModal('{{ route('admin.users.index') }}/' + user.id, 'Edit User Account', user.id)"
                                    class="text-brand-600 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300 font-bold text-xs uppercase transition-colors">
                                    Edit
                                </button>

                                <template x-if="user.id !== {{ auth()->id() }}">
                                    <button @click="$dispatch('open-confirmation-modal', { 
                                        title: 'Delete User Account', 
                                        message: `Are you sure you want to delete ${user.name}? This action cannot be undone.`, 
                                        action: `{{ route('admin.users.index') }}/${user.id}`, 
                                        method: 'DELETE', 
                                        danger: true, 
                                        confirmText: 'Delete Account' 
                                    })"
                                        class="text-red-500 hover:text-red-700 font-bold text-xs uppercase transition-colors">
                                        Delete
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
            </template>

            <template x-if="data && data.data.length === 0">
                <tr>
                    <td colspan="5"
                        class="px-5 py-10 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm text-center text-gray-500 dark:text-dark-text-muted italic">
                        No administrative users found.
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>


<template x-if="data && data.links">
    <div class="mt-4">
        <!-- Simplified Pagination for Alpine (mimic Laravel Links) -->
        <div class="flex justify-between items-center sm:hidden">
            <button x-show="data.prev_page_url" @click="fetchData(data.prev_page_url)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Previous
            </button>
            <button x-show="data.next_page_url" @click="fetchData(data.next_page_url)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Next
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-dark-text-muted">
                    Showing
                    <span class="font-medium" x-text="data.from"></span>
                    to
                    <span class="font-medium" x-text="data.to"></span>
                    of
                    <span class="font-medium" x-text="data.total"></span>
                    results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <template x-for="(link, index) in data.links" :key="index">
                        <button @click="link.url ? fetchData(link.url) : null" :disabled="!link.url || link.active"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                            :class="link.active 
                            ? 'z-10 bg-brand-50 border-brand-500 text-brand-600' 
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-dark-bg dark:border-dark-border dark:text-dark-text-muted'" x-html="link.label">
                        </button>
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>