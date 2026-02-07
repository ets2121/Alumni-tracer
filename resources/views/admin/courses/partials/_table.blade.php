<div class="overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Code</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Name</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Category</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Department</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Alumni Count</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $course)
                <tr>
                    <td
                        class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm font-medium text-gray-900 dark:text-dark-text-primary uppercase">
                        {{ $course->code }}
                    </td>
                    <td
                        class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm text-gray-700 dark:text-dark-text-secondary">
                        <div class="font-bold">{{ $course->name }}</div>
                        <div class="text-xs text-gray-400 dark:text-dark-text-muted line-clamp-1">{{ $course->description }}
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        <span
                            class="px-2 py-1 text-[10px] font-bold rounded-full 
                                            {{ $course->category === 'Graduate' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : ($course->category === 'Certificate' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300') }}">
                            {{ $course->category }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        <span
                            class="text-[11px] font-black text-gray-900 dark:text-dark-text-primary border border-gray-900 dark:border-dark-text-primary px-2 py-0.5 rounded italic">
                            {{ $course->department_name }}
                        </span>
                    </td>
                    <td
                        class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm text-gray-600 dark:text-dark-text-secondary font-bold">
                        {{ $course->alumni_count }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        <div class="flex items-center gap-3">
                            <button @click="openModal('{{ route('admin.courses.edit', $course->id) }}', 'Edit Course')"
                                class="text-brand-600 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300 font-bold text-xs uppercase transition-colors">Edit</button>
                            <button @click="$dispatch('open-confirmation-modal', { 
                                                title: 'Delete Program', 
                                                message: 'Are you sure you want to delete {{ $course->name }}? Programs with alumni records cannot be deleted.', 
                                                action: '{{ route('admin.courses.destroy', $course->id) }}', 
                                                method: 'DELETE', 
                                                danger: true, 
                                                confirmText: 'Delete' 
                                            })"
                                class="text-red-500 hover:text-red-700 font-bold text-xs uppercase transition-colors">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="px-5 py-10 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm text-center text-gray-500 dark:text-dark-text-muted italic">
                        No courses found matching your criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pagination-container">
    {{ $courses->links() }}
</div>