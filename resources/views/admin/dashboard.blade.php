<x-layouts.admin>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div x-data="adminDashboard" class="space-y-6">
        <!-- Count Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Alumni Total -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6 transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-gray-500 dark:text-dark-text-muted text-xs font-bold uppercase tracking-wider">Total
                        Alumni</h3>
                    <div class="p-2 bg-brand-50 dark:bg-brand-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <template x-if="loading.counts">
                    <div class="h-10 w-24 bg-gray-100 dark:bg-dark-bg-subtle animate-pulse rounded-lg mt-2"></div>
                </template>
                <template x-if="!loading.counts">
                    <p class="text-4xl font-black text-brand-600 mt-2" x-text="counts.alumni_total.toLocaleString()">
                    </p>
                </template>
                <div class="mt-4 flex items-center text-xs text-gray-500 dark:text-dark-text-muted">
                    <span class="text-green-500 font-bold" x-text="counts.alumni_verified.toLocaleString()"></span>
                    <span class="ml-1">verified</span>
                    <span class="mx-2">•</span>
                    <span class="text-amber-500 font-bold" x-text="counts.alumni_pending.toLocaleString()"></span>
                    <span class="ml-1">pending</span>
                </div>
            </div>

            <!-- Department Admins -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-gray-500 dark:text-dark-text-muted text-xs font-bold uppercase tracking-wider">Dept
                        Admins</h3>
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
                <template x-if="loading.counts">
                    <div class="h-10 w-24 bg-gray-100 dark:bg-dark-bg-subtle animate-pulse rounded-lg mt-2"></div>
                </template>
                <template x-if="!loading.counts">
                    <p class="text-4xl font-black text-purple-600 mt-2" x-text="counts.dept_admins.toLocaleString()">
                    </p>
                </template>
                <p class="mt-4 text-xs text-gray-500 dark:text-dark-text-muted">Across <span
                        class="font-bold text-gray-700 dark:text-dark-text-primary"
                        x-text="counts.total_departments"></span> departments</p>
            </div>

            <!-- Events Summary -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-gray-500 dark:text-dark-text-muted text-xs font-bold uppercase tracking-wider">
                        Active Events</h3>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <template x-if="loading.counts">
                    <div class="h-10 w-24 bg-gray-100 dark:bg-dark-bg-subtle animate-pulse rounded-lg mt-2"></div>
                </template>
                <template x-if="!loading.counts">
                    <p class="text-4xl font-black text-blue-600 mt-2" x-text="counts.active_events.toLocaleString()">
                    </p>
                </template>
                <div class="mt-4 flex items-center text-xs text-gray-500 dark:text-dark-text-muted">
                    <span class="text-blue-500 font-bold" x-text="counts.upcoming_events.toLocaleString()"></span>
                    <span class="ml-1">upcoming</span>
                    <span class="mx-2">•</span>
                    <span class="text-gray-400 font-bold" x-text="counts.past_events.toLocaleString()"></span>
                    <span class="ml-1">past</span>
                </div>
            </div>

            <!-- Quick Insight (Ratio) -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-gray-500 dark:text-dark-text-muted text-xs font-bold uppercase tracking-wider">
                        Verification Rate</h3>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <template x-if="loading.counts">
                    <div class="h-10 w-24 bg-gray-100 dark:bg-dark-bg-subtle animate-pulse rounded-lg mt-2"></div>
                </template>
                <template x-if="!loading.counts">
                    <p class="text-4xl font-black text-emerald-600 mt-2">
                        <span
                            x-text="counts.alumni_total > 0 ? Math.round((counts.alumni_verified / counts.alumni_total) * 100) : 0"></span>%
                    </p>
                </template>
                <div class="mt-4 w-full bg-gray-100 dark:bg-dark-bg-subtle rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-full transition-all duration-1000"
                        :style="`width: ${counts.alumni_total > 0 ? (counts.alumni_verified / counts.alumni_total) * 100 : 0}%`">
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Registration Trends -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <h3 class="text-gray-800 dark:text-dark-text-primary text-sm font-bold mb-6 flex items-center">
                    <span class="w-1.5 h-6 bg-brand-500 rounded-full mr-3"></span>
                    Alumni Registration Trends (Last 12 Months)
                </h3>
                <div class="h-72 relative">
                    <template x-if="loading.charts">
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-gray-50/50 dark:bg-dark-bg-subtle/20 rounded-xl">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
                        </div>
                    </template>
                    <canvas x-ref="registrationTrendsChart"></canvas>
                </div>
            </div>

            <!-- Alumni by Department -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <h3 class="text-gray-800 dark:text-dark-text-primary text-sm font-bold mb-6 flex items-center">
                    <span class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></span>
                    Alumni per Department
                </h3>
                <div class="h-72 relative">
                    <template x-if="loading.charts">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                        </div>
                    </template>
                    <canvas x-ref="alumniByDeptChart"></canvas>
                </div>
            </div>

            <!-- Employment Status -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <h3 class="text-gray-800 dark:text-dark-text-primary text-sm font-bold mb-6 flex items-center">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full mr-3"></span>
                    Employment Status Distribution
                </h3>
                <div class="h-72 relative">
                    <template x-if="loading.charts">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>
                        </div>
                    </template>
                    <canvas x-ref="employmentStatusChart"></canvas>
                </div>
            </div>

            <!-- Civil Status -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <h3 class="text-gray-800 dark:text-dark-text-primary text-sm font-bold mb-6 flex items-center">
                    <span class="w-1.5 h-6 bg-amber-500 rounded-full mr-3"></span>
                    Civil Status Distribution
                </h3>
                <div class="h-72 relative">
                    <template x-if="loading.charts">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
                        </div>
                    </template>
                    <canvas x-ref="civilStatusChart"></canvas>
                </div>
            </div>

            <!-- Gender Distribution -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <h3 class="text-gray-800 dark:text-dark-text-primary text-sm font-bold mb-6 flex items-center">
                    <span class="w-1.5 h-6 bg-pink-500 rounded-full mr-3"></span>
                    Gender Distribution
                </h3>
                <div class="h-64 relative">
                    <template x-if="loading.charts">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-pink-500"></div>
                        </div>
                    </template>
                    <canvas x-ref="genderDistChart"></canvas>
                </div>
            </div>

            <!-- Employment Type -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
                <h3 class="text-gray-800 dark:text-dark-text-primary text-sm font-bold mb-6 flex items-center">
                    <span class="w-1.5 h-6 bg-cyan-500 rounded-full mr-3"></span>
                    Employment Type Distribution
                </h3>
                <div class="h-64 relative">
                    <template x-if="loading.charts">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-500"></div>
                        </div>
                    </template>
                    <canvas x-ref="employmentTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Users Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Verified Users -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b dark:border-dark-border">
                    <h3 class="text-gray-900 dark:text-dark-text-primary text-sm font-bold flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Recent Verified Alumni
                    </h3>
                </div>
                <div class="flex-grow">
                    <template x-if="loading.recentUsers">
                        <div class="p-6 space-y-4">
                            <template x-for="i in 5" :key="i">
                                <div class="flex items-center space-x-3 animate-pulse">
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-dark-bg-subtle rounded-full"></div>
                                    <div class="flex-grow">
                                        <div class="h-3 bg-gray-100 dark:bg-dark-bg-subtle rounded w-32 mb-2"></div>
                                        <div class="h-2 bg-gray-50 dark:bg-dark-bg-subtle rounded w-20"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!loading.recentUsers">
                        <div class="divide-y dark:divide-dark-border overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr
                                        class="bg-gray-50 dark:bg-dark-bg-subtle/50 text-gray-500 dark:text-dark-text-muted">
                                        <th class="px-6 py-3 font-bold uppercase tracking-wider text-[10px]">User</th>
                                        <th class="px-6 py-3 font-bold uppercase tracking-wider text-[10px]">Department
                                        </th>
                                        <th class="px-6 py-3 font-bold uppercase tracking-wider text-[10px] text-right">
                                            Joined</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y dark:divide-dark-border">
                                    <template x-for="user in recentUsers.verified" :key="user.email">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg-subtle/30 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <template x-if="user.avatar">
                                                            <img class="h-8 w-8 rounded-full border dark:border-dark-border shadow-sm"
                                                                :src="`/storage/${user.avatar}`" :alt="user.name">
                                                        </template>
                                                        <template x-if="!user.avatar">
                                                            <div class="h-8 w-8 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-600 font-bold text-xs"
                                                                x-text="user.name.charAt(0)"></div>
                                                        </template>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-xs font-bold text-gray-900 dark:text-dark-text-primary"
                                                            x-text="user.name"></div>
                                                        <div class="text-[10px] text-gray-400 dark:text-dark-text-muted"
                                                            x-text="user.email"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/30"
                                                    x-text="user.department || 'N/A'"></span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-[11px] text-gray-400 dark:text-dark-text-muted whitespace-nowrap"
                                                x-text="user.created_at"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <template x-if="recentUsers.verified.length === 0">
                                <div class="p-8 text-center text-gray-400 dark:text-dark-text-muted text-xs italic">No
                                    verified alumni found.</div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Recent Pending Users -->
            <div
                class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b dark:border-dark-border">
                    <h3 class="text-gray-900 dark:text-dark-text-primary text-sm font-bold flex items-center">
                        <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                        Pending Verifications
                    </h3>
                </div>
                <div class="flex-grow">
                    <template x-if="loading.recentUsers">
                        <div class="p-6 space-y-4">
                            <template x-for="i in 5" :key="i">
                                <div class="flex items-center space-x-3 animate-pulse">
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-dark-bg-subtle rounded-full"></div>
                                    <div class="flex-grow">
                                        <div class="h-3 bg-gray-100 dark:bg-dark-bg-subtle rounded w-32 mb-2"></div>
                                        <div class="h-2 bg-gray-50 dark:bg-dark-bg-subtle rounded w-20"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!loading.recentUsers">
                        <div class="divide-y dark:divide-dark-border overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr
                                        class="bg-gray-50 dark:bg-dark-bg-subtle/50 text-gray-500 dark:text-dark-text-muted">
                                        <th class="px-6 py-3 font-bold uppercase tracking-wider text-[10px]">User</th>
                                        <th class="px-6 py-3 font-bold uppercase tracking-wider text-[10px]">Department
                                        </th>
                                        <th class="px-6 py-3 font-bold uppercase tracking-wider text-[10px] text-right">
                                            Requested</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y dark:divide-dark-border">
                                    <template x-for="user in recentUsers.pending" :key="user.email">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg-subtle/30 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <template x-if="user.avatar">
                                                            <img class="h-8 w-8 rounded-full border dark:border-dark-border shadow-sm"
                                                                :src="`/storage/${user.avatar}`" :alt="user.name">
                                                        </template>
                                                        <template x-if="!user.avatar">
                                                            <div class="h-8 w-8 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-600 font-bold text-xs"
                                                                x-text="user.name.charAt(0)"></div>
                                                        </template>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-xs font-bold text-gray-900 dark:text-dark-text-primary"
                                                            x-text="user.name"></div>
                                                        <div class="text-[10px] text-gray-400 dark:text-dark-text-muted"
                                                            x-text="user.email"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/30"
                                                    x-text="user.department || 'N/A'"></span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-[11px] text-gray-400 dark:text-dark-text-muted whitespace-nowrap"
                                                x-text="user.created_at"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <template x-if="recentUsers.pending.length === 0">
                                <div class="p-8 text-center text-gray-400 dark:text-dark-text-muted text-xs italic">No
                                    pending verifications.</div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-dark-bg-subtle/30 border-t dark:border-dark-border text-center">
                    <a href="{{ route('admin.pre-registration.index') }}"
                        class="text-[11px] font-bold text-brand-600 hover:text-brand-700 uppercase tracking-widest transition-colors flex items-center justify-center">
                        Manage Requests
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Role Badge -->
        <div
            class="bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-gray-900 dark:text-dark-text-primary font-bold">
                        @if(Auth::user()->isDepartmentAdmin())
                            Department Administrator
                        @else
                            System Administrator
                        @endif
                    </h4>
                    <p class="text-gray-500 dark:text-dark-text-muted text-sm italic">
                        @if(Auth::user()->isDepartmentAdmin())
                            Managing {{ Auth::user()->department_name }}
                        @else
                            Full System Access
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>