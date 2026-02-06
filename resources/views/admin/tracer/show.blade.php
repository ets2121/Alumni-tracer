<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tracer Survey Response') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('admin.tracer.index') }}" class="text-green-600 hover:text-green-800 font-semibold">&larr; Back to List</a>
                        <div class="text-sm text-gray-500">
                            Submitted: {{ $response->created_at->format('F d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Alumni Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Name</span>
                                <span class="block text-lg">{{ $response->user->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Email</span>
                                <span class="block text-lg">{{ $response->user->email }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Department (at submission)</span>
                                <span class="block text-lg">{{ $response->department_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        @php
                            $answersMap = [];
                            foreach($response->answers as $ans) {
                                $answersMap[$ans->question_id] = $ans->answer_text;
                            }
                            $currentSection = null;
                        @endphp

                        @foreach($response->form->questions as $question)
                            @php
                                $options = $question->options ?? []; 
                                $section = $question->section;
                                $qNum = $options['question_number'] ?? '';
                                $answerResult = $answersMap[$question->id] ?? null;
                            @endphp

                            @if($section && $section !== $currentSection)
                                @php $currentSection = $section; @endphp
                                <div class="mt-8 mb-4">
                                    <h2 class="text-xl font-bold text-gray-800 border-b-2 border-green-500 pb-2">{{ $section }}</h2>
                                </div>
                            @endif

                            <div class="mb-6 border-b border-gray-100 pb-4">
                                <label class="block text-gray-700 text-base font-semibold mb-2">
                                    @if($qNum)
                                        <span class="mr-1 text-green-600">{{ $qNum }}.</span>
                                    @endif
                                    {{ $question->question_text }}
                                </label>

                                <div class="ml-6 text-gray-900">
                                    @if(!$answerResult)
                                        <span class="text-gray-400 italic">No answer provided</span>
                                    @else
                                        @if($question->type === 'dynamic_table')
                                            @php
                                                $tableData = json_decode($answerResult, true);
                                            @endphp
                                            @if($tableData && is_array($tableData))
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-gray-200 border">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                @foreach($options['table_columns'] as $col)
                                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach($tableData as $row)
                                                                <tr>
                                                                    @foreach($options['table_columns'] as $col)
                                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row[$col] ?? '-' }}</td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                 <span class="text-gray-400 italic">Invalid Data</span>
                                            @endif

                                        @elseif($question->type === 'checkbox_matrix')
                                            @php
                                                // Matrix storage: {"Undergrad": ["Reason 1", "Reason 2"], "Grad": []}
                                                // Or flattened?
                                                // Based on controller, it's json encoded.
                                                // The input name was answers[id][cat][]
                                                // So it should be saved as JSON object
                                                $matrixData = json_decode($answerResult, true);
                                            @endphp
                                            @if($matrixData && is_array($matrixData))
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @foreach($matrixData as $cat => $reasons)
                                                        <div class="bg-gray-50 p-3 rounded">
                                                            <strong class="block text-sm mb-2 underline">{{ $cat }}</strong>
                                                            <ul class="list-disc list-inside text-sm">
                                                                @foreach($reasons as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $answerResult }}
                                            @endif

                                        @elseif($question->type === 'checkbox')
                                            @php
                                                $cbData = json_decode($answerResult, true);
                                            @endphp
                                            @if(is_array($cbData))
                                                <ul class="list-disc list-inside">
                                                    @foreach($cbData as $item)
                                                        <li>{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                {{ $answerResult }}
                                            @endif

                                        @elseif($question->type === 'date_group')
                                            @php
                                                $dateData = json_decode($answerResult, true);
                                            @endphp
                                            @if(is_array($dateData))
                                                {{ $dateData['month'] ?? '' }} {{ $dateData['day'] ?? '' }}, {{ $dateData['year'] ?? '' }}
                                            @else
                                                {{ $answerResult }}
                                            @endif
                                            
                                        @else
                                            {{ $answerResult }}
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
