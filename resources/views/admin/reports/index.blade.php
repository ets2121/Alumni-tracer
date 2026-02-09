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
        </div>


        <!-- Report Control Suite -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Master List Reports -->
            <div @click="generateReport('master_list')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-blue-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Master List Reports
                    </h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Detailed registers of alumni by
                        course, employment status, and filters.</p>
                    <div class="flex items-center text-[10px] font-black text-blue-600 uppercase tracking-widest">
                        <span>Launch Registry</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Annual Distribution Reports -->
            <div @click="generateReport('annual_distribution')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden lg:col-span-1">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-indigo-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Annual Distribution
                    </h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Summarized analytical data and
                        trends visualized with charts.</p>
                    <div class="flex items-center text-[10px] font-black text-indigo-600 uppercase tracking-widest">
                        <span>Launch Analytics</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>





            <!-- Detailed Alumni Registry -->
            <div @click="generateReport('detailed_labor')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-brand-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative p-8">
                    <div
                        class="w-14 h-14 bg-brand-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-brand-100 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Alumni Dataset</h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Deep-dive raw data table with
                        advanced filtering and export capabilities.</p>
                    <div class="flex items-center text-[10px] font-black text-brand-600 uppercase tracking-widest">
                        <span>Launch Registry</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Visual Intelligence -->
            <div @click="generateReport('statistical_summary')"
                class="group relative bg-white rounded-[2.5rem] p-1 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden lg:col-span-1">
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
                    <h3 class="text-xl font-black text-gray-900 mb-3 uppercase tracking-tighter">Visual Analytics</h3>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">High-fidelity charts and
                        Institutional Intelligence matrix.</p>
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



        </div>


        <!-- Preview Modal -->
        <div x-show="previewOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center sm:p-4 md:p-10">
            <div @click="previewOpen = false" x-show="previewOpen"
                class="absolute inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity"></div>

            <div x-show="previewOpen"
                class="bg-white w-full h-full md:max-w-[98vw] md:h-[95vh] lg:max-w-[95vw] lg:h-[90vh] md:rounded-[2rem] lg:rounded-[3rem] shadow-2xl relative overflow-hidden flex flex-col animate-in zoom-in duration-300">

                <!-- Enhanced Header Toolbar -->
                <div
                    class="bg-white px-6 sm:px-10 py-5 border-b border-gray-100 shrink-0 flex items-center justify-between">
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
                            <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest mt-0.5">Academic
                                Intelligence Suite</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button @click="printReport()"
                            class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-[0.1em] transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                        <!-- Dropdown Export Menu -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open"
                                class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-[0.1em] transition-all shadow-lg shadow-brand-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export
                                <svg class="w-3 h-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown List -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute right-0 mt-3 w-48 bg-white rounded-[1.5rem] shadow-2xl border border-gray-100 overflow-hidden z-[120] backdrop-blur-xl bg-white/90">
                                <div class="p-2 space-y-1">
                                    <button @click="exportReport('excel'); open = false"
                                        class="w-full text-left px-4 py-3 text-[10px] font-black text-gray-700 uppercase tracking-widest hover:bg-brand-50 hover:text-brand-600 rounded-xl transition-all flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        Export as Excel
                                    </button>
                                    <button @click="exportReport('csv'); open = false"
                                        class="w-full text-left px-4 py-3 text-[10px] font-black text-gray-700 uppercase tracking-widest hover:bg-brand-50 hover:text-brand-600 rounded-xl transition-all flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        Export as CSV
                                    </button>
                                    <div class="h-px bg-gray-50 mx-2 my-1"></div>
                                    <button @click="printReport(); open = false"
                                        class="w-full text-left px-4 py-3 text-[10px] font-black text-gray-700 uppercase tracking-widest hover:bg-brand-50 hover:text-brand-600 rounded-xl transition-all flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </div>
                                        Print / Save PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="h-8 w-px bg-gray-100 mx-2"></div>
                        <button @click="previewOpen = false" class="text-gray-300 hover:text-red-600 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main Content Body (2-Column) -->
                <div class="flex-1 overflow-hidden flex flex-col md:flex-row">

                    <!-- LEFT SIDEBAR: Filters (30%) -->
                    <div
                        class="w-full md:w-72 lg:w-80 bg-gray-50/50 border-r border-gray-100 overflow-y-auto p-6 sm:p-8 flex flex-col gap-8 shrink-0">
                        <div>
                            <h4
                                class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <svg class="w-3 h-3 text-brand-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                Intelligence Filters
                            </h4>

                            <div class="space-y-6">
                                <!-- Global Year Range -->
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-900 uppercase">Year Graduated
                                        Range</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select x-model="reportFilters[currentReportType].fromYear"
                                            @change="generateReport(currentReportType)"
                                            class="bg-white border-gray-100 rounded-xl text-[10px] font-bold h-10 px-3">
                                            <option value="">From (All)</option>
                                            @for($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option> @endfor
                                        </select>
                                        <select x-model="reportFilters[currentReportType].toYear"
                                            @change="generateReport(currentReportType)"
                                            class="bg-white border-gray-100 rounded-xl text-[10px] font-bold h-10 px-3">
                                            <option value="">To (All)</option>
                                            @for($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option> @endfor
                                        </select>
                                    </div>
                                    <template
                                        x-if="reportFilters[currentReportType].fromYear && reportFilters[currentReportType].toYear && parseInt(reportFilters[currentReportType].fromYear) > parseInt(reportFilters[currentReportType].toYear)">
                                        <p class="text-[9px] font-bold text-red-500 mt-1 italic">Range error: "From"
                                            must be <= "To" </p>
                                    </template>
                                </div>

                                <!-- Course Filter -->
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-900 uppercase">Degree Program</label>
                                    <select x-model="reportFilters[currentReportType].courseId"
                                        @change="generateReport(currentReportType)"
                                        class="w-full bg-white border-gray-100 rounded-xl text-[10px] font-bold h-10 px-4">
                                        <option value="">All Programs</option>
                                        @foreach($courses as $course) <option value="{{ $course->id }}">
                                            {{ $course->code }}
                                        </option> @endforeach
                                    </select>
                                </div>

                                <!-- Employment Options (Visible for relevant reports) -->
                                <template
                                    x-if="['master_list', 'detailed_labor', 'statistical_summary'].includes(currentReportType)">
                                    <div class="space-y-6">
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-gray-900 uppercase">Employment
                                                Status</label>
                                            <select x-model="reportFilters[currentReportType].workStatus"
                                                @change="generateReport(currentReportType)"
                                                class="w-full bg-white border-gray-100 rounded-xl text-[10px] font-bold h-10 px-4">
                                                <option value="">All Statuses</option>
                                                <option value="Employed">Employed</option>
                                                <option value="Unemployed">Unemployed</option>
                                                <option value="Ongoing Studies">Ongoing Studies</option>
                                            </select>
                                        </div>
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-gray-900 uppercase">Work
                                                Location</label>
                                            <select x-model="reportFilters[currentReportType].workLocation"
                                                @change="generateReport(currentReportType)"
                                                class="w-full bg-white border-gray-100 rounded-xl text-[10px] font-bold h-10 px-4">
                                                <option value="">Global/Local</option>
                                                <option value="Local">Local</option>
                                                <option value="Overseas">Overseas</option>
                                            </select>
                                        </div>
                                    </div>
                                </template>

                                <!-- Specialized Sub-Type Selectors -->
                                <template x-if="currentReportType === 'master_list'">
                                    <div class="space-y-6 pt-6 border-t border-gray-100">
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-blue-600 uppercase">Registry
                                                Type</label>
                                            <select x-model="reportFilters[currentReportType].subType"
                                                @change="generateReport(currentReportType)"
                                                class="w-full bg-blue-50 border-blue-100 text-blue-700 rounded-xl text-[10px] font-black uppercase h-10 px-4">
                                                <option value="all">Total Alumni List</option>
                                                <option value="unemployed">Unemployed Alumni</option>
                                                <option value="never_employed">Never Employed</option>
                                            </select>
                                        </div>
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-gray-900 uppercase">Search by
                                                Name</label>
                                            <input type="text" x-model="reportFilters[currentReportType].search"
                                                @input.debounce.500ms="generateReport(currentReportType)"
                                                placeholder="Enter name..."
                                                class="w-full bg-white border-gray-100 rounded-xl text-[10px] font-bold h-10 px-4">
                                        </div>
                                    </div>
                                </template>

                                <template x-if="currentReportType === 'annual_distribution'">
                                    <div class="space-y-6 pt-6 border-t border-gray-100">
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-indigo-600 uppercase">Analysis
                                                Matrix</label>
                                            <select x-model="reportFilters[currentReportType].subType"
                                                @change="generateReport(currentReportType)"
                                                class="w-full bg-indigo-50 border-indigo-100 text-indigo-700 rounded-xl text-[10px] font-black uppercase h-10 px-4">
                                                <option value="by_year">By Graduation Year</option>
                                                <option value="by_course">By Course/Program</option>
                                                <option value="employment_by_year">Employment by Year</option>
                                                <option value="employment_by_course">Employment by Course</option>
                                                <option value="location_by_year">Location by Year</option>
                                                <option value="location_by_course">Location by Course</option>
                                            </select>
                                        </div>
                                        <div class="space-y-3">
                                            <label
                                                class="text-[10px] font-black text-purple-600 uppercase">Visualization
                                                Model</label>
                                            <select x-model="reportFilters[currentReportType].chartType"
                                                @change="generateReport(currentReportType)"
                                                class="w-full bg-purple-50 border-purple-100 text-purple-700 rounded-xl text-[10px] font-black uppercase h-10 px-4">
                                                <option value="bar">Bar Chart</option>
                                                <option value="line">Line Chart</option>
                                                <option value="pie">Pie Chart</option>
                                                <option value="donut">Donut Chart</option>
                                            </select>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Sidebar Actions -->
                        <div class="mt-auto space-y-3 pt-6 border-t border-gray-100">
                            <button @click="generateReport(currentReportType)"
                                :disabled="reportFilters[currentReportType].fromYear && reportFilters[currentReportType].toYear && parseInt(reportFilters[currentReportType].fromYear) > parseInt(reportFilters[currentReportType].toYear)"
                                class="w-full py-4 bg-gray-900 text-white rounded-[1.25rem] text-[10px] font-black uppercase tracking-widest hover:bg-brand-600 disabled:opacity-50 disabled:bg-gray-400 transition-all shadow-xl shadow-gray-200 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Update Results
                            </button>
                            <button @click="resetFilters()"
                                class="w-full py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-red-500 transition-colors">Reset
                                all filters</button>
                        </div>
                    </div>

                    <!-- RIGHT CONTENT: Data Display (70%) -->
                    <div class="flex-1 bg-white relative flex flex-col">
                        <!-- Loading Overlay -->
                        <div x-show="loading"
                            class="absolute inset-0 z-[110] bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center">
                            <div class="relative w-24 h-24 mb-6">
                                <div class="absolute inset-0 border-8 border-brand-100 rounded-full"></div>
                                <div
                                    class="absolute inset-0 border-8 border-brand-600 rounded-full border-t-transparent animate-spin">
                                </div>
                            </div>
                            <p class="text-[11px] font-black text-gray-900 uppercase tracking-[0.3em] animate-pulse">
                                Synchronizing Data Matrix...</p>
                        </div>

                        <!-- Scrollable Injected Content -->
                        <div class="flex-1 overflow-y-auto bg-gray-50/30 p-4 sm:p-12">
                            <div id="report-content"
                                class="bg-white p-6 sm:p-12 shadow-2xl rounded-[2rem] sm:rounded-[3.5rem] border border-gray-100 mx-auto max-w-6xl min-h-full relative transition-all duration-500"
                                :class="loading ? 'opacity-50 blur-sm scale-[0.98]' : 'opacity-100 blur-0 scale-100'">
                                <div x-show="!loading" id="injected-report-body">
                                    <!-- AJAX Content will be placed here -->
                                </div>
                            </div>
                        </div>

                        <!-- Content Footer -->
                        <div
                            class="px-10 py-4 bg-white border-t border-gray-100 flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                            <div class="text-gray-400 italic">Report Context: <span class="text-gray-900"
                                    x-text="currentReportTitle"></span></div>
                            <div class="text-brand-600">Secure Node Access • Restricted Access</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-white px-10 py-5 border-t border-gray-100 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest">
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

</x-layouts.admin>