<x-layouts.admin>
    <x-slot name="header">
        System Evaluations
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="evaluationManager()">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold">Evaluation Forms</h3>
                    <p class="text-xs text-gray-500">Manage tracer studies, surveys, and feedback forms.</p>
                </div>
                <!-- Changed to button with Alpine click handler -->
                <button @click="openModal()"
                    class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Form
                </button>
            </div>

            <!-- Flash Message (standard Laravel flash) -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Title</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Responses</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($forms as $form)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 font-bold whitespace-no-wrap">{{ $form->title }}</p>
                                    <p class="text-gray-400 text-xs">{{ Str::limit($form->description, 50) }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                                        <span aria-hidden
                                            class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                                        <span
                                            class="relative text-xs uppercase tracking-wide">{{ ucfirst($form->type) }}</span>
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                    <span class="font-bold text-gray-700">{{ $form->responses_count }}</span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $form->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $form->is_active ? 'Active' : 'Closed' }}
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.evaluations.show', $form->id) }}"
                                            class="text-brand-600 hover:text-brand-900 font-medium text-xs uppercase tracking-wide">Results</a>
                                        <form action="{{ route('admin.evaluations.destroy', $form->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure? This will delete all responses.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 font-medium text-xs uppercase tracking-wide">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500 italic">No evaluation forms
                                    created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal()">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                    x-show="modalOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-6 border-b pb-4">
                            <h3 class="text-xl font-bold text-gray-900">Create New Evaluation Form</h3>
                            <button @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form Content -->
                        <form action="{{ route('admin.evaluations.store') }}" method="POST">
                            @csrf

                            <!-- Basic Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Form Title</label>
                                    <input type="text" name="title" required
                                        placeholder="e.g. Graduate Tracer Study 2026"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Form Type</label>
                                    <select name="type" required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                                        <option value="tracer">Graduate Tracer Study</option>
                                        <option value="usability">System Usability Survey</option>
                                        <option value="event">Event Feedback</option>
                                        <option value="general">General Survey</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="2"
                                        placeholder="Brief description of the survey..."
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500"></textarea>
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
                                                        x-model="question.text" placeholder="Question text..." required
                                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 font-medium">
                                                </div>

                                                <div class="flex flex-wrap gap-4">
                                                    <div class="w-full sm:w-1/3">
                                                        <select :name="'questions['+index+'][type]'"
                                                            x-model="question.type"
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
                                                            <input type="hidden"
                                                                :name="'questions['+index+'][required]'" value="0">
                                                            <input type="checkbox"
                                                                :name="'questions['+index+'][required]'" value="1"
                                                                x-model="question.required"
                                                                class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                                                            <span class="ml-2 text-sm text-gray-600">Required</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Options -->
                                                <div x-show="['radio', 'checkbox'].includes(question.type)"
                                                    class="pl-4 border-l-2 border-brand-200 ml-1 space-y-2 mt-2">
                                                    <template x-for="(option, optIndex) in question.options"
                                                        :key="optIndex">
                                                        <div class="flex gap-2">
                                                            <input type="text" :name="'questions['+index+'][options][]'"
                                                                x-model="question.options[optIndex]"
                                                                placeholder="Option..."
                                                                class="flex-1 text-xs border-gray-200 rounded focus:border-brand-500 focus:ring-brand-500 bg-white">
                                                            <button type="button"
                                                                @click="question.options.splice(optIndex, 1)"
                                                                class="text-gray-400 hover:text-red-500">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="question.options.push('')"
                                                        class="text-xs text-brand-600 font-bold hover:underline">+ Add
                                                        Option</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="questions.length === 0"
                                    class="text-center py-6 border-2 border-dashed border-gray-300 rounded-lg">
                                    <p class="text-sm text-gray-500">No questions added yet.</p>
                                    <button type="button" @click="addQuestion()"
                                        class="mt-1 text-brand-600 font-bold hover:underline text-sm">Add your first
                                        question</button>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                                <button type="button" @click="closeModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50 transition-colors">Cancel</button>
                                <button type="submit"
                                    class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg shadow-sm transition-colors transform hover:-translate-y-0.5">Create
                                    Form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function evaluationManager() {
                return {
                    modalOpen: false,
                    questions: [
                        { id: Date.now(), text: '', type: 'text', required: true, options: [''] }
                    ],

                    openModal() {
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                        // Optional: Confirm if dirty? For now just close.
                    },

                    addQuestion() {
                        this.questions.push({
                            id: Date.now() + Math.random(),
                            text: '',
                            type: 'text',
                            required: true,
                            options: ['']
                        });
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>