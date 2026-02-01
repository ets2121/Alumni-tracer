<div class="space-y-8">
    <div class="flex items-center justify-between border-b pb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-widest">Graduate Tracer Study Report</h2>
            <p class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-tighter">CHED-Compliant Analytical Data
            </p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black text-gray-300 uppercase leading-none mb-1">Institutional Code</p>
            <p class="text-xl font-black text-gray-900 leading-none">ALUMNI-2026</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-200">
            <thead class="bg-gray-100/50">
                <tr>
                    <th rowspan="2"
                        class="border border-gray-200 px-4 py-4 text-[10px] font-black uppercase text-gray-600">Alumni
                        Name</th>
                    <th rowspan="2"
                        class="border border-gray-200 px-4 py-4 text-[10px] font-black uppercase text-gray-600">Course &
                        Year</th>
                    <th colspan="3"
                        class="border border-gray-200 px-4 py-2 text-[10px] font-black uppercase text-gray-600 text-center">
                        Employment Data</th>
                    <th rowspan="2"
                        class="border border-gray-200 px-4 py-4 text-[10px] font-black uppercase text-gray-600">Company
                        & Address</th>
                </tr>
                <tr>
                    <th
                        class="border border-gray-200 px-3 py-2 text-[9px] font-black uppercase text-gray-500 text-center">
                        Status</th>
                    <th
                        class="border border-gray-200 px-3 py-2 text-[9px] font-black uppercase text-gray-500 text-center">
                        Position</th>
                    <th
                        class="border border-gray-200 px-3 py-2 text-[9px] font-black uppercase text-gray-500 text-center">
                        Salary (P)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $profile)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="border border-gray-200 px-4 py-3 text-xs font-bold text-gray-900">
                            {{ $profile->last_name }}, {{ $profile->first_name }}</td>
                        <td class="border border-gray-200 px-4 py-3 text-xs text-center">
                            <span class="font-black">{{ $profile->course->code }}</span><br>
                            <span class="text-[10px] text-gray-400">{{ $profile->batch_year }}</span>
                        </td>
                        <td class="border border-gray-200 px-3 py-3 text-[10px] font-black text-center">
                            <span
                                class="{{ $profile->employment_status === 'Employed' ? 'text-green-600' : 'text-amber-600' }}">{{ $profile->employment_status }}</span>
                        </td>
                        <td class="border border-gray-200 px-3 py-3 text-[10px] font-bold text-center text-gray-700">
                            {{ $profile->position ?? 'N/A' }}</td>
                        <td class="border border-gray-200 px-3 py-3 text-[10px] font-bold text-center text-gray-400 italic">
                            Information Protected</td>
                        <td class="border border-gray-200 px-4 py-3">
                            <div class="text-[10px] font-black text-gray-900 uppercase leading-tight">
                                {{ $profile->company_name ?? 'NOT REGISTERED' }}</div>
                            <div class="text-[9px] text-gray-400 italic leading-tight mt-1">
                                {{ $profile->work_address ?? 'N/A' }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-20 pt-10 border-t flex justify-between items-start opacity-50 grayscale">
        <div class="text-center">
            <div class="w-48 border-b-2 border-gray-900 mb-2"></div>
            <p class="text-[10px] font-black uppercase tracking-widest">Alumni Office Coordinator</p>
        </div>
        <div class="text-center">
            <div class="w-48 border-b-2 border-gray-900 mb-2"></div>
            <p class="text-[10px] font-black uppercase tracking-widest">VP for Academic Affairs</p>
        </div>
    </div>
</div>