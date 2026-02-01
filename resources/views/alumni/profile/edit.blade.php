<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Alumni Profile') }}
            </h2>
            @if($user->status === 'active')
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Verified
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12" x-data="employmentHistoryHandler()">
        <script>
        function employmentHistoryHandler() {
            return {
                isOpen: false,
                isEdit: false,
                updateUrl: '',
                form: {
                    company_name: '', position: '', industry: '', location: '',
                    start_date: '', end_date: '', is_current: false, description: ''
                },
                
                openModal() {
                    this.isOpen = true;
                    this.isEdit = false;
                    this.resetForm();
                },

                editHistory(history) {
                    this.isOpen = true;
                    this.isEdit = true;
                    this.updateUrl = `/employment/${history.id}`;
                    this.form = {
                        company_name: history.company_name,
                        position: history.position,
                        industry: history.industry,
                        location: history.location,
                        start_date: history.start_date ? history.start_date.split('T')[0] : '',
                        end_date: history.end_date ? history.end_date.split('T')[0] : '',
                        is_current: !!history.is_current,
                        description: history.description
                    };
                },

                closeModal() {
                    this.isOpen = false;
                },

                resetForm() {
                    this.form = {
                        company_name: '', position: '', industry: '', location: '',
                        start_date: '', end_date: '', is_current: false, description: ''
                    };
                },

                deleteHistory(url) {
                    if(confirm('Are you sure you want to remove this record?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;
                        form.innerHTML = `
                            @csrf
                            @method('DELETE')
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            }
        }
        </script>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Success</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('alumni.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo -->
                        <div class="flex flex-col items-center justify-center space-y-4 pb-6 border-b">
                            <div class="relative">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-brand-100">
                                @else
                                    <div class="w-32 h-32 rounded-full bg-brand-50 flex items-center justify-center border-4 border-brand-100">
                                        <svg class="w-16 h-16 text-brand-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                        </svg>
                                    </div>
                                @endif
                                <label for="avatar" class="absolute bottom-0 right-0 bg-brand-600 text-white p-2 rounded-full cursor-pointer hover:bg-brand-700 shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                                </label>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-900 border-none pb-0 mb-0">Profile Photo</h3>
                                <p class="text-xs text-gray-500">Upload a professional photo for your alumni profile</p>
                                @error('avatar') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $profile->first_name ?? '') }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('first_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $profile->middle_name ?? '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('middle_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $profile->last_name ?? '') }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('last_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="gender" id="gender" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $profile->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input type="date" name="dob" id="dob" value="{{ old('dob', $profile->dob ?? '') }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('dob') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="civil_status" class="block text-sm font-medium text-gray-700">Civil Status</label>
                                    <select name="civil_status" id="civil_status" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                        <option value="">Select Status</option>
                                        <option value="Single" {{ old('civil_status', $profile->civil_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status', $profile->civil_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ old('civil_status', $profile->civil_status ?? '') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ old('civil_status', $profile->civil_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                    @error('civil_status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-1">
                                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $profile->contact_number ?? '') }}" required placeholder="09123456789"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('contact_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Home Address</label>
                                    <input type="text" name="address" id="address" value="{{ old('address', $profile->address ?? '') }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Academic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="course_id" class="block text-sm font-medium text-gray-700">Course Graduated</label>
                                    <select name="course_id" id="course_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $profile->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                                {{ $course->code }} - {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="batch_year" class="block text-sm font-medium text-gray-700">Year Graduated</label>
                                    <input type="number" name="batch_year" id="batch_year" value="{{ old('batch_year', $profile->batch_year ?? '') }}" required min="1900" max="{{ date('Y') + 1 }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    @error('batch_year') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Proof of Graduation/Identity -->
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Verification Documents</h3>
                            <div class="grid grid-cols-1 gap-6 text-sm text-gray-600">
                                <p>Please upload a clear copy of your Diploma, Transcript of Records, or school ID to verify your alumni status.</p>
                                
                                @if($profile && $profile->proof_path)
                                    <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-blue-900 text-base">Current Proof Uploaded</p>
                                            <a href="{{ asset('storage/' . $profile->proof_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium mt-1 inline-block">View Document</a>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <label for="proof" class="block text-sm font-medium text-gray-700">Upload New Proof (PDF, JPG, PNG)</label>
                                    <input type="file" name="proof" id="proof"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                    <p class="mt-1 text-xs text-gray-500">Max size: 2MB</p>
                                    @error('proof') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="mt-8">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Employment Information</h3>
                            </div>
                            
                            <!-- Overall Employment Status (For Analytics) -->
                            <div class="mb-6">
                                <label for="employment_status" class="block text-sm font-medium text-gray-700">Current Employment Status</label>
                                <select name="employment_status" id="employment_status" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                    <option value="">Select Status</option>
                                    <option value="Employed" {{ old('employment_status', $profile->employment_status ?? '') == 'Employed' ? 'selected' : '' }}>Employed</option>
                                    <option value="Self-employed" {{ old('employment_status', $profile->employment_status ?? '') == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                                    <option value="Unemployed" {{ old('employment_status', $profile->employment_status ?? '') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                    <option value="Underemployed" {{ old('employment_status', $profile->employment_status ?? '') == 'Underemployed' ? 'selected' : '' }}>Underemployed</option>
                                    <option value="Student" {{ old('employment_status', $profile->employment_status ?? '') == 'Student' ? 'selected' : '' }}>Student / Further Studies</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Please select your primary status. This is important for alumni analytics.</p>
                                @error('employment_status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h4 class="text-base font-medium leading-6 text-gray-900">Work History</h4>
                                <button type="button" @click="openModal()" class="text-sm text-brand-600 hover:text-brand-800 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Position
                                </button>
                            </div>

                            <!-- List -->
                            <div class="space-y-4">
                                @forelse($user->employmentHistories as $history)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 relative group transition-all hover:bg-white hover:shadow-md">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-lg">{{ $history->position }}</h4>
                                                <p class="text-brand-600 font-medium">{{ $history->company_name }}</p>
                                                <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                                                    <span>{{ $history->start_date->format('M Y') }} - {{ $history->is_current ? 'Present' : ($history->end_date ? $history->end_date->format('M Y') : 'N/A') }}</span>
                                                    @if($history->is_current)
                                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-bold">Current</span>
                                                    @endif
                                                </div>
                                                @if($history->location)
                                                    <p class="text-xs text-gray-400 mt-1">{{ $history->location }}</p>
                                                @endif
                                            </div>
                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                                <button type="button" @click='editHistory(@json($history))' class="text-blue-500 hover:text-blue-700 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button type="button" @click="deleteHistory('{{ route('alumni.employment.destroy', $history->id) }}')" class="text-red-500 hover:text-red-700 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-gray-400 text-sm italic bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                        No employment history recorded yet.
                                    </div>
                                @endforelse
                            </div>

                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                             <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </form>

                    <!-- Employment History Modal (Moved Outside) -->
                    <div>
                        <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal()"></div>

                                <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" x-text="isEdit ? 'Edit Position' : 'Add Position'"></h3>
                                    
                                    <form :action="isEdit ? updateUrl : '{{ route('alumni.employment.store') }}'" method="POST" id="employmentForm">
                                        @csrf
                                        <template x-if="isEdit">
                                            <input type="hidden" name="_method" value="PUT">
                                        </template>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                                                <input type="text" name="company_name" x-model="form.company_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Position</label>
                                                <input type="text" name="position" x-model="form.position" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Industry</label>
                                                    <input type="text" name="industry" x-model="form.industry" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                                    <input type="text" name="location" x-model="form.location" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                                    <input type="date" name="start_date" x-model="form.start_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                                                    <input type="date" name="end_date" x-model="form.end_date" :disabled="form.is_current" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm disabled:bg-gray-100">
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" name="is_current" id="is_current" x-model="form.is_current" :checked="form.is_current" value="1" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                                                <label for="is_current" class="ml-2 block text-sm text-gray-900">I am currently working here</label>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                                <textarea name="description" x-model="form.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 sm:mt-6 flex gap-3">
                                            <button type="submit" class="flex-1 inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-brand-600 border border-transparent rounded-md shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:text-sm">
                                                Save
                                            </button>
                                            <button type="button" @click="closeModal()" class="flex-1 inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
