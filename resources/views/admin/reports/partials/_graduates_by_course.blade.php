<div class="space-y-8">
    <div class="border-b pb-6">
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest">Graduates by Course and Year</h2>
        <p class="text-xs text-gray-500 font-bold mt-1">Total Records Found: {{ $data->count() }}</p>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b-2 border-gray-200">
                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Course
                    Code</th>
                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Batch Year
                </th>
                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Alumni
                    Name</th>
                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Contact
                </th>
                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Employment
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @php $currentCourse = null; @endphp
            @foreach($data as $profile)
                @if($currentCourse !== $profile->course_id)
                    <tr class="bg-brand-50/30">
                        <td colspan="5" class="px-4 py-2 text-xs font-black text-brand-700 uppercase tracking-tighter">
                            {{ $profile->course->name }} ({{ $profile->course->code }})
                        </td>
                    </tr>
                    @php $currentCourse = $profile->course_id; @endphp
                @endif
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-4 text-[11px] font-bold text-gray-500 uppercase">{{ $profile->course->code }}</td>
                    <td class="px-4 py-4 text-[11px] font-black text-gray-900">{{ $profile->batch_year }}</td>
                    <td class="px-4 py-4 text-sm font-bold text-gray-800">{{ $profile->first_name }}
                        {{ $profile->last_name }}</td>
                    <td class="px-4 py-4 text-[11px] text-gray-500">
                        {{ $profile->user->email }}<br>{{ $profile->contact_number }}</td>
                    <td class="px-4 py-4">
                        <span
                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter
                                {{ $profile->employment_status === 'Employed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $profile->employment_status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>