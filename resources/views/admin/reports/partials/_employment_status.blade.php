<div class="space-y-8">
    <div class="border-b dark:border-dark-border pb-6">
        <h2 class="text-xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest">Alumni
            Employment Distribution</h2>
        <p class="text-xs text-gray-500 dark:text-dark-text-secondary font-bold mt-1">Classification based on current
            professional records.</p>
    </div>

    @foreach($data as $status => $profiles)
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div
                        class="h-6 w-1 {{ $status === 'Employed' ? 'bg-green-500' : ($status === 'Unemployed' ? 'bg-red-500' : 'bg-blue-500') }}">
                    </div>
                    <h3 class="text-sm font-black text-gray-800 dark:text-dark-text-primary uppercase tracking-widest">
                        {{ $status }} Registry
                        ({{ $profiles->count() }})
                    </h3>
                </div>
                <div
                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-dark-bg-subtle px-3 py-1 rounded-lg">
                    Segment Detail
                </div>
            </div>

            <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                <table
                    class="w-full border-collapse bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden shadow-sm dark:shadow-none min-w-[600px] sm:min-w-full">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-dark-bg-subtle/50">
                            <th
                                class="px-5 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-widest">
                                Name
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-widest">
                                Course</th>
                            <th
                                class="px-5 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-widest">
                                Designation/Company</th>
                            <th
                                class="px-5 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-widest">
                                Work
                                Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-dark-border dark:bg-dark-bg">
                        @foreach($profiles as $profile)
                            <tr class="hover:bg-gray-50/30 dark:hover:bg-dark-state-hover transition-colors">
                                <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-dark-text-primary">
                                    {{ $profile->first_name }}
                                    {{ $profile->last_name }}
                                </td>
                                <td class="px-5 py-4 text-[11px] font-black text-brand-600 dark:text-brand-400 uppercase">
                                    {{ $profile->course->code }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-gray-800 dark:text-dark-text-primary">
                                        {{ $profile->position ?? 'N/A' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 dark:text-dark-text-muted font-bold uppercase">
                                        {{ $profile->company_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-dark-text-secondary italic">
                                    {{ $profile->work_address ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>