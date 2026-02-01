<x-layouts.admin>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Stats Card Example -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Alumni</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
        </div>
        <!-- More cards will go here -->
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            {{ __("You're logged in as Admin!") }}
        </div>
    </div>
</x-layouts.admin>