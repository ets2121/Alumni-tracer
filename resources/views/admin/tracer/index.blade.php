<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-dark-text-primary leading-tight">
            {{ __('Graduate Tracer Survey Responses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-dark-bg-deep overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-dark-text-primary">

                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
                            <h4 class="text-sm font-bold uppercase tracking-wider opacity-80">Total Responses</h4>
                            <p class="text-4xl font-extrabold mt-2">{{ number_format($totalResponses) }}</p>
                        </div>
                        <div
                            class="md:col-span-2 bg-white dark:bg-dark-bg-elevated border border-gray-100 dark:border-dark-border rounded-lg p-6 shadow-sm">
                            <h4
                                class="text-sm font-bold text-gray-500 dark:text-dark-text-muted uppercase tracking-wider mb-4">
                                Top Participating
                                Departments</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                                @forelse($deptStats as $stat)
                                    <div class="text-center p-2 bg-gray-50 dark:bg-dark-bg-subtle rounded">
                                        <div class="text-lg font-bold text-gray-800 dark:text-dark-text-primary">
                                            {{ $stat->total }}</div>
                                        <div class="text-xs text-gray-500 dark:text-dark-text-muted truncate"
                                            title="{{ $stat->department_name }}">
                                            {{ $stat->department_name }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-gray-400 text-sm">No data available</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-dark-text-primary">All Responses</h3>

                        <!-- Advanced Export Form -->
                        <form action="{{ route('admin.tracer.export') }}" method="GET" target="_blank"
                            class="flex items-center gap-2 bg-gray-50 dark:bg-dark-bg-subtle p-2 rounded-lg border border-gray-200 dark:border-dark-border">
                            <label
                                class="text-xs font-semibold uppercase text-gray-500 dark:text-dark-text-muted">Export:</label>
                            <select name="section"
                                class="text-sm border-gray-300 dark:border-dark-border dark:bg-dark-bg-deep dark:text-dark-text-primary rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                <option value="all">Entire Survey</option>
                                <option value="reference">Reference / Gen. Info</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}">{{ Str::limit($sec, 30) }}</option>
                                @endforeach
                            </select>
                            <select name="format"
                                class="text-sm border-gray-300 dark:border-dark-border dark:bg-dark-bg-deep dark:text-dark-text-primary rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                <option value="excel">Excel (.xls)</option>
                                <option value="csv">CSV (.csv)</option>
                            </select>
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-3 rounded text-sm transition shadow flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download
                            </button>
                        </form>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.tracer.index') }}" class="mb-6 flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by name or email"
                                class="w-full border-gray-300 dark:border-dark-border dark:bg-dark-bg-subtle dark:text-dark-text-primary rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                        </div>
                        <div class="w-full sm:w-auto">
                            <select name="department"
                                class="w-full border-gray-300 dark:border-dark-border dark:bg-dark-bg-subtle dark:text-dark-text-primary rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-auto">
                            <select name="year"
                                class="w-full border-gray-300 dark:border-dark-border dark:bg-dark-bg-subtle dark:text-dark-text-primary rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                <option value="">All Years</option>
                                @foreach(range(date('Y'), 2020) as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-auto">
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                                Filter
                            </button>
                            <a href="{{ route('admin.tracer.index') }}"
                                class="ml-2 text-gray-600 dark:text-dark-text-muted hover:text-gray-800 dark:hover:text-dark-text-secondary underline">Reset</a>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-border">
                            <thead class="bg-gray-50 dark:bg-dark-bg-subtle">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                                        Alumni</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                                        Department</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                                        Date Submitted</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-text-muted uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-dark-bg divide-y divide-gray-200 dark:divide-dark-border">
                                @forelse($responses as $response)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-0">
                                                    <div
                                                        class="text-sm font-medium text-gray-900 dark:text-dark-text-primary">
                                                        {{ $response->user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-dark-text-muted">
                                                        {{ $response->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                {{ $response->department_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-dark-text-secondary">
                                            {{ $response->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('admin.tracer.show', $response->id) }}"
                                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors"
                                                    title="View Details">
                                                    View
                                                </a>
                                                <a href="{{ route('admin.tracer.pdf', $response->id) }}" target="_blank"
                                                    class="text-gray-600 dark:text-dark-text-muted hover:text-gray-900 dark:hover:text-dark-text-secondary transition-colors"
                                                    title="Print / PDF">
                                                    PDF
                                                </a>
                                                <a href="{{ route('admin.tracer.export.individual', ['response' => $response->id, 'format' => 'excel']) }}"
                                                    class="text-green-600 hover:text-green-900" title="Export Excel">
                                                    Export
                                                </a>
                                                <form action="{{ route('admin.tracer.destroy', $response->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this response? The alumni will be able to take the survey again.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        title="Delete Response">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-dark-text-muted italic">No
                                            responses found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $responses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>