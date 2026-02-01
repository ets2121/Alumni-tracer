<x-layouts.admin>
    <x-slot name="header">
        Analytics: {{ $evaluation->title }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <span class="text-sm font-bold uppercase tracking-wide text-gray-500">Total Responses</span>
                <p class="text-3xl font-black text-gray-900">{{ $evaluation->responses->count() }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.evaluations.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-900 underline">Back to List</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($analytics as $index => $item)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col h-full">
                    <h4 class="font-bold text-gray-900 mb-2 border-b pb-2">
                        <span class="text-brand-600 mr-2">Q{{ $index + 1 }}.</span> {{ $item['question'] }}
                    </h4>

                    <div class="flex-1 min-h-[300px] flex items-center justify-center relative">
                        @if(in_array($item['type'], ['radio', 'checkbox', 'scale']))
                            @if(count($item['stats']) > 0 && array_sum($item['stats']) > 0)
                                <canvas id="chart-{{ $item['id'] }}" class="w-full h-full"></canvas>
                            @else
                                <p class="text-gray-400 italic">No data available.</p>
                            @endif
                        @else
                            <div class="w-full h-full overflow-y-auto max-h-[300px]">
                                @if(count($item['text_answers']) > 0)
                                    <ul class="space-y-2">
                                        @foreach($item['text_answers'] as $ans)
                                            <li class="bg-gray-50 p-2 rounded text-sm text-gray-700 border border-gray-100">
                                                "{{ $ans }}"
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-400 italic text-center mt-10">No text responses yet.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const analyticsData = @json($analytics);

                analyticsData.forEach(item => {
                    if (['radio', 'checkbox', 'scale'].includes(item.type)) {
                        const ctx = document.getElementById('chart-' + item.id);
                        if (!ctx) return;

                        const labels = Object.keys(item.stats);
                        const data = Object.values(item.stats);

                        // Determine chart type
                        let chartType = 'bar';
                        if (item.type === 'radio') chartType = 'pie';
                        if (item.type === 'scale') chartType = 'bar';

                        // Dynamic Colors
                        const colors = [
                            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                            '#EC4899', '#6366F1', '#14B8A6'
                        ];

                        new Chart(ctx, {
                            type: chartType,
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Responses',
                                    data: data,
                                    backgroundColor: chartType === 'pie' ? colors : '#4F46E5',
                                    borderColor: chartType === 'pie' ? '#ffffff' : '#4F46E5',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: chartType === 'pie',
                                        position: 'bottom'
                                    }
                                },
                                scales: chartType === 'bar' ? {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                } : {}
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layouts.admin>