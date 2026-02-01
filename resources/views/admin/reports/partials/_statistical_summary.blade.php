<div class="space-y-8" x-data="{ subTab: 'all' }">
    <!-- Visual Intelligence Controls -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4 no-print">
        <div class="flex bg-gray-100 p-1 rounded-2xl w-full md:w-auto">
            <button @click="subTab = 'all'"
                :class="subTab === 'all' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Combined
                Matrix</button>
            <button @click="subTab = 'program'"
                :class="subTab === 'program' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Program
                Insights</button>
            <button @click="subTab = 'labor'"
                :class="subTab === 'labor' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Labor
                Analytics</button>
            <button @click="subTab = 'demographics'"
                :class="subTab === 'demographics' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Demographics</button>
            <button @click="subTab = 'tracer'"
                :class="subTab === 'tracer' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Tracer Results</button>
        </div>
        <div class="text-[9px] font-bold text-gray-400 uppercase hidden lg:block italic">💡 Click a tab to focus on
            specific datasets • Report Date: {{ date('M d, Y') }}</div>
    </div>

    <!-- COMBINED MATRIX VIEW -->
    <div x-show="subTab === 'all'" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Top Row Demos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Course Dist (Doughnut) -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Programs</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartByCourse" data-labels="{{ json_encode($data['by_course']->pluck('code')) }}"
                        data-values="{{ json_encode($data['by_course']->pluck('alumni_count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Bar Chart Comparison</div>
            </div>

            <!-- Employment (Pie) -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Labor Status</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartByEmployment"
                        data-labels="{{ json_encode($data['by_employment']->pluck('employment_status')) }}"
                        data-values="{{ json_encode($data['by_employment']->pluck('count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Current Employment</div>
            </div>

            <!-- Gender (Polar Area) -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Gender Profile</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartByGender" data-labels="{{ json_encode($data['by_gender']->pluck('gender')) }}"
                        data-values="{{ json_encode($data['by_gender']->pluck('count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Demographic Identity</div>
            </div>

            <!-- Civil Status (Bar) -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Stability Focus</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartStabilityStacked"
                        data-labels="{{ json_encode($data['stability_matrix']->pluck('program')) }}"
                        data-permanent="{{ json_encode($data['stability_matrix']->pluck('Permanent')) }}"
                        data-contractual="{{ json_encode($data['stability_matrix']->pluck('Contractual')) }}"
                        data-jo="{{ json_encode($data['stability_matrix']->pluck('Job Order')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Stacked Status Matrix</div>
            </div>
        </div>

        <!-- Momentum Trend (Combination Chart) -->
        <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-2xl">
            <h4 class="text-[9px] font-black text-brand-400 uppercase tracking-widest mb-6 px-2">Overall Employment Flux (Combination)</h4>
            <div class="h-48 w-full">
                <canvas id="chartCombinationSummary"
                    data-labels="{{ json_encode($data['combination_data']->pluck('month')) }}"
                    data-total="{{ json_encode($data['combination_data']->pluck('total_registrants')) }}"
                    data-employed="{{ json_encode($data['combination_data']->pluck('employed_count')) }}"></canvas>
            </div>
        </div>
    </div>

    <!-- PROGRAM INSIGHTS FOCUS -->
    <div x-show="subTab === 'program'" class="animate-in fade-in duration-500">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
                <h4 class="text-sm font-black text-gray-900 mb-6 uppercase tracking-tight">Full Program Enrollment
                    Ranking</h4>
                <div class="h-80 w-full">
                    <canvas id="chartProgramFocus" data-labels="{{ json_encode($data['by_course']->pluck('code')) }}"
                        data-values="{{ json_encode($data['by_course']->pluck('alumni_count')) }}"></canvas>
                </div>
            </div>
            <div class="space-y-4">
                <div class="bg-brand-600 p-6 rounded-[2rem] text-white">
                    <p class="text-[9px] font-bold uppercase tracking-widest opacity-80 mb-2">Most Popular Course</p>
                    <h5 class="text-xl font-black uppercase leading-tight">{{ $data['by_course']->first()->name }}</h5>
                </div>
                <!-- Batch Growth Summary -->
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h5 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Batch Volumetrics
                    </h5>
                    <div class="space-y-3">
                        @foreach($data['by_batch'] as $batch)
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-gray-500">Class of {{ $batch->batch_year }}</span>
                                <span class="font-black text-gray-900">{{ $batch->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LABOR ANALYTICS FOCUS -->
    <div x-show="subTab === 'labor'" class="animate-in fade-in duration-500">
        <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 mb-8">
            <h4 class="text-sm font-black text-gray-900 mb-6 uppercase tracking-tight">Employment Status Concentration
            </h4>
            <div class="h-64 w-full max-w-2xl mx-auto">
                <canvas id="chartLaborFocus"
                    data-labels="{{ json_encode($data['by_employment']->pluck('employment_status')) }}"
                    data-values="{{ json_encode($data['by_employment']->pluck('count')) }}"></canvas>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-green-50 p-6 rounded-[2rem] border border-green-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-green-500 text-white rounded-lg"><svg class="w-4 h-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg></div>
                    <span
                        class="text-xs font-black text-green-700 uppercase tracking-widest underline decoration-2 underline-offset-4">Employment
                        Health</span>
                </div>
                @php 
                                        $total = $data['by_employment']->sum('count');
                    $employed = $data['by_employment']->where('employment_status', 'Employed')->first()->count ?? 0;
                    $rate = $total > 0 ? round(($employed / $total) * 100, 1) : 0;
                @endphp
               <p class="text-4xl font-black text-green-700">{{ $rate }}%</p>
                <p class="text-[10px] font-bold text-green-600/70 uppercase mt-1">Institutional Employment Rate</p>
            </div>

            <!-- Professional Focus (New) -->
            <div class="bg-indigo-50 p-8 rounded-[2.5rem] border border-indigo-100 md:col-span-1">
                <h4 class="text-xs font-black text-indigo-900 mb-6 uppercase tracking-tight">Location Matrix (Grouped)</h4>
                <div class="h-48 w-full">
                    <canvas id="chartLocationGrouped"
                        data-labels="{{ json_encode($data['location_matrix']->pluck('program')) }}"
                        data-local="{{ json_encode($data['location_matrix']->pluck('Local')) }}"
                        data-overseas="{{ json_encode($data['location_matrix']->pluck('Overseas')) }}"></canvas>
                </div>
            </div>

            <div class="bg-purple-50 p-8 rounded-[2.5rem] border border-purple-100 md:col-span-1">
                <h4 class="text-xs font-black text-purple-900 mb-6 uppercase tracking-tight">Sector & Location Focus</h4>
                <div class="grid grid-cols-2 gap-4 h-full">
                    <div class="h-32">
                        <canvas id="chartByEstablishmentPie"
                            data-labels="{{ json_encode($data['by_establishment']->pluck('establishment_type')) }}"
                            data-values="{{ json_encode($data['by_establishment']->pluck('count')) }}"></canvas>
                    </div>
                    <div class="h-32">
                        <canvas id="chartByWorkLocationDonut"
                            data-labels="{{ json_encode($data['by_work_location']->pluck('work_location')) }}"
                            data-values="{{ json_encode($data['by_work_location']->pluck('count')) }}"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Industries Detailed List (Proposed Top 20 Entries) -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm md:col-span-2">
                <h4 class="text-xs font-black text-gray-900 mb-6 uppercase tracking-tight">Dominant Industries (Top 10)</h4>
                <div class="h-64 w-full">
                    <canvas id="chartTopFieldsFocus"
                        data-labels="{{ json_encode($data['top_fields']->pluck('field_of_work')) }}"
                        data-values="{{ json_encode($data['top_fields']->pluck('count')) }}"></canvas>
                </div>
            </div>
             
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-6">
                <div class="flex-1">
                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Social Mobility Index</h5>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed italic">Analysis of career progression and professional transitions within the alumni community based on active registry data.</p>
                 </div>
                 <div class="p-4 bg-gray-50 rounded-2xl"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg></div>
            </div>
        </div>

        <!-- Professional Data Row (New) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Work Status -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Work Status</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartByWorkStatus"
                        data-labels="{{ json_encode($data['by_work_status']->pluck('work_status')) }}"
                        data-values="{{ json_encode($data['by_work_status']->pluck('count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Contractual Mix</div>
            </div>

            <!-- Establishment Type -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Sector Distribution</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartByEstablishment"
                        data-labels="{{ json_encode($data['by_establishment']->pluck('establishment_type')) }}"
                        data-values="{{ json_encode($data['by_establishment']->pluck('count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Public vs Private</div>
            </div>

            <!-- Work Location -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Geographic Scope</span>
                <div class="h-32 w-32 relative">
                    <canvas id="chartByWorkLocation"
                        data-labels="{{ json_encode($data['by_work_location']->pluck('work_location')) }}"
                        data-values="{{ json_encode($data['by_work_location']->pluck('count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Local vs Overseas</div>
            </div>

            <!-- Top Industries -->
            <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Industry Pulse</span>
                <div class="h-32 w-full relative">
                    <canvas id="chartTopFieldsMatrix"
                        data-labels="{{ json_encode($data['top_fields']->pluck('field_of_work')) }}"
                        data-values="{{ json_encode($data['top_fields']->pluck('count')) }}"></canvas>
                </div>
                <div class="mt-4 text-[8px] font-bold text-gray-400 uppercase">Field Concentration</div>
            </div>
        </div>
    </div>

    <!-- DEMOGRAPHICS FOCUS -->

                       <div x-show="subTab === 'demographics'" class="animate-in fade-in duration-500">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                                   <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
                <h4 class="text-xs font-black text-gray-400 mb-6 uppercase tracking-widest px-2">Gender Demographics (Enhanced)</h4>
                <div class="h-[300px] w-full relative">
                    <canvas id="chartGenderFocus" data-labels="{{ json_encode($data['by_gender']->pluck('gender')) }}" data-values="{{ json_encode($data['by_gender']->pluck('count')) }}"></canvas>
                </div>
            </div>

                       
                                   <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
                <h4 class="text-xs font-black text-gray-400 mb-6 uppercase tracking-widest px-2">Civil Status Architecture</h4>
                <div class="h-[300px] w-full relative">
                    <canvas id="chartCivilFocus" data-labels="{{ json_encode($data['by_civil_status']->pluck('civil_status')) }}" data-values="{{ json_encode($data['by_civil_status']->pluck('count')) }}"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- TRACER RESULTS FOCUS -->
    <div x-show="subTab === 'tracer'" class="animate-in fade-in duration-500">
        <div class="bg-white p-12 rounded-[3rem] border border-gray-100 shadow-xl text-center">
            <div class="w-20 h-20 bg-brand-50 text-brand-600 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-4 uppercase tracking-tighter">Graduate Tracer Engine</h3>
            <p class="text-gray-500 max-w-xl mx-auto mb-10 leading-relaxed font-medium">Capture Likert-scale sentiment and multiple-choice career path analysis. CHED-compliant reporting for institutional evaluation.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
                    <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-6">Likert Evaluation (Rating)</h4>
                    <div class="h-48 w-full">
                        <canvas id="chartTracerLikert" data-labels='["Very Satisfied", "Satisfied", "Neutral", "Unsatisfied", "Very Unsatisfied"]' data-values='[45, 30, 15, 7, 3]'></canvas>
                    </div>
                </div>
                <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
                    <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-6">Response Analytics (Bar)</h4>
                    <div class="h-48 w-full">
                        <canvas id="chartTracerMultiple" data-labels='["Curriculum", "Facilities", "Placement", "Services"]' data-values='[80, 65, 90, 75]'></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>