<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $evaluation->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="mb-8 border-b pb-6">
                        <p class="text-gray-600">{{ $evaluation->description }}</p>
                    </div>

                    <form action="{{ route('alumni.evaluations.store', $evaluation->id) }}" method="POST">
                        @csrf
                        
                        <div class="space-y-8">
                            @foreach($evaluation->questions as $index => $question)
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                                    <label class="block text-lg font-bold text-gray-900 mb-3">
                                        <span class="text-brand-600 mr-2">{{ $index + 1 }}.</span> {{ $question->question_text }}
                                        @if($question->required) <span class="text-red-500">*</span> @endif
                                    </label>

                                    @if($question->type === 'text')
                                        <input type="text" name="q_{{ $question->id }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-500 focus:border-brand-500" {{ $question->required ? 'required' : '' }}>
                                    
                                    @elseif($question->type === 'textarea')
                                        <textarea name="q_{{ $question->id }}" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-500 focus:border-brand-500" {{ $question->required ? 'required' : '' }}></textarea>
                                    
                                    @elseif($question->type === 'radio')
                                        <div class="space-y-2">
                                            @php
                                                $radioOptions = $question->options;
                                                if (is_string($radioOptions)) {
                                                    $radioOptions = json_decode($radioOptions, true);
                                                }
                                                $radioOptions = is_array($radioOptions) ? $radioOptions : [];
                                            @endphp
                                            @foreach($radioOptions as $option)
                                                <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-brand-300 transition-colors">
                                                    <input type="radio" name="q_{{ $question->id }}" value="{{ $option }}" class="text-brand-600 focus:ring-brand-500" {{ $question->required ? 'required' : '' }}>
                                                    <span class="ml-3 text-gray-700">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif($question->type === 'checkbox')
                                        <div class="space-y-2">
                                            @php
                                                $cbOptions = $question->options;
                                                if (is_string($cbOptions)) {
                                                    $cbOptions = json_decode($cbOptions, true);
                                                }
                                                $cbOptions = is_array($cbOptions) ? $cbOptions : [];
                                            @endphp
                                            @foreach($cbOptions as $option)
                                                <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-brand-300 transition-colors">
                                                    <input type="checkbox" name="q_{{ $question->id }}[]" value="{{ $option }}" class="text-brand-600 focus:ring-brand-500">
                                                    <span class="ml-3 text-gray-700">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif($question->type === 'scale')
                                        <div class="flex justify-between items-center max-w-md mx-auto py-4">
                                            @php 
                                                $scaleOptions = $question->options;
                                                if (is_string($scaleOptions)) {
                                                    $scaleOptions = json_decode($scaleOptions, true);
                                                }
                                                $scaleOptions = is_array($scaleOptions) ? $scaleOptions : ['1' => 'Poor', '5' => 'Excellent'];

                                                $minLabel = $scaleOptions['1'] ?? 'Poor';
                                                $maxLabel = $scaleOptions['5'] ?? 'Excellent';
                                            @endphp
                                            <span class="text-xs font-bold text-gray-500 uppercase">{{ $minLabel }}</span>
                                            <div class="flex gap-4">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="q_{{ $question->id }}" value="{{ $i }}" class="sr-only peer" {{ $question->required ? 'required' : '' }}>
                                                        <div class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center text-gray-400 peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 peer-checked:scale-110 transition-all font-black group-hover:border-brand-300">
                                                            {{ $i }}
                                                        </div>
                                                    </label>
                                                @endfor
                                            </div>
                                            <span class="text-xs font-bold text-gray-500 uppercase">{{ $maxLabel }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-100 transition-all transform hover:-translate-y-1">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
