<x-app-layout>
    <div class="py-12" x-data="{ showPreview: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <div class="mb-6">
                        <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">Thank You!</h1>
                    <p class="text-lg text-gray-600 mb-8">
                        You have already completed the <strong>{{ $form->title }}</strong>.<br>
                        Your feedback is valuable to us.
                    </p>

                    <div class="flex justify-center gap-4">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Return to Dashboard
                        </a>

                        <button @click="showPreview = true"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            View My Response
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Modal --}}
        <div x-show="showPreview" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPreview" class="fixed inset-0 transition-opacity" aria-hidden="true"
                    @click="showPreview = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPreview"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Your Response</h3>
                            <button @click="showPreview = false" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            @foreach($form->questions->sortBy('order') as $question)
                                @php
                                    $ans = $existingResponse->answers->where('question_id', $question->id)->first();
                                    $val = $ans ? $ans->answer_text : 'N/A';
                                    $decoded = json_decode($val, true);
                                    $isJson = (json_last_error() === JSON_ERROR_NONE && is_array($decoded));
                                @endphp
                                <div class="bg-gray-50 p-3 rounded border border-gray-100">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">{{ $question->section }}</p>
                                    <p class="text-sm font-semibold text-gray-800 mb-1">
                                        {{ $question->options['question_number'] ?? '' }}. {{ $question->question_text }}
                                    </p>
                                    <div class="text-sm text-gray-600 ml-2">
                                        @if($isJson && $question->type === 'dynamic_table')
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-xs">
                                                    <thead>
                                                        <tr class="bg-gray-200">
                                                            @foreach(array_keys($decoded[0] ?? []) as $header)
                                                                <th class="p-1 text-left">{{ $header }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($decoded as $row)
                                                            <tr class="border-b">
                                                                @foreach($row as $cell)
                                                                    <td class="p-1">{{ $cell }}</td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif($isJson && array_is_list($decoded))
                                            {{ implode(', ', $decoded) }}
                                        @else
                                            {{ $val }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showPreview = false"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>