<div class="space-y-8">
    <div class="border-b pb-6">
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Alumni Employment Distribution</h2>
        <p class="text-xs text-gray-500 font-bold mt-1">Classification based on current professional records.</p>
    </div>

    @foreach($data as $status => $profiles)
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="h-6 w-1 {{ $status === 'Employed' ? 'bg-green-500' : ($status === 'Unemployed' ? 'bg-red-500' : 'bg-blue-500') }}">
                </div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">{{ $status }} Registry
                    ({{ $profiles->count() }})</h3>
            </div>

            <table class="w-full border-collapse bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Name
                        </th>
                        <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Course</th>
                        <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Designation/Company</th>
                        <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Work
                            Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($profiles as $profile)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $profile->first_name }}
                                {{ $profile->last_name }}</td>
                            <td class="px-5 py-4 text-[11px] font-black text-brand-600 uppercase">{{ $profile->course->code }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm font-bold text-gray-800">{{ $profile->position ?? 'N/A' }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $profile->company_name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500 italic">{{ $profile->work_address ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</div>