<x-layouts.admin>
    <x-slot name="header">
        Evaluation Results: {{ $evaluation->title }}
    </x-slot>

    <div class="space-y-6">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total Respondents</p>
                <p class="text-4xl font-black text-brand-600 mt-2">{{ $evaluation->responses->count() }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Status</p>
                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $evaluation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $evaluation->is_active ? 'Active' : 'Closed' }}
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Type</p>
                <p class="text-xl font-bold text-gray-900 mt-2 uppercase">{{ $evaluation->type }}</p>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Question Analysis</h3>
                <button class="text-sm text-brand-600 font-bold hover:underline" onclick="window.print()">Print Report</button>
            </div>
            
            <div class="divide-y divide-gray-100">
                @foreach($analytics as $index => $item)
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h4 class="text-md font-bold text-gray-800"><span class="text-gray-400 mr-2">{{ $index + 1 }}.</span> {{ $item['question'] }}</h4>
                            <span class="text-xs font-bold bg-gray-100 text-gray-500 px-2 py-1 rounded-md uppercase tracking-wide">{{ $item['type'] }}</span>
                        </div>

                        @if(in_array($item['type'], ['radio', 'checkbox', 'scale']))
                            <div class="space-y-3 mt-4">
                                @php 
                                    $max = 1;
                                    if(isset($item['stats']) && count($item['stats']) > 0) {
                                        $maxVal = max($item['stats']);
                                        $max = $maxVal > 0 ? $maxVal : 1;
                                    }
                                @endphp

                                @if(isset($item['stats']) && count($item['stats']) > 0)
                                    @foreach($item['stats'] as $label => $count)
                                        <div class="flex items-center gap-4 text-sm">
                                            <div class="w-1/3 md:w-1/4 text-gray-600 font-medium truncate" title="{{ $label }}">{{ $label }}</div>
                                            <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                                                <div class="bg-brand-500 h-full rounded-full" style="width: {{ ($count / $item['total_responses']) * 100 }}%"></div>
                                            </div>
                                            <div class="w-16 text-right text-gray-900 font-bold">
                                                {{ $count }} <span class="text-gray-400 text-xs font-normal">({{ round(($count / ($item['total_responses'] ?: 1)) * 100) }}%)</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-400 italic">No data available yet.</p>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 bg-gray-50 rounded-xl p-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Recent Answers</p>
                                <div class="space-y-3">
                                    @forelse($item['recent_answers'] as $answer)
                                        <div class="text-sm text-gray-700 border-l-2 border-brand-200 pl-3 italic">"{{ $answer }}"</div>
                                    @empty
                                        <p class="text-sm text-gray-400 italic">No text responses yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
