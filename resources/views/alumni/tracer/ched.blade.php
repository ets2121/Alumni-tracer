<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('GRADUATE TRACER SURVEY (GTS)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-dark-bg-deep" x-data="gtsForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-dark-bg-elevated shadow-xl rounded-none border-t-8 border-brand-600 p-8 sm:p-12">

                <!-- Form Header -->
                <div
                    class="flex flex-col md:flex-row items-center gap-6 mb-10 pb-8 border-b border-gray-200 dark:border-dark-border">
                    <div class="flex-shrink-0">
                        <!-- CHED Logo Placeholder -->
                        <div
                            class="w-24 h-24 bg-gray-100 dark:bg-dark-bg-subtle rounded-full flex items-center justify-center border-2 border-gray-200 dark:border-dark-border">
                            <span
                                class="text-[10px] font-bold text-gray-400 text-center uppercase tracking-tighter">CHED<br>LOGO</span>
                        </div>
                    </div>
                    <div class="text-center md:text-left">
                        <h1
                            class="text-2xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tight mb-2">
                            GRADUATE TRACER SURVEY (GTS)</h1>
                        <p class="text-sm leading-relaxed text-gray-700 dark:text-dark-text-muted italic">
                            "Dear Graduate: Good day! Please complete this GTS questionnaire as accurately & frankly as
                            possible by checking ( ) the box corresponding to your response. Your answer will be used
                            for research purposes in order to assess graduate employability and eventually, improve
                            course offerings of your alma mater & other universities/colleges in the Philippines. Your
                            answers to this survey will be treated with strictest confidentiality."
                        </p>
                    </div>
                </div>

                <form action="{{ route('ched-gts.store') }}" method="POST" class="space-y-12">
                    @csrf

                    <!-- SECTION A: GENERAL INFORMATION -->
                    <div class="section-container">
                        <h3 class="section-title">SECTION A: GENERAL INFORMATION</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                            <!-- Q1 -->
                            <div class="form-group">
                                <label class="question-label">Q1. Name <span class="required-star">*</span></label>
                                <input type="text" name="q1_name" value="{{ $user->name }}" required class="form-input"
                                    placeholder="Enter your full name">
                            </div>

                            <!-- Q2 -->
                            <div class="form-group">
                                <label class="question-label">Q2. Permanent Address <span
                                        class="required-star">*</span></label>
                                <input type="text" name="q2_address" required class="form-input"
                                    placeholder="Number, Street, Barangay, City/Municipality">
                            </div>

                            <!-- Q3 -->
                            <div class="form-group">
                                <label class="question-label">Q3. E-mail Address <span
                                        class="required-star">*</span></label>
                                <input type="email" name="q3_email" value="{{ $user->email }}" required
                                    class="form-input" placeholder="example@email.com">
                            </div>

                            <!-- Q4 -->
                            <div class="form-group">
                                <label class="question-label">Q4. Telephone or Contact Number(s)</label>
                                <input type="tel" name="q4_tel" class="form-input"
                                    placeholder="Landline or secondary number"
                                    @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                            </div>

                            <!-- Q5 -->
                            <div class="form-group">
                                <label class="question-label">Q5. Mobile Number <span
                                        class="required-star">*</span></label>
                                <input type="tel" name="q5_mobile" required class="form-input"
                                    placeholder="09XXXXXXXXX">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                            <!-- Q6 -->
                            <div class="form-group">
                                <label class="question-label">Q6. Civil Status <span
                                        class="required-star">*</span></label>
                                <div class="radio-group">
                                    <template
                                        x-for="status in ['Single', 'Married', 'Separated', 'Widow or Widower', 'Single Parent']">
                                        <label class="radio-item">
                                            <input type="radio" name="q6_civil_status" :value="status" required
                                                class="form-radio">
                                            <span x-text="status"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Q7 -->
                            <div class="form-group">
                                <label class="question-label">Q7. Sex <span class="required-star">*</span></label>
                                <div class="radio-group flex-row gap-8">
                                    <label class="radio-item">
                                        <input type="radio" name="q7_sex" value="Male" required class="form-radio">
                                        <span>Male</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="q7_sex" value="Female" required class="form-radio">
                                        <span>Female</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Q8 Birthday -->
                        <div class="form-group mt-8">
                            <label class="question-label">Q8. Birthday <span class="required-star">*</span></label>
                            <div class="grid grid-cols-3 gap-4 max-w-md mt-2">
                                <select name="q8_month" required class="form-input">
                                    <option value="">Month</option>
                                    <template x-for="(month, index) in months">
                                        <option :value="index + 1" x-text="month"></option>
                                    </template>
                                </select>
                                <select name="q8_day" required class="form-input">
                                    <option value="">Day</option>
                                    <template x-for="i in 31">
                                        <option :value="i" x-text="i"></option>
                                    </template>
                                </select>
                                <select name="q8_year" required class="form-input">
                                    <option value="">Year</option>
                                    <template x-for="year in years">
                                        <option :value="year" x-text="year"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Q9 Region of Origin -->
                        <div class="form-group mt-8">
                            <label class="question-label">Q9. Region of Origin <span
                                    class="required-star">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3">
                                <template x-for="region in regions">
                                    <label class="radio-item">
                                        <input type="radio" name="q9_region" :value="region" required
                                            class="form-radio">
                                        <span x-text="region"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                            <!-- Q10 -->
                            <div class="form-group">
                                <label class="question-label">Q10. Province <span class="required-star">*</span></label>
                                <input type="text" name="q10_province" required class="form-input"
                                    placeholder="Enter your province">
                            </div>

                            <!-- Q11 -->
                            <div class="form-group">
                                <label class="question-label">Q11. Location of Residence <span
                                        class="required-star">*</span></label>
                                <div class="radio-group flex-row gap-8">
                                    <label class="radio-item">
                                        <input type="radio" name="q11_location" value="City" required
                                            class="form-radio">
                                        <span>City</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="q11_location" value="Municipality" required
                                            class="form-radio">
                                        <span>Municipality</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION B: EDUCATIONAL BACKGROUND -->
                    <div class="section-container">
                        <h3 class="section-title">SECTION B: EDUCATIONAL BACKGROUND</h3>

                        <!-- Q12 -->
                        <div class="form-group mt-6">
                            <label class="question-label">Q12. Educational Attainment (Baccalaureate Degree only) <span
                                    class="required-star">*</span></label>
                            <div class="mt-4 overflow-x-auto">
                                <table class="dynamic-table">
                                    <thead>
                                        <tr>
                                            <th>Degree(s) & Specialization(s)</th>
                                            <th>College or University</th>
                                            <th>Year Graduated</th>
                                            <th>Honor(s) or Award(s) Received</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, index) in q12_rows" :key="index">
                                            <tr>
                                                <td><input type="text" :name="'q12['+index+'][degree]'"
                                                        :required="index === 0" class="table-input"
                                                        placeholder="e.g. BS Information Technology"></td>
                                                <td><input type="text" :name="'q12['+index+'][college]'"
                                                        :required="index === 0" class="table-input"
                                                        placeholder="Enter Institution"></td>
                                                <td><input type="number" :name="'q12['+index+'][year]'"
                                                        :required="index === 0" min="1900" max="2100"
                                                        class="table-input" placeholder="YYYY"></td>
                                                <td><input type="text" :name="'q12['+index+'][honors]'"
                                                        class="table-input" placeholder="N/A"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td>
                                                    <button type="button" @click="removeRow('q12_rows', index)"
                                                        x-show="q12_rows.length > 1"
                                                        class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <button type="button" @click="addRow('q12_rows')" class="add-row-btn mt-4">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Degree
                                </button>
                            </div>
                        </div>

                        <!-- Q13 -->
                        <div class="form-group mt-12">
                            <label class="question-label">Q13. Professional Examination(s) Passed</label>
                            <div class="mt-4 overflow-x-auto">
                                <table class="dynamic-table">
                                    <thead>
                                        <tr>
                                            <th>Name of Examination</th>
                                            <th>Date Taken</th>
                                            <th>Rating</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, index) in q13_rows" :key="index">
                                            <tr>
                                                <td><input type="text" :name="'q13['+index+'][name]'"
                                                        class="table-input" placeholder="e.g. Licensure Exam"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td><input type="text" :name="'q13['+index+'][date]'"
                                                        class="table-input" placeholder="MM/DD/YYYY"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td><input type="text" :name="'q13['+index+'][rating]'"
                                                        class="table-input" placeholder="Rating %"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td>
                                                    <button type="button" @click="removeRow('q13_rows', index)"
                                                        x-show="q13_rows.length > 1"
                                                        class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <button type="button" @click="addRow('q13_rows')"
                                    class="add-row-btn mt-4 text-brand-600 bg-brand-50 hover:bg-brand-100">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Examination
                                </button>
                            </div>
                        </div>

                        <!-- Q14 Checkbox Matrix -->
                        <div class="form-group mt-12">
                            <label class="question-label">Q14. Reason(s) for taking the course(s) or pursuing degree(s).
                                You may check ( ) more than one answer.</label>
                            <div class="mt-6 overflow-x-auto">
                                <table class="matrix-table">
                                    <thead class="bg-gray-50 dark:bg-dark-bg-subtle">
                                        <tr>
                                            <th class="text-left w-1/2 p-4">Reason(s)</th>
                                            <th class="text-center p-4">Undergraduate/AB/BS</th>
                                            <th class="text-center p-4">Graduate/MS/MA/PhD</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                        <template x-for="reason in q14_reasons" :key="reason">
                                            <tr
                                                class="hover:bg-gray-50 dark:hover:bg-dark-bg-subtle/50 transition-colors">
                                                <td class="p-4 text-sm font-medium text-gray-700 dark:text-dark-text-secondary"
                                                    x-text="reason"></td>
                                                <td class="p-4 text-center">
                                                    <input type="checkbox" :name="'q14[undergrad]['+reason+']'"
                                                        class="form-checkbox h-5 w-5 rounded text-brand-600 focus:ring-brand-500 dark:bg-dark-bg-subtle dark:border-dark-border">
                                                </td>
                                                <td class="p-4 text-center">
                                                    <input type="checkbox" :name="'q14[grad]['+reason+']'"
                                                        class="form-checkbox h-5 w-5 rounded text-brand-600 focus:ring-brand-500 dark:bg-dark-bg-subtle dark:border-dark-border">
                                                </td>
                                            </tr>
                                        </template>
                                        <tr class="bg-gray-50/50 dark:bg-dark-bg-subtle/20">
                                            <td class="p-4">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="text-sm font-medium text-gray-700 dark:text-dark-text-secondary">Others,
                                                        please specify:</span>
                                                    <input type="text" name="q14_others"
                                                        class="table-input !w-auto !inline-flex !h-8 border-b border-t-0 border-l-0 border-r-0 rounded-none focus:ring-0"
                                                        placeholder="Please specify or N/A"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </div>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION C: TRAINING(S)/ADVANCE STUDIES -->
                    <div class="section-container">
                        <h3 class="section-title">SECTION C: TRAINING(S)/ADVANCE STUDIES ATTENDED AFTER COLLEGE</h3>

                        <!-- Q15a -->
                        <div class="form-group mt-6">
                            <label class="question-label">Q15a. Please list down all professional or work-related
                                training program(s) including advance studies you have attended after college.</label>
                            <div class="mt-4 overflow-x-auto">
                                <table class="dynamic-table border-indigo-200">
                                    <thead class="bg-indigo-50 dark:bg-indigo-900/10">
                                        <tr>
                                            <th>Title of Training or Advance Study</th>
                                            <th>Duration and Credits Earned</th>
                                            <th>Name of Training Institution/College/University</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, index) in q15a_rows" :key="index">
                                            <tr>
                                                <td><input type="text" :name="'q15a['+index+'][title]'"
                                                        class="table-input" placeholder="Title of training"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td><input type="text" :name="'q15a['+index+'][duration]'"
                                                        class="table-input" placeholder="e.g. 40 hours"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td><input type="text" :name="'q15a['+index+'][institution]'"
                                                        class="table-input" placeholder="e.g. TESDA"
                                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                                </td>
                                                <td>
                                                    <button type="button" @click="removeRow('q15a_rows', index)"
                                                        x-show="q15a_rows.length > 1"
                                                        class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <button type="button" @click="addRow('q15a_rows')"
                                    class="add-row-btn mt-4 text-indigo-600 bg-indigo-50 hover:bg-indigo-100">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Training Record
                                </button>
                            </div>
                        </div>

                        <!-- Q15b -->
                        <div class="form-group mt-12">
                            <label class="question-label">Q15b. What made you pursue advance studies?</label>
                            <div class="radio-group mt-3">
                                <label class="radio-item">
                                    <input type="radio" name="q15b_reason" value="For promotion" class="form-radio">
                                    <span>For promotion</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="q15b_reason" value="For professional development"
                                        class="form-radio">
                                    <span>For professional development</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <label class="radio-item">
                                        <input type="radio" name="q15b_reason" value="Others" class="form-radio">
                                        <span>Others, please specify:</span>
                                    </label>
                                    <input type="text" name="q15b_others"
                                        class="table-input !w-auto !inline-flex !h-8 border-b border-t-0 border-l-0 border-r-0 rounded-none focus:ring-0"
                                        placeholder="Please specify or N/A"
                                        @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION D: EMPLOYMENT DATA -->
                    <div class="section-container">
                        <h3 class="section-title">SECTION D: EMPLOYMENT DATA</h3>

                        <!-- Q16 -->
                        <div
                            class="form-group mt-6 p-6 bg-brand-50/30 dark:bg-brand-900/10 rounded-2xl border border-brand-100 dark:border-brand-900/30">
                            <label class="question-label text-brand-900 dark:text-brand-300">Q16. Are you presently
                                employed? <span class="required-star">*</span></label>
                            <div class="radio-group flex-row gap-8 mt-4">
                                <label
                                    class="radio-item border-brand-200 bg-white dark:bg-dark-bg-elevated px-4 py-2 rounded-xl shadow-sm">
                                    <input type="radio" name="q16_employed" value="Yes" x-model="employmentStatus"
                                        required class="form-radio text-brand-600">
                                    <span class="font-bold">Yes</span>
                                </label>
                                <label
                                    class="radio-item border-brand-200 bg-white dark:bg-dark-bg-elevated px-4 py-2 rounded-xl shadow-sm">
                                    <input type="radio" name="q16_employed" value="No" x-model="employmentStatus"
                                        required class="form-radio text-brand-600">
                                    <span class="font-bold">No</span>
                                </label>
                                <label
                                    class="radio-item border-brand-200 bg-white dark:bg-dark-bg-elevated px-4 py-2 rounded-xl shadow-sm">
                                    <input type="radio" name="q16_employed" value="Never Employed"
                                        x-model="employmentStatus" required class="form-radio text-brand-600">
                                    <span class="font-bold">Never Employed</span>
                                </label>
                            </div>
                        </div>

                        <!-- Q17 (Conditional) -->
                        <div x-show="employmentStatus === 'No' || employmentStatus === 'Never Employed'" x-transition
                            class="form-group mt-10 p-6 border-l-4 border-amber-400 bg-amber-50/30 dark:bg-amber-900/10 rounded-r-2xl">
                            <label class="question-label">Q17. Please state reason(s) why you are not yet employed. You
                                may check ( ) more than one answer.</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <template
                                    x-for="reason in ['Advance or further study', 'Family concern and decided not to find a job', 'Health-related reason(s)', 'Lack of work experience', 'No job opportunity', 'Did not look for a job']">
                                    <label
                                        class="checkbox-item flex items-center p-3 bg-white dark:bg-dark-bg-elevated rounded-xl shadow-sm border border-amber-100 dark:border-amber-900/30 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                                        <input type="checkbox" :name="'q17_reasons['+reason+']'"
                                            class="form-checkbox h-5 w-5 rounded text-amber-600 focus:ring-amber-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-dark-text-primary"
                                            x-text="reason"></span>
                                    </label>
                                </template>
                            </div>
                            <div class="flex items-center gap-3 mt-6 ml-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-dark-text-primary italic">Other
                                    reason(s), please specify:</span>
                                <input type="text" name="q17_others"
                                    class="table-input !w-auto !inline-flex !h-8 border-b border-t-0 border-l-0 border-r-0 rounded-none focus:ring-0 bg-transparent">
                            </div>
                        </div>

                        <!-- Q18-22 (Conditional Block) -->
                        <div x-show="employmentStatus === 'Yes'" x-transition class="space-y-10 mt-10">
                            <!-- Q18 -->
                            <div class="form-group">
                                <label class="question-label">Q18. Present Employment Status</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-3">
                                    <template
                                        x-for="status in ['Regular or Permanent', 'Temporary', 'Casual', 'Contractual', 'Self-employed']">
                                        <label
                                            class="radio-item border p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-dark-bg-subtle transition-all text-center flex flex-col items-center">
                                            <input type="radio" name="q18_status" :value="status" x-model="q18Status"
                                                class="form-radio text-brand-600 mb-2">
                                            <span class="text-[10px] font-bold uppercase tracking-tighter"
                                                x-text="status"></span>
                                        </label>
                                    </template>
                                </div>
                                <!-- Sub-question for Self-employed -->
                                <div x-show="q18Status === 'Self-employed'" x-transition
                                    class="mt-6 p-5 bg-white dark:bg-dark-bg-subtle rounded-2xl border-2 border-brand-200">
                                    <label class="question-label text-sm text-brand-700 dark:text-brand-300">"What
                                        skills acquired in college were you able to apply in your work?"</label>
                                    <input type="text" name="q18_skills" class="form-input mt-2 border-brand-300">
                                </div>
                            </div>

                            <!-- Q19 -->
                            <div class="form-group">
                                <label class="question-label">Q19. Present occupation (Ex. Grade School Teacher,
                                    Electrical Engineer, Self-employed)</label>
                                <input type="text" name="q19_occupation" class="form-input mt-2" placeholder="Enter your current job title" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                            </div>

                            <!-- Q20 -->
                            <div class="form-group">
                                <label class="question-label">Q20. Major line of business of the company you are
                                    presently employed in. Check one only.</label>
                                <select name="q20_business_line"
                                    class="form-input mt-3 bg-gray-50 dark:bg-dark-bg-subtle">
                                    <option value="">Select Line of Business</option>
                                    <template x-for="line in businessLines">
                                        <option :value="line" x-text="line"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Q21 -->
                            <div class="form-group">
                                <label class="question-label">Q21. Place of work</label>
                                <div class="radio-group flex-row gap-12 mt-3">
                                    <label class="radio-item text-lg font-black">
                                        <input type="radio" name="q21_place" value="Local" class="form-radio h-6 w-6">
                                        <span>Local</span>
                                    </label>
                                    <label class="radio-item text-lg font-black">
                                        <input type="radio" name="q21_place" value="Abroad" class="form-radio h-6 w-6">
                                        <span>Abroad</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Q22 -->
                            <div
                                class="form-group p-6 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                                <label class="question-label text-indigo-900 dark:text-indigo-300">Q22. Is this your
                                    first job after college?</label>
                                <div class="radio-group flex-row gap-12 mt-4">
                                    <label
                                        class="radio-item px-6 py-2 bg-white dark:bg-dark-bg-elevated rounded-xl shadow-sm font-black text-indigo-600">
                                        <input type="radio" name="q22_first_job" value="Yes" x-model="isFirstJob"
                                            class="form-radio">
                                        <span>Yes</span>
                                    </label>
                                    <label
                                        class="radio-item px-6 py-2 bg-white dark:bg-dark-bg-elevated rounded-xl shadow-sm font-black text-indigo-600">
                                        <input type="radio" name="q22_first_job" value="No" x-model="isFirstJob"
                                            class="form-radio">
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Conditional Q23-25 Block (if Q22 = Yes) -->
                            <div x-show="isFirstJob === 'Yes'" x-transition
                                class="space-y-10 pl-6 border-l-4 border-brand-200">
                                <!-- Q23 -->
                                <div class="form-group">
                                    <label class="question-label italic">Q23. What are your reason(s) for staying on the
                                        job? You may check ( ) more than one answer.</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                        <template
                                            x-for="reason in ['Salaries and benefits', 'Career challenge', 'Related to special skill', 'Related to course or program of study', 'Proximity to residence', 'Peer influence', 'Family influence']">
                                            <label class="checkbox-item flex items-center">
                                                <input type="checkbox" :name="'q23_reasons['+reason+']'"
                                                    class="form-checkbox h-5 w-5 rounded text-brand-600">
                                                <span class="ml-3 text-sm text-gray-700 dark:text-dark-text-primary"
                                                    x-text="reason"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-3 mt-4">
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-dark-text-primary italic">Other
                                            reason(s):</span>
                                        <input type="text" name="q23_others"
                                            class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                    </div>
                                    <p
                                        class="mt-6 text-xs font-black text-brand-600 uppercase tracking-widest bg-brand-50 dark:bg-brand-900/30 px-4 py-2 rounded-lg inline-block">
                                        Please proceed to Question 24</p>
                                </div>

                                <!-- Q24 -->
                                <div class="form-group">
                                    <label class="question-label">Q24. Is your first job related to the course you took
                                        up in college?</label>
                                    <div class="radio-group flex-row gap-8 mt-3">
                                        <label class="radio-item">
                                            <input type="radio" name="q24_related" value="Yes" x-model="isCourseRelated"
                                                class="form-radio">
                                            <span>Yes</span>
                                        </label>
                                        <label class="radio-item">
                                            <input type="radio" name="q24_related" value="No" x-model="isCourseRelated"
                                                class="form-radio">
                                            <span>No</span>
                                        </label>
                                    </div>
                                    <p x-show="isCourseRelated === 'No'"
                                        class="mt-4 text-[10px] font-bold text-amber-600 italic">Please proceed to
                                        Question 26</p>
                                </div>

                                <!-- Q25 (Conditional if Q24 = Yes) -->
                                <div x-show="isCourseRelated === 'Yes'" x-transition
                                    class="form-group p-5 bg-gray-50 dark:bg-dark-bg-subtle/50 rounded-2xl">
                                    <label
                                        class="question-label bg-white dark:bg-dark-bg-elevated px-3 py-1 rounded shadow-sm">Q25.
                                        What were your reasons for accepting the job?</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                        <template
                                            x-for="reason in ['Salaries & benefits', 'Career challenge', 'Related to special skills', 'Proximity to residence']">
                                            <label class="checkbox-item flex items-center">
                                                <input type="checkbox" :name="'q25_reasons['+reason+']'"
                                                    class="form-checkbox h-5 w-5 rounded text-brand-600">
                                                <span class="ml-3 text-sm" x-text="reason"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-3 mt-4">
                                        <span class="text-sm italic">Other reason(s):</span>
                                        <input type="text" name="q25_others"
                                            class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                    </div>
                                </div>
                            </div>

                            <!-- Conditional Q26-27 Block (if Q22 = No or Q24 = No) -->
                            <div x-show="isFirstJob === 'No' || isCourseRelated === 'No'" x-transition
                                class="space-y-10 pl-6 border-l-4 border-amber-200">
                                <!-- Q26 -->
                                <div class="form-group">
                                    <label class="question-label">Q26. What were your reason(s) for changing job? You
                                        may check ( ) more than one answer.</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                        <template
                                            x-for="reason in ['Salaries & benefits', 'Career challenge', 'Related to special skills', 'Proximity to residence']">
                                            <label class="checkbox-item flex items-center">
                                                <input type="checkbox" :name="'q26_reasons['+reason+']'"
                                                    class="form-checkbox h-5 w-5 rounded text-amber-600 focus:ring-amber-500">
                                                <span class="ml-3 text-sm" x-text="reason"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-3 mt-4">
                                        <span class="text-sm italic">Other reason(s), please specify:</span>
                                        <input type="text" name="q26_others"
                                            class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                    </div>
                                </div>

                                <!-- Q27 -->
                                <div class="form-group">
                                    <label class="question-label">Q27. How long did you stay in your first job?</label>
                                    <div class="radio-group mt-3">
                                        <template
                                            x-for="duration in ['Less than a month', '1 to 6 months', '7 to 11 months', '1 year to less than 2 years', '2 years to less than 3 years', '3 years to less than 4 years']">
                                            <label class="radio-item">
                                                <input type="radio" name="q27_stay_duration" :value="duration"
                                                    class="form-radio text-amber-600">
                                                <span x-text="duration"></span>
                                            </label>
                                        </template>
                                        <div class="flex items-center gap-3">
                                            <label class="radio-item">
                                                <input type="radio" name="q27_stay_duration" value="Others"
                                                    class="form-radio text-amber-600">
                                                <span>Others, please specify:</span>
                                            </label>
                                            <input type="text" name="q27_others"
                                                class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Q28 -->
                            <div class="form-group">
                                <label class="question-label">Q28. How did you find your first job?</label>
                                <div class="radio-group mt-3">
                                    <template
                                        x-for="source in ['Response to an advertisement', 'As walk-in applicant', 'Recommended by someone', 'Information from friends', 'Arranged by school\'s job placement officer', 'Family business', 'Job Fair or Public Employment Service Office (PESO)']">
                                        <label class="radio-item">
                                            <input type="radio" name="q28_find_source" :value="source"
                                                class="form-radio">
                                            <span x-text="source"></span>
                                        </label>
                                    </template>
                                    <div class="flex items-center gap-3">
                                        <label class="radio-item">
                                            <input type="radio" name="q28_find_source" value="Others"
                                                class="form-radio">
                                            <span>Others, please specify:</span>
                                        </label>
                                        <input type="text" name="q28_others"
                                            class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                    </div>
                                </div>
                            </div>

                            <!-- Q29 -->
                            <div class="form-group">
                                <label class="question-label">Q29. How long did it take you to land your first
                                    job?</label>
                                <div class="radio-group mt-3">
                                    <template
                                        x-for="duration in ['Less than a month', '1 to 6 months', '7 to 11 months', '1 year to less than 2 years', '2 years to less than 3 years', '3 years to less than 4 years']">
                                        <label class="radio-item">
                                            <input type="radio" name="q29_land_duration" :value="duration"
                                                class="form-radio">
                                            <span x-text="duration"></span>
                                        </label>
                                    </template>
                                    <div class="flex items-center gap-3">
                                        <label class="radio-item">
                                            <input type="radio" name="q29_land_duration" value="Others"
                                                class="form-radio">
                                            <span>Others, please specify:</span>
                                        </label>
                                        <input type="text" name="q29_others"
                                            class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                    </div>
                                </div>
                            </div>

                            <!-- Q30 Job Level Position Matrix -->
                            <div class="form-group">
                                <label class="question-label">Q30. Job Level Position</label>
                                <div class="mt-4 overflow-x-auto">
                                    <table class="matrix-table text-[10px]">
                                        <thead class="bg-gray-50 dark:bg-dark-bg-subtle">
                                            <tr>
                                                <th class="text-left p-2">Level</th>
                                                <th class="text-center p-2">30.1. First Job</th>
                                                <th class="text-center p-2">30.2. Current or Present Job</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                            <template
                                                x-for="level in ['Rank or Clerical', 'Professional, Technical or Supervisory', 'Managerial or Executive', 'Self-employed']">
                                                <tr>
                                                    <td class="p-3 font-bold uppercase tracking-tighter" x-text="level">
                                                    </td>
                                                    <td class="p-3 text-center">
                                                        <input type="radio" name="q30_first_job_level" :value="level"
                                                            class="form-radio">
                                                    </td>
                                                    <td class="p-3 text-center">
                                                        <input type="radio" name="q30_current_job_level" :value="level"
                                                            class="form-radio">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Q31 -->
                            <div class="form-group">
                                <label class="question-label">Q31. What is your initial gross monthly earning in your
                                    first job after college?</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                    <template
                                        x-for="salary in ['Below P5,000.00', 'P5,000.00 to less than P10,000.00', 'P10,000.00 to less than P15,000.00', 'P15,000.00 to less than P20,000.00', 'P20,000.00 to less than P25,000.00', 'P25,000.00 and above']">
                                        <label
                                            class="radio-item border p-4 rounded-2xl hover:bg-green-50 dark:hover:bg-green-900/10 transition-colors">
                                            <input type="radio" name="q31_salary" :value="salary"
                                                class="form-radio text-green-600">
                                            <span class="ml-3 font-black text-gray-900 dark:text-dark-text-primary"
                                                x-text="salary"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Q32 -->
                            <div
                                class="form-group p-6 bg-purple-50/50 dark:bg-purple-900/10 rounded-2xl border border-purple-100 dark:border-purple-900/30">
                                <label class="question-label text-purple-900 dark:text-purple-300">Q32. Was the
                                    curriculum you had in college relevant to your first job?</label>
                                <div class="radio-group flex-row gap-12 mt-4">
                                    <label
                                        class="radio-item px-8 py-3 bg-white dark:bg-dark-bg-elevated rounded-2xl shadow-sm font-black text-purple-600">
                                        <input type="radio" name="q32_relevant" value="Yes"
                                            x-model="isCurriculumRelevant" class="form-radio">
                                        <span>Yes</span>
                                    </label>
                                    <label
                                        class="radio-item px-8 py-3 bg-white dark:bg-dark-bg-elevated rounded-2xl shadow-sm font-black text-purple-600">
                                        <input type="radio" name="q32_relevant" value="No"
                                            x-model="isCurriculumRelevant" class="form-radio">
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Q33 (Conditional if Q32 = Yes) -->
                            <div x-show="isCurriculumRelevant === 'Yes'" x-transition
                                class="form-group p-6 border-l-4 border-purple-400 bg-white dark:bg-dark-bg-subtle rounded-r-2xl">
                                <label class="question-label">Q33. If YES, what competencies learned in college did you
                                    find very useful in your first job? You may check ( ) more than one answer.</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                    <template
                                        x-for="skill in ['Communication skills', 'Human Relations skills', 'Entrepreneurial skills', 'Information Technology skills', 'Problem-solving skills', 'Critical Thinking skills']">
                                        <label
                                            class="checkbox-item flex items-center p-3 border rounded-xl hover:bg-purple-50 transition-colors">
                                            <input type="checkbox" :name="'q33_skills['+skill+']'"
                                                class="form-checkbox h-5 w-5 rounded text-purple-600">
                                            <span class="ml-3 text-sm font-bold" x-text="skill"></span>
                                        </label>
                                    </template>
                                </div>
                                <div class="flex items-center gap-3 mt-6">
                                    <span class="text-sm italic">Other skills:</span>
                                    <input type="text" name="q33_others"
                                        class="table-input !w-auto !inline-flex !h-8 border-b rounded-none focus:ring-0" placeholder="Please specify or N/A" @blur="if($event.target.value === '') $event.target.value = 'N/A'">
                                </div>
                            </div>

                            <!-- Q34 -->
                            <div class="form-group">
                                <label class="question-label">Q34. List down suggestions to further improve your course
                                    curriculum</label>
                                <textarea name="q34_suggestions" rows="4" class="form-input mt-2 resize-none"
                                    placeholder="Your valuable feedback..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- REFERRAL SECTION -->
                    <div class="section-container !bg-gray-50 dark:!bg-dark-bg-subtle">
                        <h3 class="section-title italic">REFERRAL SECTION</h3>
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-dark-text-muted mt-4">
                            "Thank you for taking time out to fill out this questionnaire. Please return this GTS to
                            your Institution. Being one of the alumni of your institution, may we request you to list
                            down the names of other college graduates (AY 2000-2001 to AY 2003-2004) from your
                            institution including their addresses and contact numbers. Their participation will also be
                            needed to make this study more meaningful and useful."
                        </p>

                        <div class="form-group mt-8">
                            <label class="question-label underline">REF. List other graduates from your
                                institution</label>
                            <div class="mt-4 overflow-x-auto">
                                <table class="dynamic-table !bg-white dark:!bg-dark-bg-elevated">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Full Address</th>
                                            <th>Contact Number</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, index) in ref_rows" :key="index">
                                            <tr>
                                                <td><input type="text" :name="'ref['+index+'][name]'"
                                                        class="table-input" placeholder="Graduate Name" @blur="if($event.target.value === '') $event.target.value = 'N/A'"></td>
                                                <td><input type="text" :name="'ref['+index+'][address]'"
                                                        class="table-input" placeholder="Full Address" @blur="if($event.target.value === '') $event.target.value = 'N/A'"></td>
                                                <td><input type="tel" :name="'ref['+index+'][contact]'"
                                                        class="table-input" placeholder="Contact Number" @blur="if($event.target.value === '') $event.target.value = 'N/A'"></td>
                                                <td>
                                                    <button type="button" @click="removeRow('ref_rows', index)"
                                                        x-show="ref_rows.length > 4"
                                                        class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <button type="button" @click="addRow('ref_rows')"
                                    class="add-row-btn mt-4 text-gray-600 bg-gray-100 hover:bg-gray-200 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Referree
                                </button>
                                <p class="text-[10px] text-gray-400 font-bold uppercase italic mt-2">"Please use extra
                                    sheet if needed."</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-10 flex justify-end">
                        <button type="submit" class="submit-btn">
                            Submit CHED GTS
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .section-container {
            @apply p-6 sm:p-10 rounded-3xl bg-gray-50/50 dark:bg-dark-bg-subtle/30 border border-gray-100 dark:border-dark-border;
        }

        .section-title {
            @apply text-lg font-black text-gray-900 dark:text-dark-text-primary border-b-2 border-brand-600 uppercase tracking-widest pb-2 mb-6 block w-fit;
        }

        .question-label {
            @apply block text-sm font-bold text-gray-800 dark:text-dark-text-primary leading-tight mb-2;
        }

        .required-star {
            @apply text-red-500;
        }

        .form-input {
            @apply w-full rounded-xl border-gray-200 dark:border-dark-border dark:bg-dark-bg-subtle text-gray-700 dark:text-dark-text-secondary focus:border-brand-500 focus:ring-brand-500 transition-all;
        }

        .radio-group {
            @apply flex flex-col gap-2 mt-2;
        }

        .radio-item {
            @apply flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-600 dark:text-dark-text-secondary hover:text-gray-900 dark:hover:text-dark-text-primary transition-colors;
        }

        .checkbox-item {
            @apply flex items-center gap-2 cursor-pointer;
        }

        .dynamic-table {
            @apply w-full border-collapse rounded-xl overflow-hidden border border-gray-200 dark:border-dark-border;
        }

        .dynamic-table th {
            @apply bg-brand-50 dark:bg-brand-900/10 p-4 text-left text-[10px] font-black uppercase text-brand-900 dark:text-brand-300 border-b border-gray-200 dark:border-dark-border;
        }

        .dynamic-table td {
            @apply p-2 border-b border-gray-100 dark:border-dark-border;
        }

        .table-input {
            @apply w-full text-sm border-transparent focus:border-brand-500 focus:ring-0 bg-transparent dark:text-dark-text-secondary transition-all;
        }

        .add-row-btn {
            @apply inline-flex items-center px-4 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all;
        }

        .matrix-table {
            @apply w-full border border-gray-200 dark:border-dark-border rounded-xl;
        }

        .matrix-table th {
            @apply p-3 border-b border-gray-200 dark:border-dark-border text-[9px] uppercase font-black tracking-widest text-gray-400;
        }

        .matrix-table td {
            @apply border-b border-gray-100 dark:border-dark-border;
        }

        .submit-btn {
            @apply inline-flex items-center px-10 py-4 bg-brand-600 border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-widest hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-xl shadow-brand-500/20;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .section-container {
                border: none !important;
                background: transparent !important;
                margin: 0 !important;
                padding: 10px 0 !important;
            }

            .bg-white {
                box-shadow: none !important;
            }

            .py-12 {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }

            .shadow-xl {
                border: none !important;
            }
        }
    </style>

    <script>
        function gtsForm() {
            return {
                months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                years: Array.from({ length: 61 }, (_, i) => 2010 - i),
                regions: ['Region 1', 'Region 2', 'Region 3', 'Region 4', 'Region 5', 'Region 6', 'Region 7', 'Region 8', 'Region 9', 'Region 10', 'Region 11', 'Region 12', 'NCR', 'CAR', 'ARMM', 'CARAGA'],
                businessLines: [
                    'Agriculture, Hunting and Forestry', 'Fishing', 'Mining and Quarrying', 'Manufacturing',
                    'Electricity, Gas and Water Supply', 'Construction',
                    'Wholesale and Retail Trade, repair of motor vehicles, motorcycles and personal and household goods',
                    'Hotels and Restaurants', 'Transport Storage and Communication', 'Financial Intermediation',
                    'Real Estate, Renting and Business Activities',
                    'Public Administration and Defense; Compulsory Social Security', 'Education',
                    'Health and Social Work', 'Other Community, Social and Personal Service Activities',
                    'Private Households with Employed Persons', 'Extra-territorial Organizations and Bodies'
                ],
                q14_reasons: [
                    'High grades in the course or subject area(s) related to the course', 'Good grades in high school',
                    'Influence of parents or relatives', 'Peer Influence', 'Inspired by a role model',
                    'Strong passion for the profession', 'Prospect for immediate employment',
                    'Status or prestige of the profession', 'Availability of course offering in chosen institution',
                    'Prospect of career advancement', 'Affordable for the family', 'Prospect of attractive compensation',
                    'Opportunity for employment abroad', 'No particular choice or no better idea'
                ],

                // Active States
                employmentStatus: '',
                q18Status: '',
                isFirstJob: '',
                isCourseRelated: '',
                isCurriculumRelevant: '',

                // Dynamic Row Structures
                q12_rows: Array.from({ length: 4 }, () => ({})),
                q13_rows: Array.from({ length: 2 }, () => ({})),
                q15a_rows: Array.from({ length: 3 }, () => ({})),
                ref_rows: Array.from({ length: 4 }, () => ({})),

                addRow(key) {
                    this[key].push({});
                },
                removeRow(key, index) {
                    this[key].splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>