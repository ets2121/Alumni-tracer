<div class="overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Name / Email</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Role</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Department</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Status</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg-subtle text-left text-xs font-semibold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10">
                                <img class="w-full h-full rounded-full object-cover border-2 border-brand-50 dark:border-dark-border"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=F9FAFB&background=10B981' }}"
                                    alt="">
                            </div>
                            <div class="ml-3">
                                <p
                                    class="text-gray-900 dark:text-dark-text-primary whitespace-no-wrap font-bold capitalize">
                                    {{ $user->name }}
                                </p>
                                <p class="text-gray-500 dark:text-dark-text-muted whitespace-no-wrap text-xs">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        <span
                            class="px-3 py-1 text-[10px] font-bold rounded-full uppercase
                                    {{ $user->role === 'admin' ? 'bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' }}">
                            {{ $user->role === 'admin' ? 'System Admin' : 'Dept Admin' }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        @if($user->department_name)
                            <span
                                class="text-[11px] font-black text-gray-900 dark:text-dark-text-primary border border-gray-900 dark:border-dark-text-primary px-2 py-0.5 rounded italic">
                                {{ $user->department_name }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-dark-text-muted text-xs italic">System Wide</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm">
                        <span
                            class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 uppercase">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right flex items-center gap-3">
                        <button
                            @click="openModal('{{ route('admin.users.update', $user->id) }}', 'Edit User Account', {{ $user->id }})"
                            class="text-brand-600 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300 font-bold text-xs uppercase transition-colors">Edit</button>

                        @if($user->id !== auth()->id())
                            <button @click="$dispatch('open-confirmation-modal', { 
                                                        title: 'Delete User Account', 
                                                        message: 'Are you sure you want to delete {{ $user->name }}? This action cannot be undone.', 
                                                        action: '{{ route('admin.users.destroy', $user->id) }}', 
                                                        method: 'DELETE', 
                                                        danger: true, 
                                                        confirmText: 'Delete Account' 
                                                    })"
                                class="text-red-500 hover:text-red-700 font-bold text-xs uppercase transition-colors">Delete</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        class="px-5 py-10 border-b border-gray-200 dark:border-dark-border bg-white dark:bg-dark-bg text-sm text-center text-gray-500 dark:text-dark-text-muted italic">
                        No administrative users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pagination-container">
    {{ $users->links() }}
</div>