<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Graduate Tracer Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-dark-bg-elevated overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                <div class="mb-6 flex justify-center">
                    <div
                        class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-dark-text-primary mb-2">Submission Successful</h3>
                <p class="text-gray-600 dark:text-dark-text-muted mb-8">
                    Thank you for completing the CHED Graduate Tracer Survey. Your response has been recorded and will
                    be used for research purposes to improve course offerings and graduate employability.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('ched-gts.show') }}"
                        class="inline-flex items-center px-6 py-3 bg-white dark:bg-dark-bg-subtle border border-gray-300 dark:border-dark-border rounded-xl font-bold text-sm text-gray-700 dark:text-dark-text-primary uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-dark-bg-elevated focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        View My Response
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-6 py-3 bg-brand-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>