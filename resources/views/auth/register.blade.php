<x-guest-layout>
    <div x-data="{
        step: 'form', // 'form' or 'verify'
        loading: false,
        error: '',
        message: '',
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        },
        otp: '',

        async requestOtp() {
            this.loading = true;
            this.error = '';
            try {
                const response = await fetch('{{ route('verification.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: this.form.email,
                        type: 'signup'
                    })
                });
                const result = await response.json();
                if (result.success) {
                    this.step = 'verify';
                    this.message = result.message;
                } else {
                    this.error = result.message;
                }
            } catch (e) {
                this.error = 'Something went wrong. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        async verifyAndRegister() {
            this.loading = true;
            this.error = '';
            try {
                // First verify OTP
                const vResponse = await fetch('{{ route('verification.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: this.form.email,
                        code: this.otp,
                        type: 'signup'
                    })
                });
                const vResult = await vResponse.json();
                
                if (!vResult.success) {
                    this.error = vResult.message;
                    this.loading = false;
                    return;
                }

                // If OTP valid, submit registration
                const rResponse = await fetch('{{ route('register') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                
                if (rResponse.ok) {
                    window.location.href = '{{ route('dashboard') }}';
                } else {
                    const rResult = await rResponse.json();
                    this.error = rResult.message || 'Registration failed.';
                    this.step = 'form'; // Go back to fix validation errors
                }
            } catch (e) {
                this.error = 'Connection error. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }" class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">

        <div x-show="step === 'form'" class="animate-in fade-in duration-300">
            <h2 class="text-2xl font-black text-brand-900 mb-6 uppercase tracking-tight">Create Account</h2>

            <div x-show="error" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"
                x-text="error"></div>

            <form @submit.prevent="requestOtp" class="space-y-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Full Name')" />
                    <x-text-input id="name" x-model="form.name" class="block mt-1 w-full" type="text" required
                        autofocus />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email Address')" />
                    <x-text-input id="email" x-model="form.email" class="block mt-1 w-full" type="email" required />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" x-model="form.password" class="block mt-1 w-full" type="password"
                        required />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" x-model="form.password_confirmation"
                        class="block mt-1 w-full" type="password" required />
                </div>

                <div class="flex items-center justify-between mt-8">
                    <a class="text-sm text-gray-600 hover:text-brand-600 font-medium underline"
                        href="{{ route('login') }}">
                        Already registered?
                    </a>

                    <button type="submit" :disabled="loading"
                        class="inline-flex items-center px-6 py-3 bg-brand-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-900 focus:outline-none focus:border-brand-900 focus:ring ring-brand-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg shadow-brand-100">
                        <span x-show="!loading">Send Code</span>
                        <span x-show="loading">Please wait...</span>
                    </button>
                </div>
            </form>
        </div>

        <div x-show="step === 'verify'" style="display: none;" x-cloak
            class="animate-in slide-in-from-right-4 duration-300">
            <h2 class="text-2xl font-black text-brand-900 mb-2 uppercase tracking-tight">Verify Email</h2>
            <p class="text-sm text-gray-600 mb-6" x-text="message"></p>

            <div x-show="error" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"
                x-text="error"></div>

            <form @submit.prevent="verifyAndRegister" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">6-Digit Code</label>
                    <input type="text" x-model="otp" maxlength="6" required
                        class="w-full text-center text-2xl font-black tracking-[0.5em] py-4 border-gray-300 rounded-xl focus:ring-brand-500 focus:border-brand-500"
                        placeholder="000000">
                </div>

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs text-blue-700">
                            <strong>Check your spam folder</strong> if you don't see the email within a minute. Codes
                            are temporary and expire in 5 minutes.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" @click="step = 'form'"
                        class="text-sm text-gray-600 hover:text-brand-600 font-medium">
                        &larr; Back to Details
                    </button>

                    <button type="submit" :disabled="loading"
                        class="inline-flex items-center px-8 py-3 bg-brand-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-700 shadow-lg shadow-brand-100 disabled:opacity-50">
                        <span x-show="!loading">Verify & Sign Up</span>
                        <span x-show="loading">Processing...</span>
                    </button>
                </div>

                <div class="text-center mt-4">
                    <button type="button" @click="requestOtp" class="text-xs text-brand-600 font-bold hover:underline">
                        Resend Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>