<x-layouts.admin>
    <x-slot name="header">
        Edit Evaluation Form: {{ $evaluation->title }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"
        x-data="editEvaluationManager({{ $evaluation->questions }}, {{ $evaluation->is_active ? 'true' : 'false' }})">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Form Configuration</h3>
                <span class="text-xs text-gray-500">Version: {{ $evaluation->version }}</span>
            </div>

            <form action="{{ route('admin.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Form Title</label>
                        <input type="text" name="title" required value="{{ old('title', $evaluation->title) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Form Type</label>
                        <select name="type" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                            <option value="tracer" {{ $evaluation->type == 'tracer' ? 'selected' : '' }}>Graduate Tracer
                                Study</option>
                            <option value="usability" {{ $evaluation->type == 'usability' ? 'selected' : '' }}>System
                                Usability Survey</option>
                            <option value="event" {{ $evaluation->type == 'event' ? 'selected' : '' }}>Event Feedback
                            </option>
                            <option value="general" {{ $evaluation->type == 'general' ? 'selected' : '' }}>General Survey
                            </option>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">{{ old('description', $evaluation->description) }}</textarea>
                    </div>
                </div>

                <!-- Questions Builder -->
                <div class="space-y-4 border-t pt-4">
                    <div class="flex justify-between items-center px-2">
                        <h3 class="text-lg font-bold text-gray-900">Questions</h3>
                        <button type="button" @click="addQuestion()"
                            class="text-sm bg-brand-50 text-brand-700 px-4 py-2 rounded-lg font-bold hover:bg-brand-100 transition-colors">
                            + Add Question
                        </button>
                    </div>

                    <template x-for="(question, index) in questions" :key="question.id">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 relative group">
                            <!-- Remove Button -->
                            <button type="button" @click="questions.splice(index, 1)"
                                class="absolute top-3 right-3 text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="flex gap-4 items-start">
                                <span
                                    class="bg-white border border-gray-200 text-gray-500 font-bold w-6 h-6 flex items-center justify-center rounded-full text-xs flex-shrink-0 mt-2"
                                    x-text="index + 1"></span>

                                <div class="flex-1 space-y-3">
                                    <div>
                                        <input type="text" :name="'questions['+index+'][text]'"
                                            x-model="question.question_text" placeholder="Question text..." required
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 font-medium">
                                    </div>

                                    <div class="flex flex-wrap gap-4">
                                        <div class="w-full sm:w-1/3">
                                            <select :name="'questions['+index+'][type]'" x-model="question.type"
                                                class="w-full text-sm border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500">
                                                <option value="text">Short Text</option>
                                                <option value="textarea">Long Text</option>
                                                <option value="radio">Multiple Choice</option>
                                                <option value="checkbox">Checkboxes</option>
                                                <option value="scale">Rating Scale (1-5)</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center">
                                            <label class="inline-flex items-center">
                                                <input type="hidden" :name="'questions['+index+'][required]'" value="0">
                                                <input type="checkbox" :name="'questions['+index+'][required]'"
                                                    value="1" x-model="question.required"
                                                    class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                                                <span class="ml-2 text-sm text-gray-600">Required</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Options -->
                                    <div x-show="['radio', 'checkbox'].includes(question.type)"
                                        class="pl-4 border-l-2 border-brand-200 ml-1 space-y-2 mt-2">
                                        <template x-for="(option, optIndex) in question.parsedOptions" :key="optIndex">
                                            <div class="flex gap-2">
                                                <input type="text" :name="'questions['+index+'][options][]'"
                                                    x-model="question.parsedOptions[optIndex]" placeholder="Option..."
                                                    class="flex-1 text-xs border-gray-200 rounded focus:border-brand-500 focus:ring-brand-500 bg-white">
                                                <button type="button"
                                                    @click="question.parsedOptions.splice(optIndex, 1)"
                                                    class="text-gray-400 hover:text-red-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button" @click="question.parsedOptions.push('')"
                                            class="text-xs text-brand-600 font-bold hover:underline">+ Add
                                            Option</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-8 flex justify-between items-center border-t pt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" x-model="isActive"
                            class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                        <span class="ml-2 text-sm text-gray-600 font-bold">Set as Active (Publish)</span>
                    </label>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.evaluations.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50 transition-colors">Cancel</a>
                        <button type="submit"
                            class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg shadow-sm transition-colors transform hover:-translate-y-0.5">
                            Update Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function editEvaluationManager(existingQuestions, isActiveStatus) {
                return {
                    questions: existingQuestions.map(q => ({
                        ...q,
                        // Ensure options is parsed from JSON string if it comes as string, or use as is
                        parsedOptions: typeof q.options === 'string' ? JSON.parse(q.options) : (q.options || ['']),
                        required: !!q.required // ensure boolean
                    })),
                    isActive: isActiveStatus,

                    addQuestion() {
                        this.questions.push({
                            id: Date.now() + Math.random(),
                            question_text: '',
                            type: 'text',
                            required: true,
                            parsedOptions: ['']
                        });
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>