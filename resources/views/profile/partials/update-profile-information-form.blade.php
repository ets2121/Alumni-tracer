<section x-data="{
    email: '{{ old('email', $user->email) }}',
    originalEmail: '{{ $user->email }}',
    otpRequired: false,
    loading: false,
    otpCode: '',
    error: '',
    
    async handleSubmit(e) {
        if (this.email !== this.originalEmail && !this.otpRequired) {
            this.sendOtp();
            return;
        }
        e.target.submit();
    },

    async sendOtp() {
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
                    email: this.email,
                    type: 'email_update'
                })
            });
            const result = await response.json();
            if (result.success) {
                this.otpRequired = true;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: result.message, type: 'success' } }));
            } else {
                this.error = result.message;
            }
        } catch (e) {
            this.error = 'Failed to send verification code.';
        } finally {
            this.loading = false;
        }
    },

    async verifyOtp() {
        this.loading = true;
        this.error = '';
        try {
            const response = await fetch('{{ route('verification.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: this.email,
                    code: this.otpCode,
                    type: 'email_update'
                })
            });
            const result = await response.json();
            if (result.success) {
                // Now submit the main form
                document.getElementById('profile-update-form').submit();
            } else {
                this.error = result.message;
            }
        } catch (e) {
            this.error = 'Verification failed.';
        } finally {
            this.loading = false;
        }
    }
}">
    <header>
        <h2 class="text-xl font-black text-brand-900 uppercase tracking-tight">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <div x-show="error" class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm animate-in fade-in"
        x-text="error"></div>

    <form id="profile-update-form" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6"
        enctype="multipart/form-data" @submit.prevent="handleSubmit">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Profile Photo')" />
            <div class="mt-2 flex items-center gap-4">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                        class="w-20 h-20 rounded-2xl object-cover ring-2 ring-gray-100 ring-offset-2">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-400">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                @endif
                <input type="file" name="avatar" id="avatar"
                    class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-all"
                    accept="image/*">
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" x-model="email" required
                    autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                <p x-show="email !== originalEmail"
                    class="mt-2 text-xs text-brand-600 font-bold italic animate-in slide-in-from-left-2">Updating email
                    requires verification</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" :disabled="loading"
                class="inline-flex items-center px-6 py-3 bg-brand-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-700 shadow-lg shadow-brand-100 transition-all disabled:opacity-50">
                <span x-show="!loading">{{ __('Save Changes') }}</span>
                <span x-show="loading">{{ __('Processing...') }}</span>
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold">{{ __('Successfully saved.') }}</p>
            @endif
        </div>
    </form>

    <!-- OTP Modal Overlay -->
    <div x-show="otpRequired" style="display: none;" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 relative animate-in slide-in-from-bottom-8 duration-300">
            <h3 class="text-2xl font-black text-brand-900 uppercase tracking-tight mb-2">Verify New Email</h3>
            <p class="text-sm text-gray-600 mb-6">A verification code has been sent to <strong x-text="email"></strong>.
                Please enter it below to confirm the change.</p>

            <div x-show="error" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"
                x-text="error"></div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">6-Digit OTP
                        Code</label>
                    <input type="text" x-model="otpCode" maxlength="6"
                        class="w-full text-center text-3xl font-black tracking-[0.5em] py-4 border-gray-200 rounded-2xl focus:ring-brand-500 focus:border-brand-500 bg-gray-50"
                        placeholder="000000">
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" @click="verifyOtp" :disabled="loading"
                        class="w-full py-4 bg-brand-600 text-white rounded-2xl font-bold text-sm hover:bg-brand-700 shadow-lg shadow-brand-100 transition-all disabled:opacity-50">
                        <span x-show="!loading">Confirm & Save</span>
                        <span x-show="loading">Verifying...</span>
                    </button>

                    <button type="button" @click="otpRequired = false; error = ''"
                        class="w-full py-2 text-sm text-gray-500 font-medium hover:text-gray-700">
                        Cancel Change
                    </button>
                </div>

                <div class="text-center pt-2">
                    <button type="button" @click="sendOtp" class="text-xs text-brand-600 font-bold hover:underline">
                        Didn't receive code? Resend
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>