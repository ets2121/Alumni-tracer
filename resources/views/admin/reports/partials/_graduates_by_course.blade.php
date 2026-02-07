<div class="space-y-8">
    <div class="border-b dark:border-dark-border pb-6">
        <h2 class="text-xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest">Graduates by
            Course and Year</h2>
        <p class="text-xs text-gray-500 dark:text-dark-text-secondary font-bold mt-1">Total Records Found:
            {{ $data->count() }}</p>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-dark-bg-subtle/50 border-b-2 border-gray-200 dark:border-dark-border">
                <th
                    class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                    Course
                    Code</th>
                <th
                    class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                    Batch Year
                </th>
                <th
                    class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                    Alumni
                    Name</th>
                <th
                    class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                    Contact
                </th>
                <th
                    class="px-4 py-3 text-left text-[10px] font-black text-gray-400 dark:text-dark-text-muted uppercase tracking-wider">
                    Employment
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
            @php $currentCourse = null; @endphp
            @foreach($data as $profile)
                @if($currentCourse !== $profile->course_id)
                    <tr class="bg-brand-50/30 dark:bg-brand-900/10">
                        <td colspan="5"
                            class="px-4 py-2 text-xs font-black text-brand-700 dark:text-brand-300 uppercase tracking-tighter">
                            {{ $profile->course->name }} ({{ $profile->course->code }})
                        </td>
                    </tr>
                    @php $currentCourse = $profile->course_id; @endphp
                @endif
                <tr class="hover:bg-gray-50/50 dark:hover:bg-dark-state-hover transition-colors">
                    <td class="px-4 py-4 text-[11px] font-bold text-gray-500 dark:text-dark-text-secondary uppercase">
                        {{ $profile->course->code }}</td>
                    <td class="px-4 py-4 text-[11px] font-black text-gray-900 dark:text-dark-text-primary">
                        {{ $profile->batch_year }}</td>
                    <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-dark-text-primary">
                        {{ $profile->first_name }}
                        {{ $profile->last_name }}
                    </td>
                    <td class="px-4 py-4 text-[11px] text-gray-500 dark:text-dark-text-muted">
                        {{ $profile->user->email }}<br>{{ $profile->contact_number }}
                    </td>
                    <td class="px-4 py-4">
                        <span
                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter
                                    {{ $profile->employment_status === 'Employed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                            {{ $profile->employment_status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>