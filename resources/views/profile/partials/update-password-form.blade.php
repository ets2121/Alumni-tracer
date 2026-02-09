<section x-data="{
    otpRequired: false,
    loading: false,
    otpCode: '',
    error: '',
    email: '{{ $user->email }}',

    async handleSubmit(e) {
        if (!this.otpRequired) {
            this.sendOtp();
            return;
        }
        e.target.submit();
    },

    async sendOtp() {
        this.loading = true;
        this.error = '';
        try {
            const response = await fetch('{{ route('otp.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: this.email,
                    type: 'password_update'
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
            this.error = 'Failed to send security code.';
        } finally {
            this.loading = false;
        }
    },

    async verifyOtp() {
        this.loading = true;
        this.error = '';
        try {
            const response = await fetch('{{ route('otp.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: this.email,
                    code: this.otpCode,
                    type: 'password_update'
                })
            });
            const result = await response.json();
            if (result.success) {
                document.getElementById('password-update-form').submit();
            } else {
                this.error = result.message;
            }
        } catch (e) {
            this.error = 'Identity verification failed.';
        } finally {
            this.loading = false;
        }
    }
}">
    <header>
        <h2 class="text-xl font-black text-brand-900 uppercase tracking-tight">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <div x-show="error" class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm animate-in fade-in"
        x-text="error"></div>

    <form id="password-update-form" method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6"
        @submit.prevent="handleSubmit">
        @csrf
        @method('put')

        <div class="space-y-4">
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password"
                    class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" />
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm New Password')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" :disabled="loading"
                class="inline-flex items-center px-6 py-3 bg-brand-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-700 shadow-lg shadow-brand-100 transition-all disabled:opacity-50">
                <span x-show="!loading">{{ __('Update Password') }}</span>
                <span x-show="loading">{{ __('Processing...') }}</span>
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold">{{ __('Password changed.') }}</p>
            @endif
        </div>
    </form>

    <!-- Identity Verification Modal -->
    <div x-show="otpRequired" style="display: none;" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 relative animate-in slide-in-from-bottom-8 duration-300">
            <h3 class="text-2xl font-black text-brand-900 uppercase tracking-tight mb-2">Confirm Your Identity</h3>
            <p class="text-sm text-gray-600 mb-6">For security, we've sent a 6-digit code to
                <strong>{{ $user->email }}</strong>. Enter it to authorize this password change.
            </p>

            <div x-show="error" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"
                x-text="error"></div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">6-Digit Security
                        Code</label>
                    <input type="text" x-model="otpCode" maxlength="6"
                        class="w-full text-center text-3xl font-black tracking-[0.5em] py-4 border-gray-200 rounded-2xl focus:ring-brand-500 focus:border-brand-500 bg-gray-50"
                        placeholder="000000">
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" @click="verifyOtp" :disabled="loading"
                        class="w-full py-4 bg-brand-600 text-white rounded-2xl font-bold text-sm hover:bg-brand-700 shadow-lg shadow-brand-100 transition-all disabled:opacity-50">
                        <span x-show="!loading">Authorize & Change</span>
                        <span x-show="loading">Verifying...</span>
                    </button>

                    <button type="button" @click="otpRequired = false; error = ''"
                        class="w-full py-2 text-sm text-gray-500 font-medium hover:text-gray-700">
                        Nevermind, Go Back
                    </button>
                </div>

                <div class="text-center pt-2">
                    <button type="button" @click="sendOtp" class="text-xs text-brand-600 font-bold hover:underline">
                        Email hasn't arrived? Resend code
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>