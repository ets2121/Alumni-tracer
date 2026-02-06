<x-app-layout>
    <div class="py-12" x-data="gtsForm()">
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

                        @php
                            $currentSection = null;
                            // Build logic map for JS
                            // map[target_q_num] = { trigger_q_id: 123, trigger_value: 'Yes' }
                            $logicMap = [];
                            $questionIdMap = []; // num -> id

                            foreach ($form->questions as $q) {
                                $opts = $q->options; // casted to array in model
                                $qNum = $opts['question_number'] ?? null;
                                if ($qNum)
                                    $questionIdMap[$qNum] = $q->id;
                            }

                            foreach ($form->questions as $q) {
                                $opts = $q->options;
                                if (isset($opts['conditional_logic'])) {
                                    foreach ($opts['conditional_logic'] as $logic) {
                                        if ($logic['action'] == 'show') {
                                            foreach ($logic['target_questions'] as $targetNum) {
                                                if (isset($questionIdMap[$targetNum])) {
                                                    // Array of triggers if multiple? simplified for now
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

                        @foreach($form->questions as $question)
                            @php
                                $options = $question->options ?? []; // Array
                                $section = $question->section;
                                $type = $question->type;
                                $qNum = $options['question_number'] ?? '';
                            @endphp

                            @if($section && $section !== $currentSection)
                                @php $currentSection = $section; @endphp
                                <div class="mt-8 mb-4">
                                    <h2 class="text-xl font-bold text-gray-800 border-b-2 border-green-500 pb-2">{{ $section }}</h2>
                                </div>
                            @endif

                            <fieldset class="mb-6 p-4 rounded-lg hover:bg-gray-50 transition min-w-0"
                                x-show="isVisible({{ $question->id }})"
                                :disabled="!isVisible({{ $question->id }})"
                                x-transition>
                                <label class="block text-gray-700 text-base font-semibold mb-2">
                                    @if($qNum)
                                        <span class="mr-1">{{ $qNum }}.</span>
                                    @endif
                                    {{ $question->question_text }}
                                    @if($question->required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                <!-- Question Type: Text -->
                                @if($type === 'text')
                                    @php
                                        $isNumeric = Str::contains(strtolower($question->question_text), ['year', 'number', 'age', 'amount', 'salary', 'contact']);
                                        $inputType = $isNumeric ? 'number' : 'text';
                                        $placeholder = $question->required ? 'Enter your answer...' : 'N/A (Optional)';
                                    @endphp
                                    <input type="{{ $inputType }}" name="answers[{{ $question->id }}]" x-model="answers[{{ $question->id }}]"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                                        placeholder="{{ $placeholder }}"
                                        {{ $question->required ? 'required' : '' }}>

                                    <!-- Question Type: Email -->
                                @elseif($type === 'email')
                                    <input type="email" name="answers[{{ $question->id }}]" x-model="answers[{{ $question->id }}]"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                                        {{ $question->required ? 'required' : '' }}>

                                    <!-- Question Type: Radio -->
                                @elseif($type === 'radio' || $type === 'radio_with_other')
                                    <div class="space-y-2">
                                        @if(isset($options['options']))
                                            @foreach($options['options'] as $opt)
                                                <label class="inline-flex items-center mr-4">
                                                    <input type="radio" value="{{ $opt }}" name="answers[{{ $question->id }}]"
                                                        x-model="answers[{{ $question->id }}]" class="text-green-600 focus:ring-green-500"
                                                        {{ $question->required ? 'required' : '' }}>
                                                    <span class="ml-2">{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>

                                    <!-- Question Type: Checkbox -->
                                @elseif($type === 'checkbox')
                                    <div class="space-y-2">
                                        @if(isset($options['options']))
                                            @foreach($options['options'] as $opt)
                                                <label class="flex items-center">
                                                    <input type="checkbox" value="{{ $opt }}" name="answers[{{ $question->id }}][]"
                                                        class="rounded text-green-600 focus:ring-green-500">
                                                    <span class="ml-2">{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>

                                    <!-- Question Type: Date Group (Month, Day, Year) -->
                                @elseif($type === 'date_group')
                                    <div class="flex space-x-2">
                                        <div class="w-1/3">
                                            <select name="answers[{{ $question->id }}][month]"
                                                class="w-full border-gray-300 rounded-lg shadow-sm" required>
                                                <option value="">Month</option>
                                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                    <option value="{{ $month }}">{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-1/3">
                                            <select name="answers[{{ $question->id }}][day]"
                                                class="w-full border-gray-300 rounded-lg shadow-sm" required>
                                                <option value="">Day</option>
                                                @for($i = 1; $i <= 31; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="w-1/3">
                                            <select name="answers[{{ $question->id }}][year]"
                                                class="w-full border-gray-300 rounded-lg shadow-sm" required>
                                                <option value="">Year</option>
                                                @for($i = date('Y') - 15; $i >= 1960; $i--)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Question Type: Dynamic Table -->
                                @elseif($type === 'dynamic_table')
                                    <div x-data="{ rows: Array.from({ length: {{ $options['min_rows'] ?? 1 }} }, () => ({})) }">
                                        <table class="w-full border-collapse border border-gray-200 mb-2">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    @foreach($options['table_columns'] as $col)
                                                        <th class="border border-gray-200 p-2 text-left text-sm">{{ $col }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(row, index) in rows" :key="index">
                                                    <tr>
                                                        @foreach($options['table_columns'] as $colIndex => $col)
                                                            <td class="border border-gray-200 p-1">
                                                                @php
                                                                    $isDate = Str::contains(strtolower($col), ['date', 'year graduated']);
                                                                    $inputType = $isDate ? 'date' : 'text';
                                                                    
                                                                    // Specific optional columns even if question is required
                                                                    $optionalCols = ['honors/awards', 'rating', 'duration', 'institution'];
                                                                    $isOptionalCol = Str::contains(strtolower($col), $optionalCols);
                                                                    
                                                                    // If question is optional, inputs shouldn't enforce required unless we want to enforce row completeness?
                                                                    // Usually if they fill a row, they should fill key fields. 
                                                                    // But if the question ITSELF is optional, the browser validation might block submission if defaults are empty?
                                                                    // No, because empty inputs are just "" and if required is present, browser blocks.
                                                                    // If question is optional, we probably shouldn't require ANY field technically, OR only if row is dirty (too complex for simple HTML).
                                                                    // Safest: If question is NOT required, remove required attribute.
                                                                    // Exception: "Honors/Awards" is always optional.
                                                                    
                                                                    $isRequired = $question->required && !$isOptionalCol;
                                                                @endphp
                                                                <input type="{{ $inputType }}"
                                                                    :name="'answers[{{ $question->id }}]['+index+'][{{ $col }}]'"
                                                                    class="w-full border-transparent focus:border-green-500 text-sm"
                                                                    {{ $isRequired ? 'required' : '' }}>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                        <button type="button" @click="rows.push({})"
                                            class="text-xs text-green-600 hover:text-green-800 font-semibold">+ Add Row</button>
                                    </div>

                                    <!-- Question Type: Checkbox Matrix -->
                                @elseif($type === 'checkbox_matrix')
                                    <div class="overflow-x-auto">
                                        <table class="w-full border-collapse border border-gray-200">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th class="border border-gray-200 p-2 text-left w-1/2">Reason</th>
                                                    @foreach($options['matrix_categories'] as $cat)
                                                        <th class="border border-gray-200 p-2 text-center">{{ $cat }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($options['matrix_options'] as $mOpt)
                                                    <tr>
                                                        <td class="border border-gray-200 p-2 text-sm">{{ $mOpt }}</td>
                                                        @foreach($options['matrix_categories'] as $cat)
                                                            <td class="border border-gray-200 p-2 text-center">
                                                                <input type="checkbox" name="answers[{{ $question->id }}][{{ $cat }}][]"
                                                                    value="{{ $mOpt }}" class="text-green-600 focus:ring-green-500">
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </fieldset>
                        @endforeach

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

        <script>
            function gtsForm() {
                return {
                    answers: {},
                    logicMap: @json($logicMap),
                    isVisible(questionId) {
                        if (this.logicMap[questionId]) {
                            const rule = this.logicMap[questionId];
                            return this.answers[rule.trigger_id] === rule.value;
                        }
                        return true;
                    }
                }
            }

            // Alpine data needs to be available globally if referenced by name
            // But here I'll use inline x-data for the main form.
            // Wait, `x-data="gtsForm()"` expects `gtsForm` to be defined.
        </script>
    </div>
</x-app-layout>