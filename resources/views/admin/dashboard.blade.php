<x-layouts.admin>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Total Alumni</h3>
            <p class="text-4xl font-black text-brand-600 mt-2">{{ number_format($stats['total_alumni']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Pending</h3>
            <p class="text-4xl font-black text-amber-500 mt-2">{{ number_format($stats['pending_alumni']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">News & Events</h3>
            <p class="text-4xl font-black text-purple-600 mt-2">{{ number_format($stats['total_events']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Gallery Albums</h3>
            <p class="text-4xl font-black text-blue-600 mt-2">{{ number_format($stats['total_gallery']) }}</p>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            @if(Auth::user()->isDepartmentAdmin())
                {{ __("You're logged in as Department Administrator for ") }}
                <strong>{{ Auth::user()->department_name }}</strong>.
            @else
                {{ __("You're logged in as System Administrator.") }}
            @endif
        </div>
    </div>
</x-layouts.admin>