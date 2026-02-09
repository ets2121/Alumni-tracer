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
                                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider pl-6 rounded-tl-xl">
                                Title</th>
                            <th
                                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Responses</th>
                            <th
                                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 text-right text-xs font-bold text-gray-500 uppercase tracking-wider pr-6 rounded-tr-xl">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($forms as $form)
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-5 bg-white text-sm">
                                    <div class="flex flex-col">
                                        <p class="text-gray-900 font-bold whitespace-no-wrap text-base">{{ $form->title }}
                                        </p>
                                        <p class="text-gray-400 text-xs mt-1 line-clamp-1">
                                            {{ Str::limit($form->description, 60) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-5 bg-white text-sm">
                                    <div class="flex flex-col items-start gap-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                            {{ ucfirst($form->type) }}
                                        </span>
                                        <div class="flex gap-2">
                                            <span
                                                class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded border border-gray-200 font-mono">v{{ $form->version }}</span>
                                            @if($form->is_draft)
                                                <span
                                                    class="text-[10px] bg-amber-50 text-amber-600 px-2 py-0.5 rounded border border-amber-100 font-bold uppercase tracking-wider">Draft</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 bg-white text-sm text-center">
                                    <span class="font-black text-gray-800 text-lg">{{ $form->responses_count }}</span>
                                </td>
                                <td class="px-6 py-5 bg-white text-sm text-center">
                                    @if($form->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            Closed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 bg-white text-sm text-right">
                                    <div
                                        class="flex justify-end gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <!-- Analytics / Results -->
                                        <!-- Analytics / Results -->
                                        <!-- Analytics / Results -->
                                        <button type="button"
                                            @click="openAnalyticsModal({{ $form->id }}, '{{ addslashes($form->title) }}')"
                                            class="p-2 rounded-lg text-brand-600 hover:bg-brand-50 transition-all duration-200"
                                            title="View Analytics">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </button>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.evaluations.edit', $form->id) }}"
                                            class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="Edit Form">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Duplicate Action -->
                                        <button type="button" @click="$dispatch('open-confirmation-modal', { 
                                                                                                title: 'Duplicate Form', 
                                                                                                message: 'Create a draft copy of \'{{ $form->title }}\'?', 
                                                                                                action: '{{ route('admin.evaluations.duplicate', $form->id) }}', 
                                                                                                method: 'POST', 
                                                                                                confirmText: 'Duplicate' 
                                                                                            })"
                                            class="p-2 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                            title="Duplicate Form">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>

                                        <!-- Delete Action -->
                                        <button type="button" @click="$dispatch('open-confirmation-modal', { 
                                                                                                title: 'Delete Evaluation Form', 
                                                                                                message: 'Are you sure you want to delete \'{{ $form->title }}\'? This will permanently delete all associated questions and responses.', 
                                                                                                action: '{{ route('admin.evaluations.destroy', $form->id) }}', 
                                                                                                method: 'DELETE', 
                                                                                                danger: true,
                                                                                                confirmText: 'Delete Form' 
                                                                                            })"
                                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            title="Delete Form">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">No Forms Created</h3>
                                        <p class="text-gray-500 text-sm mt-1">Get started by creating your first evaluation
                                            form.</p>
                                    </div>
                                </td>
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

                            <div class="mt-4 flex items-center justify-between border-t pt-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="save_as_active" value="1"
                                        class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                                    <span class="ml-2 text-sm text-gray-600 font-bold">Publish Immediately
                                        (Active)</span>
                                </label>
                                <div class="flex gap-3">
                                    <button type="button" @click="closeModal()"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50 transition-colors">Cancel</button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg shadow-sm transition-colors transform hover:-translate-y-0.5">Create
                                        Form</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Analytics Modal -->
        <div x-show="analyticsModalOpen" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
            <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" x-show="analyticsModalOpen"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="closeAnalyticsModal()"></div>

            <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6" x-show="analyticsModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="bg-white rounded-2xl shadow-xl w-full max-w-7xl max-h-[90vh] flex flex-col overflow-hidden"
                    @click.stop @filters-applied.window="handleFilters($event.detail)">

                    <!-- Modal Header -->
                    <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100 bg-white z-10">
                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Evaluation Results</h3>
                        <button @click="closeAnalyticsModal()"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto flex-1 p-8 bg-gray-50 custom-scrollbar relative">
                        <!-- Loading State -->
                        <div x-show="analyticsLoading"
                            class="absolute inset-0 bg-white/50 z-20 flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-10 w-10 text-brand-600 mb-4"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Loading
                                    Data...</span>
                            </div>
                        </div>

                        <!-- Dynamic Content -->
                        <div x-html="analyticsContent" class="h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('admin.evaluations.partials.analytics_script')
    @endpush



</x-layouts.admin>