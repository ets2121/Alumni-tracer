<div x-data="analyticsDashboard({
        analytics: {{ json_encode($analytics) }},
        trendLabels: {{ json_encode($trendLabels) }},
        trendValues: {{ json_encode($trendValues) }},
        courseDistLabels: {{ json_encode($courseDistLabels ?? []) }},
        courseDistValues: {{ json_encode($courseDistValues ?? []) }},
        batchDistLabels: {{ json_encode($batchDistLabels ?? []) }},
        batchDistValues: {{ json_encode($batchDistValues ?? []) }}
    })">

    <!-- Tab Navigation -->
    <div class="flex items-center space-x-1 bg-gray-100/50 p-1.5 rounded-2xl mb-8 w-fit mx-auto md:mx-0">
        <button @click="activeTab = 'overview'"
            :class="activeTab === 'overview' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
            Overview
        </button>
        <button @click="activeTab = 'insights'"
            :class="activeTab === 'insights' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
            Deep Insights
        </button>
    </div>

    <!-- Filters (Collapsible) -->
    <div x-data="{ open: false }" class="mb-8">
        <button @click="open = !open"
            class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-brand-600 transition-colors mb-4">
            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            Filter Dataset
        </button>
        <div x-show="open" x-collapse>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <!-- IMPORTANT: Form submission handling for Modal Context vs Full Page -->
                <!-- We use a regular form for full page, but for modal we might need to trap variables. 
                     However, simpler to just use 'GET' parameters and let the controller return the partial. 
                     If in modal, we intercept this form submit in the parent Alpine component. -->
                <form method="GET" @submit.prevent="applyFilters($el)" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="hidden" name="type" value="evaluation_results">
                    <!-- For ReportController routing if needed -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Course</label>
                        <select name="course"
                            class="w-full text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 transition-shadow">
                            <option value="">All Courses</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>
                                    {{ $c->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Batch</label>
                        <select name="batch"
                            class="w-full text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 transition-shadow">
                            <option value="">All Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch }}" {{ request('batch') == $batch ? 'selected' : '' }}>
                                    {{ $batch }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date Start</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 transition-shadow">
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date End</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full text-sm border-gray-200 rounded-lg focus:ring-brand-500 focus:border-brand-500 transition-shadow">
                        </div>
                        <button type="submit"
                            class="bg-brand-600 text-white p-2.5 rounded-lg hover:bg-brand-700 transition-all shadow-lg shadow-brand-100 transform hover:-translate-y-0.5"
                            title="Apply Filters">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </button>
                        <button @click.prevent="resetFilters()"
                            class="bg-gray-100 text-gray-500 p-2.5 rounded-lg hover:bg-gray-200 transition-colors"
                            title="Clear Filters">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Filtered Responses -->
            <div
                class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-brand-50/50 to-transparent"></div>
                <!-- Decorative Icon -->
                <div
                    class="absolute -right-4 -bottom-4 opacity-10 text-brand-600 transform -rotate-12 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                        </path>
                    </svg>
                </div>

                <div class="relative z-10 w-full">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="p-1.5 bg-brand-100 text-brand-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </span>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Participation</p>
                    </div>

                    <div class="flex items-end justify-between w-full mt-2">
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 leading-none">{{ $totalResponses }}</h3>
                            <p class="text-[10px] text-gray-400 mt-1 font-medium">Total Respondents</p>
                        </div>
                        <!-- Percentage Badge -->
                        @php
                            $percentage = $overallTotal > 0 ? round(($totalResponses / $overallTotal) * 100) : 0;
                        @endphp
                        <div class="flex flex-col items-end">
                            <span class="text-xl font-black text-brand-600">{{ $percentage }}%</span>
                            <div class="w-16 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full shadow-[0_0_10px_rgba(79,70,229,0.3)]"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Course -->
            <div
                class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-50/50 to-transparent"></div>
                <div
                    class="absolute -right-4 -bottom-4 opacity-10 text-blue-600 transform -rotate-12 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                        </path>
                    </svg>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="p-1.5 bg-blue-100 text-blue-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </span>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Top Program</p>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 line-clamp-2 leading-tight min-h-[3rem]"
                        title="{{ $topCourse }}">{{ $topCourse ?? 'N/A' }}
                    </h3>
                    <p
                        class="text-[10px] text-blue-600 font-bold mt-1 bg-blue-50 inline-block px-2 py-0.5 rounded-full border border-blue-100">
                        Most Active</p>
                </div>
            </div>

            <!-- Latest Activity -->
            <div
                class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-green-50/50 to-transparent"></div>
                <div
                    class="absolute -right-4 -bottom-4 opacity-10 text-green-600 transform -rotate-12 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="p-1.5 bg-green-100 text-green-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Last Activity</p>
                    </div>
                    <h3 class="text-xl font-black text-gray-900">
                        {{ $latestResponse ? $latestResponse->created_at->diffForHumans() : 'N/A' }}
                    </h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-medium">
                        {{ $latestResponse ? $latestResponse->created_at->format('M d, Y h:i A') : 'No submissions yet' }}
                    </p>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 relative overflow-hidden flex flex-col justify-center items-center gap-3 group hover:bg-white hover:shadow-md transition-all duration-300 cursor-pointer"
                onclick="window.print()">
                <div
                    class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 transform group-hover:rotate-3">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div class="text-center">
                    <span
                        class="block text-xs font-black text-gray-700 group-hover:text-brand-600 uppercase tracking-wider mb-0.5">Print
                        Report</span>
                    <span class="text-[10px] text-gray-400">Download PDF</span>
                </div>
            </div>
        </div>

        <!-- Questions Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 print:block print:w-full">
            <template x-for="(item, index) in analytics" :key="item.id || index">
                <div x-data="{ expanded: true }"
                    class="bg-white rounded-2xl shadow-[0_2px_10px_-2px_rgba(0,0,0,0.05)] border border-gray-100 flex flex-col break-inside-avoid h-full transition-all duration-300 hover:shadow-md">

                    <!-- Question Header (Click to Toggle) -->
                    <div @click="expanded = !expanded"
                        class="p-5 cursor-pointer flex justify-between items-start gap-4 border-b border-gray-50 bg-gray-50/30 rounded-t-2xl">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-brand-50 text-brand-700 uppercase tracking-wider">
                                    Q<span x-text="index + 1" class="ml-0.5"></span>
                                </span>
                                <span x-show="item.type === 'scale' && item.average"
                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                    <span x-text="item.average"></span>
                                    <svg class="w-3 h-3 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 leading-snug text-sm line-clamp-2" x-text="item.question"
                                :title="item.question"></h4>
                        </div>

                        <!-- Toggle Icon -->
                        <div class="text-gray-400 transition-transform duration-200"
                            :class="expanded ? 'rotate-180' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Collapsible Content -->
                    <div x-show="expanded" x-collapse>
                        <div class="p-5 pt-4">
                            <!-- Chart / Data Area -->
                            <div
                                class="w-full h-64 flex items-center justify-center relative bg-gray-50/50 rounded-xl p-2 border border-blue-50/50">
                                <!-- Chart View -->
                                <div x-show="['radio', 'checkbox', 'scale'].includes(item.type)" class="w-full h-full">
                                    <canvas :id="'chart-' + item.id" class="w-full h-full"></canvas>
                                    <!-- Fallback if no stats -->
                                    <div x-show="!hasStats(item)"
                                        class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8 opacity-20 mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider opacity-50">No
                                            Data</span>
                                    </div>
                                </div>

                                <!-- Text View -->
                                <div x-show="!['radio', 'checkbox', 'scale'].includes(item.type)"
                                    class="w-full h-full overflow-hidden flex flex-col">
                                    <ul x-show="item.text_answers && item.text_answers.length > 0"
                                        class="space-y-2 overflow-y-auto max-h-full scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-200 pr-1">
                                        <template x-for="(ans, aIdx) in (item.text_answers || [])" :key="aIdx">
                                            <li
                                                class="bg-white p-2.5 rounded-lg text-xs text-gray-600 border border-gray-100 shadow-sm relative pl-7 hover:bg-gray-50">
                                                <svg class="w-3 h-3 absolute left-2.5 top-3 text-brand-300"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.017 21L14.017 18C14.017 16.8954 13.1216 16 12.0171 16H9.9626L11.854 12.2171L12.0494 11.8262H11.6125H7.01714V3H19.0171V21H14.017ZM6.68657 14.8584L4.99614 18.2391L4.65386 18.9237L4.01714 18.6053L2.69539 17.9444L1.31714 17.2553L6.01714 7.85528V3H0.0171415V14.8584H6.68657Z">
                                                    </path>
                                                </svg>
                                                <span x-text="ans"></span>
                                            </li>
                                        </template>
                                    </ul>
                                    <div x-show="!item.text_answers || (item.text_answers && item.text_answers.length === 0)"
                                        class="flex-1 flex flex-col items-center justify-center text-gray-400">
                                        <span class="text-[10px] font-bold uppercase tracking-wider opacity-50">No text
                                            responses</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>


    <!-- TAB 2: DEEP INSIGHTS -->
    <div x-show="activeTab === 'insights'" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100">

        <!-- Response Volume Trend -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h4 class="font-black text-gray-900 text-xl uppercase tracking-tight">Timeline Analysis</h4>
                    <p class="text-xs text-gray-400 font-medium">Submission frequency over the selected period</p>
                </div>
                <span
                    class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Time
                    Series</span>
            </div>
            <div class="relative h-[350px] w-full" x-show="trendValues.length > 0">
                <canvas x-ref="trendChart"></canvas>
            </div>
            <div x-show="trendValues.length === 0"
                class="h-[350px] flex items-center justify-center text-gray-400 italic text-sm">
                No trend data available for this range.
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Participation by Course -->
            <div
                class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h4 class="font-black text-gray-900 text-lg uppercase tracking-tight">Program Distribution</h4>
                        <p class="text-xs text-gray-400 font-medium">Which programs are most active?</p>
                    </div>
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="relative h-[300px] w-full">
                    <canvas x-ref="courseDistChart"></canvas>
                </div>
            </div>

            <!-- Participation by Batch -->
            <div
                class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h4 class="font-black text-gray-900 text-lg uppercase tracking-tight">Batch Demographics</h4>
                        <p class="text-xs text-gray-400 font-medium">Response breakdown by graduation year</p>
                    </div>
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative h-[300px] w-full">
                    <canvas x-ref="batchDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>