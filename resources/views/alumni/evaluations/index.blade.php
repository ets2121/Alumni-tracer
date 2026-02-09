<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Evaluations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-6">Available Forms & Surveys</h3>

                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">{{ session('info') }}
                        </div>
                    @endif

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" x-data="alumniEvaluationList()">

                        <!-- Skeleton Loader -->
                        <template x-if="loading">
                            <div class="contents">
                                <div class="border rounded-xl p-6 bg-white shadow-sm animate-pulse h-64">
                                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
                                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-auto"></div>
                                    <div class="h-10 bg-gray-200 rounded w-full mt-4"></div>
                                </div>
                                <div class="border rounded-xl p-6 bg-white shadow-sm animate-pulse h-64">
                                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
                                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-auto"></div>
                                    <div class="h-10 bg-gray-200 rounded w-full mt-4"></div>
                                </div>
                                <div class="border rounded-xl p-6 bg-white shadow-sm animate-pulse h-64">
                                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
                                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-auto"></div>
                                    <div class="h-10 bg-gray-200 rounded w-full mt-4"></div>
                                </div>
                            </div>
                        </template>

                        <!-- Data Loop -->
                        <template x-for="form in forms" :key="form.id">
                            <div
                                class="border rounded-xl p-6 bg-white hover:shadow-md transition-shadow flex flex-col h-full relative overflow-hidden group">
                                <div
                                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                    <svg class="w-24 h-24 text-brand-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                    </svg>
                                </div>
                                <div class="flex-1 relative z-10">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-brand-800 bg-brand-100 rounded-full mb-3 uppercase tracking-wide"
                                        x-text="form.type"></span>
                                    <h4 class="text-xl font-bold text-gray-900 mb-2" x-text="form.title"></h4>
                                    <p class="text-gray-600 text-sm mb-4" x-text="form.description"></p>
                                </div>

                                <div class="pt-4 border-t border-gray-100 relative z-10 mt-auto">
                                    <template x-if="form.responses_count > 0">
                                        <button disabled
                                            class="w-full py-2 bg-gray-100 text-gray-400 font-bold rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completed
                                        </button>
                                    </template>
                                    <template x-if="form.responses_count == 0">
                                        <a :href="`/alumni/evaluations/${form.id}`"
                                            class="block w-full text-center py-2 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 transition-colors shadow-sm">
                                            Start Survey
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="!loading && forms.length === 0"
                            class="col-span-full text-center py-12 text-gray-500">
                            <p class="text-lg">No active evaluations at the moment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>