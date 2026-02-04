<div class="overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Name / Email</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Role</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Department</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Status</th>
                <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10">
                                <img class="w-full h-full rounded-full object-cover border-2 border-brand-50"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=F9FAFB&background=10B981' }}"
                                    alt="">
                            </div>
                            <div class="ml-3">
                                <p class="text-gray-900 whitespace-no-wrap font-bold capitalize">
                                    {{ $user->name }}
                                </p>
                                <p class="text-gray-500 whitespace-no-wrap text-xs">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span
                            class="px-3 py-1 text-[10px] font-bold rounded-full uppercase
                                {{ $user->role === 'admin' ? 'bg-brand-100 text-brand-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $user->role === 'admin' ? 'System Admin' : 'Dept Admin' }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($user->department_name)
                            <span
                                class="text-[11px] font-black text-gray-900 border border-gray-900 px-2 py-0.5 rounded italic">
                                {{ $user->department_name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs italic">System Wide</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right flex items-center gap-3">
                        <button
                            @click="openModal('{{ route('admin.users.update', $user->id) }}', 'Edit User Account', {{ $user->id }})"
                            class="text-brand-600 hover:text-brand-900 font-bold text-xs uppercase transition-colors">Edit</button>

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
                        class="px-5 py-10 border-b border-gray-200 bg-white text-sm text-center text-gray-500 italic">
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