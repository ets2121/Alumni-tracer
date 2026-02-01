<div id="pre-registration-container" class="space-y-6">
    <!-- TABS NAVIGATION -->
    <div class="flex border-b border-gray-200 bg-white px-6 pt-2 rounded-t-xl overflow-x-auto">
        <button @click="setTab('pending')"
            class="px-6 py-4 border-b-2 text-sm transition-all flex items-center gap-2 whitespace-nowrap"
            :class="tab === 'pending' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'">
            Pending
            <span class="py-0.5 px-2 rounded-full text-[10px]"
                :class="tab === 'pending' ? 'bg-brand-100 text-brand-700' : 'bg-gray-100 text-gray-500'">{{ $pendingAlumni->total() }}</span>
        </button>
        <button @click="setTab('approved')"
            class="px-6 py-4 border-b-2 text-sm transition-all flex items-center gap-2 whitespace-nowrap"
            :class="tab === 'approved' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'">
            Approved History
            <span class="py-0.5 px-2 rounded-full text-[10px]"
                :class="tab === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">{{ $approvedAlumni->total() }}</span>
        </button>
        <button @click="setTab('rejected')"
            class="px-6 py-4 border-b-2 text-sm transition-all flex items-center gap-2 whitespace-nowrap"
            :class="tab === 'rejected' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'">
            Rejected History
            <span class="py-0.5 px-2 rounded-full text-[10px]"
                :class="tab === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500'">{{ $rejectedAlumni->total() }}</span>
        </button>
    </div>

    <!-- TAB CONTENT -->
    <div class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 p-6">

        <!-- PENDING TAB -->
        <div x-show="tab === 'pending'">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Course & Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registration Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($pendingAlumni as $user)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10 me-3 text-brand-300">
                                            <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" /></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($user->alumniProfile)
                                        <div class="font-medium text-gray-700">{{ $user->alumniProfile->course->code ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">Class of {{ $user->alumniProfile->batch_year }}</div>
                                    @else
                                        <span class="italic text-gray-400">Profile incomplete</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-full bg-amber-100 text-amber-700 uppercase tracking-wider border border-amber-200 shadow-sm">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openModal('{{ route('admin.alumni.edit', $user->id) }}', 'Review Application Details')"
                                        class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-brand-100">Review</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic bg-gray-50/50 rounded-xl">No pending registrations.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pagination-container">
                {{ $pendingAlumni->links() }}
            </div>
        </div>

        <!-- APPROVED TAB -->
        <div x-show="tab === 'approved'">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved Date</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($approvedAlumni as $user)
                            <tr class="hover:bg-green-50/30 transition-colors border-l-4 border-l-green-500">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-bold uppercase">
                                    {{ $user->alumniProfile->course->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->updated_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button @click="openModal('{{ route('admin.alumni.edit', $user->id) }}', 'View Activation Details')"
                                        class="text-green-600 hover:text-green-900 font-black text-xs uppercase tracking-tighter">Details</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic bg-gray-50/50 rounded-xl">No history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pagination-container">
                {{ $approvedAlumni->links() }}
            </div>
        </div>

        <!-- REJECTED TAB -->
        <div x-show="tab === 'rejected'">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejection Remarks</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($rejectedAlumni as $user)
                            <tr class="hover:bg-red-50/30 transition-colors border-l-4 border-l-red-500">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-brand-600 font-bold tracking-tighter">{{ $user->alumniProfile->course->code ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm max-w-xs">
                                    <div class="text-xs text-gray-500 font-medium italic line-clamp-2">{{ $user->admin_remarks ?? 'Reason not specified.' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->updated_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openModal('{{ route('admin.alumni.edit', $user->id) }}', 'Re-Review Application')"
                                        class="text-red-600 hover:text-red-900 font-black text-xs uppercase tracking-tighter">Review</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic bg-gray-50/50 rounded-xl">No rejected records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pagination-container">
                {{ $rejectedAlumni->links() }}
            </div>
        </div>
    </div>
</div>