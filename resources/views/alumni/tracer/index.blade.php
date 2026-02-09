@php
    $currentSection = null;
    $logicMap = [];
    $questionIdMap = [];
    foreach ($form->questions as $q) {
        $opts = $q->options;
        $qNum = $opts['question_number'] ?? null;
        if ($qNum) $questionIdMap[$qNum] = $q->id;
    }
    foreach ($form->questions as $q) {
        $opts = $q->options;
        if (isset($opts['conditional_logic'])) {
            foreach ($opts['conditional_logic'] as $logic) {
                if ($logic['action'] == 'show') {
                    foreach ($logic['target_questions'] as $targetNum) {
                        if (isset($questionIdMap[$targetNum])) {
                            $logicMap[$questionIdMap[$targetNum]] = [
                                'trigger_id' => $q->id,
                                'value' => $logic['trigger']
                            ];
                        }
                    }
                }
            }
        }
    }
@endphp

<x-app-layout>
    <div class="py-12" x-data="tracerForm({ logicMap: {{ json_encode($logicMap) }} })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8 text-center">
                        <h1 class="text-2xl font-bold mb-2">{{ $form->title }}</h1>
                        <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-700 text-justify italic">
                            {{ $form->description }}
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('tracer.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="form_id" value="{{ $form->id }}">


                        @if(count($form->questions) > 0)
                            @php
                                $currentSection = null;
                                // We need to group questions by section first to handle the grid properly? 
                                // Or we can just break the grid when section changes.
                                // Let's try breaking the grid.
                            @endphp

                            <div class="space-y-8"> <!-- Main Container for Sections -->
                                @foreach($form->questions as $index => $question)
                                    @php
                                        $section = $question->section;
                                        $type = $question->type;
                                        $options = $question->options ?? [];
                                        $qNum = $options['question_number'] ?? '';
                                        
                                        // Determine Column Span
                                        $colSpan = 'col-span-1 md:col-span-12'; // Default full width
                                        
                                        // Half-width fields
                                        if (in_array($type, ['text', 'email', 'number', 'select', 'date', 'date_group'])) {
                                             $colSpan = 'col-span-1 md:col-span-6';
                                        }
                                        
                                        // Specific overrides could go here if needed
                                    @endphp

                                    @if($section !== $currentSection)
                                        @if($index > 0)
                                            </div> <!-- Close previous grid -->
                                            </div> <!-- Close previous section container -->
                                        @endif
                                        
                                        @php $currentSection = $section; @endphp
                                        
                                        <div class="bg-gray-50/50 p-6 rounded-xl border border-gray-100">
                                            @if($section)
                                                <h2 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                                                    <span class="bg-green-600 w-2 h-6 rounded-full inline-block"></span>
                                                    {{ $section }}
                                                </h2>
                                            @endif
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6"> <!-- Start new grid -->
                                    @endif

                                    <div class="{{ $colSpan }} min-w-0"
                                        x-show="isVisible({{ $question->id }})"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        style="display: none;"> <!-- Hidden by default to prevent flicker, handled by x-show -->
                                        
                                        <div class="h-full">
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                @if($qNum)
                                                    <span class="text-green-600 mr-1">{{ $qNum }}.</span>
                                                @endif
                                                {{ $question->question_text }}
                                                @if($question->required)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>

                                            <!-- Question Types -->
                                            @if($type === 'text')
                                                @php
                                                    $isNumeric = Str::contains(strtolower($question->question_text), ['year', 'number', 'age', 'amount', 'salary', 'contact', 'zip']);
                                                    $inputType = $isNumeric ? 'number' : 'text';
                                                    $placeholder = $question->required ? 'Enter your answer...' : 'Optional';
                                                @endphp
                                                <input type="{{ $inputType }}" name="answers[{{ $question->id }}]" x-model="answers[{{ $question->id }}]"
                                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 transition-all font-sans"
                                                    placeholder="{{ $placeholder }}"
                                                    {{ $question->required ? 'required' : '' }}>

                                            @elseif($type === 'email')
                                                <input type="email" name="answers[{{ $question->id }}]" x-model="answers[{{ $question->id }}]"
                                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                                                    placeholder="example@email.com"
                                                    {{ $question->required ? 'required' : '' }}>

                                            @elseif($type === 'radio' || $type === 'radio_with_other')
                                                <div class="space-y-2 mt-1">
                                                    @if(isset($options['options']))
                                                        @foreach($options['options'] as $opt)
                                                            <label class="flex items-start hover:bg-gray-50 p-2 rounded-lg -ml-2 transition-colors cursor-pointer">
                                                                <input type="radio" value="{{ $opt }}" name="answers[{{ $question->id }}]"
                                                                    x-model="answers[{{ $question->id }}]" 
                                                                    class="mt-0.5 text-green-600 focus:ring-green-500 border-gray-300"
                                                                    {{ $question->required ? 'required' : '' }}>
                                                                <span class="ml-2 text-gray-700 text-sm leading-snug">{{ $opt }}</span>
                                                            </label>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            @elseif($type === 'checkbox')
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                                    @if(isset($options['options']))
                                                        @foreach($options['options'] as $opt)
                                                            <label class="flex items-start hover:bg-gray-50 p-2 rounded-lg -ml-2 transition-colors cursor-pointer">
                                                                <input type="checkbox" value="{{ $opt }}" name="answers[{{ $question->id }}][]"
                                                                    class="mt-0.5 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                                                <span class="ml-2 text-gray-700 text-sm leading-snug">{{ $opt }}</span>
                                                            </label>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            @elseif($type === 'date_group')
                                                <div class="grid grid-cols-3 gap-3">
                                                    <div class="col-span-1">
                                                        <select name="answers[{{ $question->id }}][month]" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                                                            <option value="">Month</option>
                                                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                                                <option value="{{ $month }}">{{ $month }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-span-1">
                                                        <select name="answers[{{ $question->id }}][day]" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                                                            <option value="">Day</option>
                                                            @for($i = 1; $i <= 31; $i++)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-span-1">
                                                        <select name="answers[{{ $question->id }}][year]" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                                                            <option value="">Year</option>
                                                            @for($i = date('Y'); $i >= 1960; $i--)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                            @elseif($type === 'dynamic_table')
                                                 <div x-data="{ rows: Array.from({ length: {{ $options['min_rows'] ?? 1 }} }, () => ({})) }" class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                @foreach($options['table_columns'] as $col)
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <template x-for="(row, index) in rows" :key="index">
                                                                <tr>
                                                                    @foreach($options['table_columns'] as $colIndex => $col)
                                                                        <td class="px-2 py-2">
                                                                            @php
                                                                                $isDate = Str::contains(strtolower($col), ['date', 'year']);
                                                                                $inputType = $isDate ? 'date' : 'text';
                                                                                $isRequired = $question->required && !Str::contains(strtolower($col), ['optional', 'honor', 'award']);
                                                                            @endphp
                                                                            <input type="{{ $inputType }}"
                                                                                :name="'answers[{{ $question->id }}]['+index+'][{{ $col }}]'"
                                                                                class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-green-500 focus:border-green-500"
                                                                                {{ $isRequired ? 'required' : '' }}>
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" @click="rows.push({})"
                                                        class="mt-2 text-xs flex items-center gap-1 text-green-700 hover:text-green-900 font-bold uppercase tracking-wider">
                                                        <span>+ Add Row</span>
                                                    </button>
                                                </div>

                                            @elseif($type === 'checkbox_matrix')
                                                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Option</th>
                                                                @foreach($options['matrix_categories'] as $cat)
                                                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $cat }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach($options['matrix_options'] as $mOpt)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $mOpt }}</td>
                                                                    @foreach($options['matrix_categories'] as $cat)
                                                                        <td class="px-2 py-2 text-center">
                                                                            <input type="checkbox" name="answers[{{ $question->id }}][{{ $cat }}][]"
                                                                                value="{{ $mOpt }}" class="text-green-600 focus:ring-green-500 rounded border-gray-300">
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Close last grid and section container -->
                                @if(count($form->questions) > 0)
                                    </div> <!-- Close last grid -->
                                    </div> <!-- Close last section container -->
                                @endif
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                No questions found for this tracer survey.
                            </div>
                        @endif

                        <div class="mt-8 flex justify-end">
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform transition hover:scale-105">
                                Submit Survey
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>