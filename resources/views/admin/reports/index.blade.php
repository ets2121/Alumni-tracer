<x-layouts.admin>
    <div class="py-6" x-data="reportManager()" x-init="init()">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-10">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol
                        class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-[0.2em]">
                        <li class="inline-flex items-center text-gray-400">Admin</li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-brand-600">Analytics & Reports</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Institutional Intelligence</h2>
                <p class="text-sm text-gray-500 font-medium">Capture insights and generate high-fidelity reports for
                    academic planning.</p>
            </div>

            <div
                class="flex items-center gap-3 bg-white/80 backdrop-blur-md p-2 rounded-[1.5rem] shadow-sm border border-gray-100 ring-1 ring-black/[0.03]">
                <div class="flex items-center px-4 gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                    </svg>
                    <input type="date" x-model="fromDate"
                        class="border-none bg-transparent focus:ring-0 text-xs font-bold text-gray-700 p-0"
                        title="Start Date">
                </div>
                <div class="h-4 w-px bg-gray-200"></div>
                <div class="flex items-center px-4 gap-2">
                    <input type="date" x-model="toDate"
                        class="border-none bg-transparent focus:ring-0 text-xs font-bold text-gray-700 p-0"
                        title="End Date">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select x-model="workStatus"
                    class="bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-brand-500 shadow-sm transition-all h-10">
                    <option value="">Work Status</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Contractual">Contractual</option>
                    <option value="Job Order">Job Order</option>
                </select>

                <select x-model="establishmentType"
                    class="bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-brand-500 shadow-sm transition-all h-10">
                    <option value="">Establishment</option>
                    <option value="Public">Public</option>
                    <option value="Private">Private</option>
                </select>

                <select x-model="workLocation"
                    class="bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-brand-500 shadow-sm transition-all h-10">
                    <option value="">Location</option>
                    <option value="Local">Local</option>
                    <option value="Overseas">Overseas</option>
                </select>

                <select x-model="courseId"
                    class="bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-brand-500 shadow-sm transition-all h-10 w-40">
                    <option value="">All Programs</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->code }}</option>
                    @endforeach
                </select>

                <select x-model="batchYear"
                    class="bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-brand-500 shadow-sm transition-all h-10">
                    <option value="">All Batches</option>
                    @for($y = date('Y'); $y >= 1990; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Report Control Suite -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Graduates by Course -->
            <div @click="generateReport('graduates_by_course')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-brand-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-brand-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-brand-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Graduates Registry</h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Grouped analysis of academic
                        programs and graduation batches.</p>
                    <div class="flex items-center text-[10px] font-black text-brand-600 uppercase tracking-widest">
                        <span>Generate Matrix</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Employment Analytics -->
            <div @click="generateReport('employment_status')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-blue-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Labor Insights</h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Employment distribution and
                        industrial transition metrics.</p>
                    <div class="flex items-center text-[10px] font-black text-blue-600 uppercase tracking-widest">
                        <span>Analyze Status</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Geographic Mapping -->
            <div @click="generateReport('location')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-amber-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Demographic Map</h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Geographical spread analysis of
                        the global alumni community.</p>
                    <div class="flex items-center text-[10px] font-black text-amber-600 uppercase tracking-widest">
                        <span>View Distribution</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Statistical Dashboard -->
            <div @click="generateReport('statistical_summary')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-purple-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-purple-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-purple-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Visual Intelligence
                    </h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Interactive charts and high-level
                        statistical summaries.</p>
                    <div class="flex items-center text-[10px] font-black text-purple-600 uppercase tracking-widest">
                        <span>Launch Charts</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tracer Study (CHED) -->
            <div @click="generateReport('tracer_study')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden lg:col-span-2">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8 flex flex-col md:flex-row md:items-center gap-10">
                    <div
                        class="w-20 h-20 bg-green-600 text-white rounded-3xl flex items-center justify-center shadow-xl shadow-green-100 group-hover:rotate-12 transition-transform shrink-0">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 2.944a11.955 11.955 0 018.618 3.04M12 21.189c4.736-2.559 8-7.429 8-12.429a11.955 11.955 0 01-8.618-3.04M12 21.189c-4.736-2.559-8-7.429-8-12.429a11.955 11.955 0 018.618-3.04" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div
                            class="inline-block px-3 py-1 bg-green-50 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest mb-3 border border-green-100">
                            Official Compliance</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-3 uppercase tracking-tighter">CHED Graduate
                            Tracer Study</h3>
                        <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6 max-w-xl">Comprehensive
                            analytical matrix for education-to-labor alignment monitoring as mandated by national
                            standards.</p>
                        <div class="flex items-center text-[10px] font-black text-green-600 uppercase tracking-widest">
                            <span>Compile Tracer Data</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div x-show="previewOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display:none;"
            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="flex items-center justify-center min-h-screen p-4 md:p-10">
                <div class="modal-backdrop" @click="previewOpen = false"></div>

                <div class="modal-content-container max-w-7xl w-full h-[90vh] flex flex-col p-0 overflow-hidden border-none ring-1 ring-black/[0.05]"
                    x-show="previewOpen" x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-20"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                    <!-- Header Toolbar -->
                    <div
                        class="bg-white/90 backdrop-blur-md px-10 py-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 bg-gray-900 text-white rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter"
                                    x-text="currentReportTitle"></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Alumni
                                        Intelligence Suite</span>
                                    <span class="h-1 w-1 bg-gray-300 rounded-full"></span>
                                    <span class="text-[9px] font-black text-brand-600 uppercase tracking-widest">Active
                                        Session</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button @click="printReport()"
                                class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.1em] transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Document
                            </button>
                            <button @click="exportCSV()"
                                class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.1em] transition-all shadow-lg shadow-brand-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export Spreadsheet
                            </button>
                            <div class="h-8 w-px bg-gray-100 mx-2"></div>
                            <button @click="previewOpen = false"
                                class="text-gray-300 hover:text-red-600 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Content -->
                    <div id="report-view" class="flex-1 overflow-y-auto p-12 bg-gray-50/50">
                        <div x-show="loading" class="flex flex-col items-center justify-center py-40 gap-6">
                            <div class="relative w-20 h-20">
                                <div class="absolute inset-0 border-8 border-brand-100 rounded-full"></div>
                                <div class="absolute inset-0 border-8 border-t-brand-600 rounded-full animate-spin">
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-black text-gray-900 uppercase tracking-tighter mb-1">Processing
                                    Records</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Assembling
                                    Analytical Matrix...</p>
                            </div>
                        </div>
                        <div id="report-content"
                            class="bg-white p-16 shadow-2xl rounded-[3rem] border border-gray-100 mx-auto max-w-5xl min-h-[11in]"
                            x-show="!loading"></div>
                    </div>

                    <!-- Footer Info -->
                    <div
                        class="bg-white px-10 py-5 border-t border-gray-100 flex justify-between items-center shrink-0">
                        <div
                            class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            This is an auto-generated institutional dataset.
                        </div>
                        <div class="text-[9px] font-black text-brand-600 uppercase tracking-widest">
                            Generated by {{ Auth::user()->name }} • {{ date('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function reportManager() {
                return {
                    fromDate: '',
                    toDate: '',
                    workStatus: '',
                    establishmentType: '',
                    workLocation: '',
                    courseId: '',
                    batchYear: '',
                    loading: false,
                    previewOpen: false,
                    currentReportType: '',
                    currentReportTitle: '',

                    init() { },

                    async generateReport(type) {
                        this.currentReportType = type;
                        this.currentReportTitle = type.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                        this.loading = true;
                        this.previewOpen = true;

                        const url = new URL('{{ route('admin.reports.generate') }}');
                        url.searchParams.set('type', type);
                        if (this.fromDate) url.searchParams.set('from_date', this.fromDate);
                        if (this.toDate) url.searchParams.set('to_date', this.toDate);
                        if (this.workStatus) url.searchParams.set('work_status', this.workStatus);
                        if (this.establishmentType) url.searchParams.set('establishment_type', this.establishmentType);
                        if (this.workLocation) url.searchParams.set('work_location', this.workLocation);
                        if (this.courseId) url.searchParams.set('course_id', this.courseId);
                        if (this.batchYear) url.searchParams.set('batch_year', this.batchYear);

                        try {
                            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await response.text();
                            document.getElementById('report-content').innerHTML = html;

                            if (type === 'statistical_summary') {
                                setTimeout(() => this.initCharts(), 100);
                            }
                        } catch (error) {
                            console.error('Report failed:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    initCharts() {
                        Chart.defaults.font.family = 'Figtree';
                        const baseConfig = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };

                        // 1. COMBINED MATRIX CHARTS
                        this.createChart('chartByCourse', 'bar', baseConfig, { colors: ['#6366f1'] }); // Changed to Bar as requested
                        this.createChart('chartByEmployment', 'pie', baseConfig, { colors: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] });
                        this.createChart('chartByGender', 'polarArea', baseConfig, { colors: ['rgba(99, 102, 241, 0.7)', 'rgba(236, 72, 153, 0.7)', 'rgba(100, 116, 139, 0.7)'] });
                        
                        // Stacked Bar for Stability
                        const ctxStability = document.getElementById('chartStabilityStacked');
                        if (ctxStability) {
                            new Chart(ctxStability, {
                                type: 'bar',
                                data: {
                                    labels: JSON.parse(ctxStability.dataset.labels),
                                    datasets: [
                                        { label: 'Permanent', data: JSON.parse(ctxStability.dataset.permanent), backgroundColor: '#10b981' },
                                        { label: 'Contractual', data: JSON.parse(ctxStability.dataset.contractual), backgroundColor: '#f59e0b' },
                                        { label: 'Job Order', data: JSON.parse(ctxStability.dataset.jo), backgroundColor: '#ef4444' }
                                    ]
                                },
                                options: { ...baseConfig, scales: { x: { stacked: true }, y: { stacked: true } } }
                            });
                        }

                        // Combination Chart for Registration vs Employment
                        const ctxCombo = document.getElementById('chartCombinationSummary');
                        if (ctxCombo) {
                            new Chart(ctxCombo, {
                                type: 'bar',
                                data: {
                                    labels: JSON.parse(ctxCombo.dataset.labels),
                                    datasets: [
                                        { type: 'bar', label: 'Total Alumni', data: JSON.parse(ctxCombo.dataset.total), backgroundColor: 'rgba(99, 102, 241, 0.2)', borderColor: '#6366f1', borderWidth: 1 },
                                        { type: 'line', label: 'Employed', data: JSON.parse(ctxCombo.dataset.employed), borderColor: '#10b981', borderWidth: 3, tension: 0.4, fill: false }
                                    ]
                                },
                                options: baseConfig
                            });
                        }

                        // 2. FOCUS VIEW CHARTS
                        this.createChart('chartProgramFocus', 'bar', baseConfig, {
                            indexAxis: 'y',
                            borderRadius: 20,
                            colors: ['#6366f1'],
                            scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
                        });
                        this.createChart('chartLaborFocus', 'bar', baseConfig, {
                            borderRadius: 20,
                            colors: ['#2563eb', '#10b981', '#f59e0b', '#ef4444'],
                            scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
                        });
                        this.createChart('chartGenderFocus', 'doughnut', baseConfig, { cutout: '70%', colors: ['#6366f1', '#ec4899', '#64748b'] });
                        this.createChart('chartCivilFocus', 'bar', baseConfig, { borderRadius: 12, colors: ['#8b5cf6'] });

                        // 4. LABOR ANALYTICS ADDITIONS
                        // Grouped Bar for Location Matrix
                        const ctxLoc = document.getElementById('chartLocationGrouped');
                        if (ctxLoc) {
                            new Chart(ctxLoc, {
                                type: 'bar',
                                data: {
                                    labels: JSON.parse(ctxLoc.dataset.labels),
                                    datasets: [
                                        { label: 'Local', data: JSON.parse(ctxLoc.dataset.local), backgroundColor: '#3b82f6' },
                                        { label: 'Overseas', data: JSON.parse(ctxLoc.dataset.overseas), backgroundColor: '#f97316' }
                                    ]
                                },
                                options: baseConfig
                            });
                        }

                        this.createChart('chartByEstablishmentPie', 'pie', baseConfig, { colors: ['#6366f1', '#a855f7'] });
                        this.createChart('chartByWorkLocationDonut', 'doughnut', baseConfig, { cutout: '70%', colors: ['#3b82f6', '#f97316'] });
                        
                        this.createChart('chartTopFieldsFocus', 'bar', baseConfig, {
                            indexAxis: 'y',
                            borderRadius: 20,
                            colors: ['#6366f1'],
                            scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
                        });

                        // Tracer Static Examples
                        this.createChart('chartTracerLikert', 'bar', baseConfig, { colors: ['#10b981', '#34d399', '#94a3b8', '#f87171', '#ef4444'] });
                        this.createChart('chartTracerMultiple', 'bar', baseConfig, { indexAxis: 'y', colors: ['#6366f1'] });

                        // 3. REGISTRATION TREND
                        const ctxTrend = document.getElementById('chartRegistrationTrend');
                        if (ctxTrend) {
                            new Chart(ctxTrend, {
                                type: 'line',
                                data: {
                                    labels: JSON.parse(ctxTrend.dataset.labels),
                                    datasets: [{
                                        data: JSON.parse(ctxTrend.dataset.values),
                                        borderColor: '#818cf8',
                                        borderWidth: 4,
                                        pointRadius: 6,
                                        pointBackgroundColor: '#fff',
                                        fill: true,
                                        backgroundColor: (context) => {
                                            const chart = context.chart;
                                            const { ctx, chartArea } = chart;
                                            if (!chartArea) return null;
                                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                            gradient.addColorStop(0, 'rgba(129, 140, 248, 0.2)');
                                            gradient.addColorStop(1, 'rgba(129, 140, 248, 0)');
                                            return gradient;
                                        },
                                        tension: 0.4
                                    }]
                                },
                                options: {
                                    ...baseConfig, scales: {
                                        y: { grid: { color: 'rgba(255,255,255,0.05)' }, border: { dash: [4, 4] }, ticks: { color: '#64748b', font: { weight: '800', size: 9 } } },
                                        x: { grid: { display: false }, ticks: { color: '#64748b', font: { weight: '800', size: 9 } } }
                                    }
                                }
                            });
                        }
                    },

                    createChart(id, type, baseConfig, options = {}) {
                        const ctx = document.getElementById(id);
                        if (!ctx) return;

                        const config = {
                            type: type,
                            data: {
                                labels: JSON.parse(ctx.dataset.labels),
                                datasets: [{
                                    data: JSON.parse(ctx.dataset.values),
                                    backgroundColor: options.colors || ['#6366f1'],
                                    ...options
                                }]
                            },
                            options: { ...baseConfig, ...options }
                        };
                        new Chart(ctx, config);
                    },

                    printReport() {
                        // Create a clone of the report content for manipulation
                        const reportContainer = document.getElementById('report-content');
                        const clone = reportContainer.cloneNode(true);

                        // Convert Canvas to Images for the print window
                        const originalCanvases = reportContainer.querySelectorAll('canvas');
                        const clonedCanvases = clone.querySelectorAll('canvas');

                        originalCanvases.forEach((canvas, index) => {
                            const dataUrl = canvas.toDataURL('image/png');
                            const img = document.createElement('img');
                            img.src = dataUrl;
                            img.style.maxWidth = '100%';
                            img.style.height = 'auto';
                            img.className = 'mx-auto block';
                            clonedCanvases[index].parentNode.replaceChild(img, clonedCanvases[index]);
                        });

                        const printWindow = window.open('', '', 'height=1000,width=1200');
                        const logoUrl = '{{ asset('images/logo-1.png') }}';

                        printWindow.document.write('<html><head><title>Academic Intelligence Report</title>');
                        printWindow.document.write('<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />');
                        printWindow.document.write('<script src="https://cdn.tailwindcss.com"><\/script>');
                        printWindow.document.write('<style>@media print { .no-print { display: none; } } body { font-family: "Figtree", sans-serif; }</style>');
                        printWindow.document.write('</head><body class="p-16 bg-white min-h-screen">');

                        // Premium Print Header with Logo
                        printWindow.document.write(`
                                    <div class="mb-12 pb-8 border-b-4 border-gray-900 flex justify-between items-end">
                                        <div class="flex items-center gap-6">
                                            <img src="${logoUrl}" class="w-16 h-16 object-contain" alt="Logo">
                                            <div class="h-12 w-px bg-gray-200"></div>
                                            <div>
                                                <h1 class="text-2xl font-black uppercase tracking-[0.1em] text-gray-900 leading-none">Alumni Management System</h1>
                                                <p class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em] mt-2">Official Analytical Intelligence Record</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Document ID: AMS-${Date.now()}</p>
                                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mt-1">Generated: {{ date('F d, Y') }}</p>
                                        </div>
                                    </div>
                                `);

                        printWindow.document.write(clone.innerHTML);

                        // Signatory Section for Print
                        printWindow.document.write(`
                                    <div class="mt-24 pt-10 border-t border-gray-100 flex justify-between items-start opacity-80">
                                        <div class="text-center">
                                            <div class="w-48 border-b border-gray-900 mb-2 mx-auto"></div>
                                            <p class="text-[9px] font-black uppercase tracking-widest">Verified by Records Office</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest italic mb-2">END OF DOCUMENT</p>
                                        </div>
                                        <div class="text-center">
                                            <div class="w-48 border-b border-gray-900 mb-2 mx-auto"></div>
                                            <p class="text-[9px] font-black uppercase tracking-widest">Institutional Registrar</p>
                                        </div>
                                    </div>
                                `);

                        printWindow.document.write('</body></html>');
                        printWindow.document.close();

                        // Wait for styles/images to load before printing
                        setTimeout(() => {
                            printWindow.print();
                        }, 1200);
                    },

                    exportCSV() {
                        const table = document.querySelector('#report-content table');
                        if (!table) return alert('No tabular dataset found for export.');

                        let csv = [];
                        const rows = table.querySelectorAll('tr');
                        for (let i = 0; i < rows.length; i++) {
                            const cols = rows[i].querySelectorAll('td, th');
                            let row = [];
                            for (let j = 0; j < cols.length; j++) row.push('"' + cols[j].innerText.trim().replace(/"/g, '""') + '"');
                            csv.push(row.join(','));
                        }

                        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.setAttribute('download', `AA_REPORT_${this.currentReportType}_${new Date().getTime()}.csv`);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>