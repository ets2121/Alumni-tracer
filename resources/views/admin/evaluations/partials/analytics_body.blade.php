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
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-brand-50 to-transparent"></div>
                <div class="relative z-10 w-full">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Participation</p>
                    <div class="flex items-end justify-between w-full">
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 leading-none">{{ $totalResponses }}</h3>
                            <p class="text-[10px] text-gray-400 mt-1">out of {{ $overallTotal }} total records</p>
                        </div>
                        <!-- Percentage Badge -->
                        @php
                            $percentage = $overallTotal > 0 ? round(($totalResponses / $overallTotal) * 100) : 0;
                        @endphp
                        <div class="flex flex-col items-end">
                            <span class="text-2xl font-black text-brand-600">{{ $percentage }}%</span>
                            <div class="w-12 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Course -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-50 to-transparent"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Top Dept/Course</p>
                    <h3 class="text-xl font-black text-gray-900 line-clamp-1" title="{{ $topCourse }}">{{ $topCourse }}
                    </h3>
                    <p class="text-[10px] text-gray-400 mt-1">Highest distinct volume</p>
                </div>
            </div>

            <!-- Latest Activity -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-green-50 to-transparent"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Last Submission</p>
                    <h3 class="text-lg font-black text-gray-900">
                        {{ $latestResponse ? $latestResponse->created_at->diffForHumans() : 'No data' }}
                    </h3>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-center items-center gap-2 group hover:shadow-md transition-shadow cursor-pointer"
                onclick="window.print()">
                <div
                    class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center group-hover:bg-brand-50 group-hover:scale-110 transition-all">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-brand-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <span
                    class="text-[10px] font-bold text-gray-500 group-hover:text-brand-600 uppercase tracking-wider">Print
                    Summary</span>
            </div>
        </div>

        <!-- Questions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 print:block print:w-full">
            <template x-for="(item, index) in analytics" :key="item.id">
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col break-inside-avoid mb-8 h-full">
                    <!-- Question Header -->
                    <div class="mb-6">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-brand-50 text-brand-700 uppercase tracking-wider mb-2">
                            Question <span x-text="index + 1" class="ml-1"></span>
                        </span>
                        <h4 class="font-bold text-gray-900 leading-snug text-base" x-text="item.question"></h4>
                        <p class="text-xs text-gray-400 mt-1" x-show="item.type === 'scale'">Rating Scale: 1 (Lowest) to
                            5 (Highest)</p>
                    </div>

                    <!-- Chart / Data Area -->
                    <div
                        class="flex-1 min-h-[300px] flex items-center justify-center relative bg-gray-50/30 rounded-xl p-4 border border-gray-50">
                        <!-- Chart View -->
                        <div x-show="['radio', 'checkbox', 'scale'].includes(item.type)" class="w-full h-full">
                            <canvas :id="'chart-' + item.id" class="w-full h-full"></canvas>
                            <!-- Fallback if no stats -->
                            <div x-show="!hasStats(item)"
                                class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 opacity-20 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wider opacity-50">No Data</span>
                            </div>
                        </div>

                        <!-- Text View -->
                        <div x-show="!['radio', 'checkbox', 'scale'].includes(item.type)"
                            class="w-full h-full overflow-hidden flex flex-col">
                            <ul x-show="item.text_answers && item.text_answers.length > 0"
                                class="space-y-3 overflow-y-auto max-h-[300px] scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-200 pr-2 pb-2">
                                <template x-for="ans in item.text_answers" :key="ans">
                                    <li
                                        class="bg-white p-3.5 rounded-lg text-sm text-gray-600 border border-gray-100 shadow-[0_2px_8px_-2px_rgba(0,0,0,0.05)] relative pl-9 transition-transform hover:scale-[1.01]">
                                        <svg class="w-4 h-4 absolute left-3 top-3.5 text-brand-300" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M14.017 21L14.017 18C14.017 16.8954 13.1216 16 12.0171 16H9.9626L11.854 12.2171L12.0494 11.8262H11.6125H7.01714V3H19.0171V21H14.017ZM6.68657 14.8584L4.99614 18.2391L4.65386 18.9237L4.01714 18.6053L2.69539 17.9444L1.31714 17.2553L6.01714 7.85528V3H0.0171415V14.8584H6.68657Z">
                                            </path>
                                        </svg>
                                        <span x-text="ans"></span>
                                    </li>
                                </template>
                            </ul>
                            <div x-show="!item.text_answers || item.text_answers.length === 0"
                                class="flex-1 flex flex-col items-center justify-center text-gray-400">
                                <span class="text-xs font-bold uppercase tracking-wider opacity-50">No text
                                    responses</span>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="mb-6">
                    <h4 class="font-black text-gray-900 text-lg uppercase tracking-tight">Program Distribution</h4>
                    <p class="text-xs text-gray-400 font-medium">Which programs are most active?</p>
                </div>
                <div class="relative h-[300px] w-full">
                    <canvas x-ref="courseDistChart"></canvas>
                </div>
            </div>

            <!-- Participation by Batch -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="mb-6">
                    <h4 class="font-black text-gray-900 text-lg uppercase tracking-tight">Batch Demographics</h4>
                    <p class="text-xs text-gray-400 font-medium">Response breakdown by graduation year</p>
                </div>
                <div class="relative h-[300px] w-full">
                    <canvas x-ref="batchDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('analyticsDashboard', (initData) => ({
            analytics: initData.analytics,
            trendLabels: initData.trendLabels,
            trendValues: initData.trendValues,
            courseDistLabels: initData.courseDistLabels || [],
            courseDistValues: initData.courseDistValues || [],
            batchDistLabels: initData.batchDistLabels || [],
            batchDistValues: initData.batchDistValues || [],
            activeTab: 'overview', // 'overview' or 'insights'

            init() {
                this.$watch('activeTab', (value) => {
                    // Re-render charts when tab switches if needed, 
                    // but Charts.js usually handles hidden canvases okay if initialized.
                    // However, initializing them only when visible is safer for sizing.
                    this.$nextTick(() => {
                        if (value === 'insights') {
                            this.initInsightsCharts();
                        } else {
                            this.initCharts();
                        }
                    });
                });

                // Initialize default tab
                this.initCharts();
                // We also init trend chart in background or when tab 2 is clicked.
                // Let's init everything but standard charts might need visibility.
            },

            applyFilters(form) {
                // Determine if we are in modal or page
                // If in page, default submit works (remove preventDefault)
                // If in modal, we need to fetch new partial.

                // For now, let's assume this component handles its own reloading or redirect
                // If this is inside the report modal, the form action needs to be correct.
                // Since simpler approach: Just reload the page or trigger the parent to filter.

                // Hack: If we detect we are in a modal (parent has reportManager), we might call that.
                // Or simpler: Just submit form to current URL (works for standalone).
                // For modal, we need `generateReport` to accept these params.

                const formData = new FormData(form);
                const params = new URLSearchParams(formData);

                // Check if `reportManager` is available in specific parent scope? hard to tell.
                // Easier: Dispatch an event that the parent listens to.
                this.$dispatch('filters-applied', Object.fromEntries(formData));

                // If standalone (no listener), submit manually
                if (!window.Alpine.store('modalContext')) {
                    window.location.search = params.toString();
                }
            },

            hasStats(item) {
                return item.stats && Object.keys(item.stats).length > 0 && Object.values(item.stats).some(v => v > 0);
            },

            initInsightsCharts() {
                this.$nextTick(() => {
                    this.initTrendChart();
                    this.initCourseDistChart();
                    this.initBatchDistChart();
                });
            },

            initTrendChart() {
                if (this.trendValues.length === 0 || !this.$refs.trendChart) return;

                // Destroy existing if any? Chart.js 3+ helps, but good practice to check attached instance.
                // For simplicity assuming fresh init or idempotent.

                const ctx = this.$refs.trendChart.getContext('2d');
                if (Chart.getChart(ctx)) Chart.getChart(ctx).destroy();

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.trendLabels,
                        datasets: [{
                            label: 'New Responses',
                            data: this.trendValues,
                            borderColor: '#4F46E5',
                            backgroundColor: (context) => {
                                const ctx = context.chart.ctx;
                                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
                                return gradient;
                            },
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#4F46E5',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#F3F4F6' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            },

            initCourseDistChart() {
                if (!this.$refs.courseDistChart) return;
                const ctx = this.$refs.courseDistChart.getContext('2d');
                if (Chart.getChart(ctx)) Chart.getChart(ctx).destroy();

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.courseDistLabels,
                        datasets: [{
                            label: 'Responses',
                            data: this.courseDistValues,
                            backgroundColor: '#0ea5e9',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false } }
                        }
                    }
                });
            },

            initBatchDistChart() {
                if (!this.$refs.batchDistChart) return;
                const ctx = this.$refs.batchDistChart.getContext('2d');
                if (Chart.getChart(ctx)) Chart.getChart(ctx).destroy();

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: this.batchDistLabels,
                        datasets: [{
                            label: 'Responses',
                            data: this.batchDistValues,
                            backgroundColor: [
                                '#6366f1', '#ec4899', '#8b5cf6', '#10b981', '#f59e0b'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            },

            initCharts() {
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#6B7280';

                this.$nextTick(() => {
                    this.analytics.forEach(item => {
                        if (['radio', 'checkbox', 'scale'].includes(item.type) && this.hasStats(item)) {
                            const ctx = document.getElementById('chart-' + item.id);
                            if (!ctx) return;

                            if (Chart.getChart(ctx)) return; // Already exists

                            const labels = Object.keys(item.stats);
                            const data = Object.values(item.stats);

                            let chartType = 'bar';
                            let indexAxis = 'x';
                            let legendDisplay = false;

                            if (item.type === 'radio') {
                                chartType = 'doughnut';
                                legendDisplay = true;
                            }
                            if (item.type === 'checkbox') {
                                chartType = 'bar';
                                indexAxis = 'y'; // Readable long labels
                            }

                            // Professional Palette
                            const backgrounds = [
                                'rgba(79, 70, 229, 0.8)',   // Indigo
                                'rgba(16, 185, 129, 0.8)',  // Emerald
                                'rgba(245, 158, 11, 0.8)',  // Amber
                                'rgba(239, 68, 68, 0.8)',   // Red
                                'rgba(139, 92, 246, 0.8)',  // Violet
                                'rgba(236, 72, 153, 0.8)',  // Pink
                                'rgba(6, 182, 212, 0.8)',   // Cyan
                            ];

                            new Chart(ctx, {
                                type: chartType,
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Count',
                                        data: data,
                                        backgroundColor: chartType === 'doughnut' ? backgrounds : '#4F46E5',
                                        borderRadius: 4,
                                        borderWidth: 0,
                                        hoverOffset: 4
                                    }]
                                },
                                options: {
                                    indexAxis: indexAxis,
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: legendDisplay,
                                            position: 'right',
                                            labels: {
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 15,
                                                font: { size: 11, weight: 600 }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: '#1F2937',
                                            padding: 12,
                                            cornerRadius: 8,
                                            callbacks: {
                                                label: function (context) {
                                                    let label = context.dataset.label || '';
                                                    if (label) label += ': ';
                                                    let value = context.parsed.y !== null ? context.parsed.y : context.parsed;
                                                    if (indexAxis === 'y') value = context.parsed.x;
                                                    let total = context.chart._metasets[context.datasetIndex].total;
                                                    let percentage = Math.round((value / total) * 100) + '%';
                                                    return label + value + ' (' + percentage + ')';
                                                }
                                            }
                                        }
                                    },
                                    scales: chartType !== 'doughnut' ? {
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: '#F3F4F6' },
                                            ticks: {
                                                stepSize: 1,
                                                font: { size: 10, weight: 600 }
                                            }
                                        },
                                        x: {
                                            grid: { display: false },
                                            ticks: { font: { size: 10 } }
                                        }
                                    } : {
                                        // Hide scales for doughnut
                                        x: { display: false },
                                        y: { display: false }
                                    },
                                    layout: {
                                        padding: chartType === 'doughnut' ? 20 : 0
                                    }
                                }
                            });
                        }
                    });
                });
            }
        }));
    });
</script>