<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h2 class="font-black text-xl text-gray-800 dark:text-dark-text-primary leading-tight tracking-tight">
                    {{ __('GTS Response Preview') }}
                </h2>
            </div>
            <div class="flex gap-3 no-print">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center px-6 py-2.5 bg-gray-100 dark:bg-dark-bg-subtle border border-gray-200 dark:border-dark-border rounded-2xl font-black text-[10px] text-gray-700 dark:text-dark-text-primary uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-dark-bg-elevated transition-all shadow-sm">
                    Back to Feed
                </a>
                <button onclick="window.print()"
                    class="inline-flex items-center px-6 py-2.5 bg-brand-600 border border-transparent rounded-2xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-brand-700 transition-all shadow-lg hover:shadow-brand-500/20 active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Print Record
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-dark-bg-deep" x-data="gtsPreview()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Glass Header Card -->
            <div
                class="bg-white/80 dark:bg-dark-bg-elevated/80 backdrop-blur-xl shadow-2xl rounded-[3rem] border border-white/20 dark:border-dark-border/30 p-8 sm:p-16 relative overflow-hidden">

                <!-- Abstract Background Blobs -->
                <div
                    class="absolute -top-24 -right-24 w-96 h-96 bg-brand-500/5 dark:bg-brand-500/10 rounded-full blur-[100px] pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/5 dark:bg-purple-500/10 rounded-full blur-[100px] pointer-events-none">
                </div>

                <!-- Status Badge -->
                <div class="absolute top-0 right-0 m-12 no-print">
                    <div
                        class="flex items-center gap-2 px-6 py-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-green-100 dark:border-green-900/30">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        Submitted Official Response
                    </div>
                </div>

                <!-- Form Header -->
                <div class="flex flex-col md:flex-row items-center gap-10 mb-16 relative z-10">
                    <div class="flex-shrink-0">
                        <div
                            class="w-32 h-32 bg-white dark:bg-dark-bg-subtle rounded-[2.5rem] shadow-xl flex items-center justify-center border-4 border-gray-50 dark:border-dark-border/50 rotate-3 transition-transform hover:rotate-0">
                            <svg class="w-16 h-16 text-brand-600" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45zM11 15h2v2h-2v-2zm0-6h2v4h-2V9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center md:text-left space-y-3">
                        <p class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em]">CHED Official
                            Record</p>
                        <h1
                            class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tighter leading-none">
                            Graduate Tracer Survey</h1>
                        <p
                            class="text-sm font-medium text-gray-500 dark:text-dark-text-muted italic max-w-lg leading-relaxed">
                            Verified professional record for alumni monitoring and institutional quality assurance
                            programs.
                        </p>
                    </div>
                </div>

                <div class="space-y-16 relative z-10">
                    <!-- SECTION A -->
                    <div class="section-container group"
                        x-intersect="$el.classList.add('animate-in', 'fade-in', 'slide-in-from-bottom-10', 'duration-700')">
                        <div class="section-header">
                            <div class="section-icon bg-brand-100 text-brand-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="section-title">General Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mt-10">
                            <div class="form-group col-span-full">
                                <label class="question-label">Q1. Full Legal Name</label>
                                <div class="preview-value text-lg font-black" x-text="resp.q1_name"></div>
                            </div>
                            <div class="form-group col-span-full">
                                <label class="question-label">Q2. Permanent Mailing Address</label>
                                <div class="preview-value" x-text="resp.q2_address"></div>
                            </div>
                            <div class="form-group">
                                <label class="question-label">Q3. E-mail Address</label>
                                <div class="preview-value font-bold text-brand-600" x-text="resp.q3_email"></div>
                            </div>
                            <div class="form-group">
                                <label class="question-label">Q4. Landline / Reference Contact</label>
                                <div class="preview-value" x-text="resp.q4_tel || 'N/A'"></div>
                            </div>
                            <div class="form-group">
                                <label class="question-label">Q5. Primary Mobile Number</label>
                                <div class="preview-value font-black" x-text="resp.q5_mobile"></div>
                            </div>
                            <div class="form-group">
                                <label class="question-label">Q6. Current Civil Status</label>
                                <div class="preview-value font-bold" x-text="resp.q6_civil_status"></div>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-10 p-6 bg-white dark:bg-dark-bg-subtle/50 rounded-3xl border border-gray-100 dark:border-dark-border">
                            <div class="form-group">
                                <label class="question-label">Q7. Sex</label>
                                <p class="text-sm font-black text-gray-900 dark:text-dark-text-primary mt-1"
                                    x-text="resp.q7_sex"></p>
                            </div>
                            <div class="form-group col-span-2">
                                <label class="question-label">Q8. Birth Date</label>
                                <p class="text-sm font-black text-gray-900 dark:text-dark-text-primary mt-1">
                                    <span x-text="months[resp.q8_month - 1]"></span>
                                    <span x-text="resp.q8_day"></span>,
                                    <span x-text="resp.q8_year"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
                            <div class="form-group">
                                <label class="question-label">Q9. Region</label>
                                <div class="preview-value" x-text="resp.q9_region"></div>
                            </div>
                            <div class="form-group">
                                <label class="question-label">Q10. Province</label>
                                <div class="preview-value" x-text="resp.q10_province"></div>
                            </div>
                            <div class="form-group">
                                <label class="question-label">Q11. Residence</label>
                                <div class="preview-value" x-text="resp.q11_location"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION B -->
                    <div class="section-container"
                        x-intersect="$el.classList.add('animate-in', 'fade-in', 'slide-in-from-bottom-10', 'duration-700')">
                        <div class="section-header">
                            <div class="section-icon bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="section-title">Educational Background</h3>
                        </div>

                        <div class="form-group mt-10">
                            <label class="question-label">Q12. Educational Attainment Details</label>
                            <div
                                class="overflow-x-auto mt-4 rounded-3xl border border-gray-100 dark:border-dark-border">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>Degree(s) earned</th>
                                            <th>Major / Institution</th>
                                            <th>Graduation</th>
                                            <th>Honors</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="row in resp.q12">
                                            <tr x-show="row.degree" class="hover:bg-gray-50/50 transition-colors">
                                                <td class="font-bold text-gray-900 dark:text-dark-text-primary"
                                                    x-text="row.degree"></td>
                                                <td x-text="row.college"></td>
                                                <td class="text-brand-600 font-bold" x-text="row.year"></td>
                                                <td class="italic text-gray-400" x-text="row.honors || 'None'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group mt-16">
                            <label class="question-label">Q13. Professional Licenses / Certifications</label>
                            <div
                                class="overflow-x-auto mt-4 rounded-3xl border border-gray-100 dark:border-dark-border">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>Examination Name</th>
                                            <th>Date Passed</th>
                                            <th>Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="row in resp.q13">
                                            <tr x-show="row.name" class="hover:bg-gray-50/50 transition-colors">
                                                <td class="font-bold text-gray-900 dark:text-dark-text-primary"
                                                    x-text="row.name"></td>
                                                <td x-text="row.date"></td>
                                                <td class="text-green-600 font-black" x-text="row.rating"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group mt-16">
                            <label class="question-label">Q14. Academic Driver (UG = Undergrad, G = Grad)</label>
                            <div
                                class="mt-4 p-8 bg-white dark:bg-dark-bg-subtle/30 rounded-[2.5rem] border border-gray-100 dark:border-dark-border shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="reason in q14_reasons">
                                        <div
                                            class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-dark-bg-elevated rounded-2xl border border-transparent hover:border-brand-200 transition-all group/item">
                                            <span
                                                class="text-xs font-bold text-gray-600 dark:text-dark-text-secondary pr-4"
                                                x-text="reason"></span>
                                            <div class="flex gap-2 shrink-0">
                                                <div x-show="isSelected('q14', 'undergrad', reason)"
                                                    class="w-6 h-6 bg-brand-600 rounded-lg flex items-center justify-center text-[10px] text-white font-black shadow-lg shadow-brand-500/30">
                                                    UG</div>
                                                <div x-show="isSelected('q14', 'grad', reason)"
                                                    class="w-6 h-6 bg-purple-600 rounded-lg flex items-center justify-center text-[10px] text-white font-black shadow-lg shadow-purple-500/30">
                                                    G</div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="resp.q14_others"
                                    class="mt-8 pt-8 border-t border-gray-100 border-dashed italic text-sm text-gray-400">
                                    Additional Reasons / Notes: <span
                                        class="text-gray-900 dark:text-dark-text-primary font-bold ml-2"
                                        x-text="resp.q14_others"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION D -->
                    <div class="section-container"
                        x-intersect="$el.classList.add('animate-in', 'fade-in', 'slide-in-from-bottom-10', 'duration-700')">
                        <div class="section-header">
                            <div class="section-icon bg-amber-100 text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="section-title">Employment Record</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-12 mt-10">
                            <!-- Global Status Card -->
                            <div
                                class="relative p-10 bg-gradient-to-br from-brand-600 to-brand-700 rounded-[3rem] text-white shadow-2xl shadow-brand-500/20 overflow-hidden group">
                                <div
                                    class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-1000">
                                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M20 7h-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H4a2 2 0 00-2 2v11a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM10 5h4v2h-4V5zm10 15H4V9h16v11z" />
                                    </svg>
                                </div>
                                <div class="relative z-10">
                                    <p class="text-[10px] font-black uppercase tracking-[0.4em] opacity-60">Q16. Current
                                        Status</p>
                                    <h4 class="text-5xl font-black mt-2 leading-none"
                                        x-text="resp.q16_employed === 'Yes' ? 'Actively Employed' : 'Not Employed'">
                                    </h4>
                                    <p class="text-sm font-medium mt-4 opacity-80"
                                        x-text="resp.q16_employed === 'Yes' ? 'Professional career path is currently active and documented.' : 'Alumni is currently in transition or seeking employment opportunities.'">
                                    </p>
                                </div>
                            </div>

                            <!-- Conditional: Unemployed Details -->
                            <div x-show="resp.q16_employed !== 'Yes'"
                                class="p-10 bg-amber-50/50 dark:bg-amber-900/10 rounded-[3rem] border border-amber-100 dark:border-amber-900/30">
                                <label class="question-label text-amber-600">Q17. Documented Reasons for
                                    Non-Employment</label>
                                <div class="flex flex-wrap gap-3 mt-6">
                                    <template x-for="(val, key) in resp.q17_reasons">
                                        <div class="px-6 py-4 bg-white dark:bg-dark-bg-elevated rounded-2xl shadow-sm border border-amber-200/50 text-sm font-black text-amber-700 dark:text-amber-400"
                                            x-text="key"></div>
                                    </template>
                                    <div x-show="resp.q17_others"
                                        class="px-6 py-4 bg-white/50 dark:bg-dark-bg-elevated/50 rounded-2xl border border-amber-200 border-dashed text-sm italic text-amber-600 dark:text-amber-500"
                                        x-text="'Others: ' + resp.q17_others"></div>
                                </div>
                            </div>

                            <!-- Conditional: Employed Details -->
                            <div x-show="resp.q16_employed === 'Yes'" class="space-y-12">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div class="form-group glass-input flex flex-col justify-center">
                                        <label class="question-label">Q18. Regularity of Work</label>
                                        <div class="text-xl font-black text-gray-900 dark:text-dark-text-primary"
                                            x-text="resp.q18_status"></div>
                                        <div x-show="resp.q18_status === 'Self-employed'"
                                            class="mt-4 p-4 bg-brand-50/30 dark:bg-brand-900/10 rounded-2xl border border-brand-100">
                                            <p class="text-[9px] font-black uppercase text-brand-400">Applications</p>
                                            <p class="text-sm font-bold text-brand-700" x-text="resp.q18_skills"></p>
                                        </div>
                                    </div>
                                    <div class="form-group glass-input flex flex-col justify-center">
                                        <label class="question-label">Q19. Professional Designation</label>
                                        <div class="text-xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tight"
                                            x-text="resp.q19_occupation"></div>
                                    </div>
                                    <div class="form-group col-span-full glass-input">
                                        <label class="question-label">Q20. Business Sector / Industry</label>
                                        <div class="text-lg font-black text-gray-800 dark:text-dark-text-secondary"
                                            x-text="resp.q20_business_line"></div>
                                    </div>
                                    <div class="form-group glass-input">
                                        <label class="question-label">Q21. Operational Base</label>
                                        <div class="text-lg font-black" x-text="resp.q21_place"></div>
                                    </div>
                                    <div class="form-group glass-input">
                                        <label class="question-label">Q22. Career Initiation</label>
                                        <div class="text-lg font-black text-brand-600"
                                            x-text="resp.q22_first_job === 'Yes' ? 'First Professional Role' : 'Subsequent Career Move'">
                                        </div>
                                    </div>
                                </div>

                                <!-- Deep Insights: Career Alignment -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div
                                        class="p-8 bg-brand-50/20 dark:bg-brand-900/5 rounded-[3rem] border border-brand-100/30 shadow-sm transition-all hover:shadow-xl hover:shadow-brand-500/5">
                                        <label class="question-label text-brand-600">Q24. Curriculum Alignment</label>
                                        <div class="flex items-center gap-4 mt-4">
                                            <div :class="resp.q24_related === 'Yes' ? 'bg-green-500 shadow-green-500/20' : 'bg-gray-400 shadow-gray-500/20'"
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-xl">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M9 12l2 2 4-4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-2xl font-black"
                                                    x-text="resp.q24_related === 'Yes' ? 'Perfect Match' : 'Non-Direct'">
                                                </p>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                                    Job-Course Relevance</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="p-8 bg-purple-50/20 dark:bg-purple-900/5 rounded-[3rem] border border-purple-100/30 shadow-sm transition-all hover:shadow-xl hover:shadow-purple-500/5">
                                        <label class="question-label text-purple-600">Q31. Economic Impact</label>
                                        <div class="flex items-center gap-4 mt-4">
                                            <div
                                                class="w-12 h-12 bg-purple-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-purple-500/20">
                                                <span class="text-lg font-black">P</span>
                                            </div>
                                            <div>
                                                <p class="text-2xl font-black text-purple-600" x-text="resp.q31_salary">
                                                </p>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                                    Avg. Monthly Revenue</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="form-group glass-input bg-indigo-50/30 dark:bg-indigo-900/10 p-10 rounded-[3rem] border-indigo-100/30">
                                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                                        <div class="flex-1 text-center sm:text-left">
                                            <label class="question-label text-indigo-600">Q32. Curricular
                                                Efficacy</label>
                                            <h4 class="text-2xl font-black text-indigo-700 dark:text-indigo-400 mt-2"
                                                x-text="resp.q32_relevant === 'Yes' ? 'Skills were highly relevant' : 'Incomplete skill alignment'">
                                            </h4>
                                        </div>
                                        <div x-show="resp.q32_relevant === 'Yes'"
                                            class="flex-shrink-0 flex flex-wrap gap-2 justify-center max-w-sm">
                                            <template x-for="(val, key) in resp.q33_skills">
                                                <span
                                                    class="px-3 py-1 bg-white dark:bg-dark-bg-elevated rounded-xl border border-indigo-200 text-[10px] font-black uppercase text-indigo-600"
                                                    x-text="key"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Suggestions -->
                        <div
                            class="mt-16 p-10 bg-white dark:bg-dark-bg-subtle/50 rounded-[3rem] border border-gray-100 dark:border-dark-border shadow-inner">
                            <label class="question-label mb-6">Q34. Suggestions for Institutional Growth</label>
                            <div class="text-sm font-medium leading-relaxed text-gray-700 dark:text-dark-text-muted bg-gray-50/50 dark:bg-dark-bg-deep p-8 rounded-[2rem] border border-gray-100 dark:border-dark-border border-dashed"
                                x-text="resp.q34_suggestions || 'No documented suggestions for this submission.'"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action -->
                <div
                    class="mt-20 pt-12 border-t border-gray-100 dark:border-dark-border flex justify-center no-print relative z-10">
                    <a href="{{ route('dashboard') }}" class="modern-submit-btn">
                        Finish Review
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Referral floating Section -->
            <div
                class="mt-8 no-print p-10 bg-white dark:bg-dark-bg-elevated rounded-[3rem] shadow-xl border border-gray-100 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-8">
                    <span
                        class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 font-black text-xs">R</span>
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-gray-400">Professional Referrals</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="row in resp.ref">
                        <div x-show="row.name"
                            class="p-6 bg-gray-50 dark:bg-dark-bg-subtle rounded-2xl border border-transparent hover:border-gray-200 transition-all">
                            <p class="text-sm font-black text-gray-900 dark:text-dark-text-primary uppercase truncate"
                                x-text="row.name"></p>
                            <p class="text-[10px] text-gray-400 mt-1 truncate" x-text="row.address"></p>
                            <p class="text-[10px] font-black text-brand-600 mt-2" x-text="row.contact"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .section-container {
            @apply p-0 relative;
        }

        .section-header {
            @apply flex items-center gap-4 mb-8;
        }

        .section-icon {
            @apply w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform hover:scale-110;
        }

        .section-title {
            @apply text-lg font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-[0.2em];
        }

        .question-label {
            @apply block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2;
        }

        .preview-value {
            @apply text-gray-800 dark:text-dark-text-primary tracking-tight leading-none;
        }

        .modern-table {
            @apply w-full border-collapse;
        }

        .modern-table th {
            @apply bg-gray-50 dark:bg-dark-bg-subtle p-6 text-left text-[9px] font-black uppercase text-gray-400 border-b border-gray-100 dark:border-dark-border;
        }

        .modern-table td {
            @apply p-6 border-b border-gray-50 dark:border-dark-border text-xs font-semibold text-gray-600 dark:text-dark-text-secondary;
        }

        .glass-input {
            @apply p-8 bg-white dark:bg-dark-bg-subtle/30 rounded-[2.5rem] border border-gray-100 dark:border-dark-border transition-all hover:shadow-xl hover:shadow-gray-200/20;
        }

        .modern-submit-btn {
            @apply inline-flex items-center px-12 py-5 bg-gray-900 dark:bg-brand-600 rounded-[2rem] font-black text-xs text-white uppercase tracking-widest hover:scale-105 transition-all shadow-2xl active:scale-95;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
                font-family: serif !important;
            }

            .py-12 {
                padding: 0 !important;
            }

            .bg-white\/80 {
                background: white !important;
                box-shadow: none !important;
                border: 1px solid #eee !important;
                padding: 40px !important;
                border-radius: 0 !important;
            }

            .section-container {
                page-break-inside: avoid;
                padding: 20px 0 !important;
            }

            .text-[10px],
            .text-xs {
                font-size: 8pt !important;
            }

            .text-sm {
                font-size: 10pt !important;
            }

            .text-xl,
            .text-2xl {
                font-size: 14pt !important;
            }

            .text-4xl,
            .text-5xl {
                font-size: 18pt !important;
            }

            .modern-table th {
                background: #f9fafb !important;
                color: #666 !important;
            }
        }
    </style>

    <script>
        function gtsPreview() {
            return {
                resp: @json($response),
                months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                q14_reasons: [
                    'High grades in the course or subject area(s) related to the course', 'Good grades in high school',
                    'Influence of parents or relatives', 'Peer Influence', 'Inspired by a role model',
                    'Strong passion for the profession', 'Prospect for immediate employment',
                    'Status or prestige of the profession', 'Availability of course offering in chosen institution',
                    'Prospect of career advancement', 'Affordable for the family', 'Prospect of attractive compensation',
                    'Opportunity for employment abroad', 'No particular choice or no better idea'
                ],
                isSelected(q, group, reason) {
                    if (!this.resp[q] || !this.resp[q][group]) return false;
                    return this.resp[q][group][reason] === 'on';
                }
            }
        }
    </script>
</x-app-layout>