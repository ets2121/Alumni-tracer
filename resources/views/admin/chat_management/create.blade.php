<x-layouts.admin>
    <x-slot name="header">
        Create Chat Room
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            <div class="p-8">
                @include('admin.chat_management.partials._form')
            </div>
        </div>
    </div>
</x-layouts.admin>