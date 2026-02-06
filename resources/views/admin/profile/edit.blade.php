<x-layouts.admin>
    <x-slot name="header">
        System Administrator Profile
    </x-slot>

    <div class="max-w-7xl mx-auto" x-data="{ 
        activeTab: 'settings',
        avatarPreview: null,
        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
                setTimeout(() => { 
                    if(confirm('Update profile picture?')) this.$refs.avatarForm.submit(); 
                    else this.avatarPreview = null; 
                }, 500);
            }
        },
        // OTP Logic
        email: '{{ old('email', $admin->email) }}',
        originalEmail: '{{ $admin->email }}',
        otpRequired: false,
        otpType: '', // 'email' or 'password'
        otpCode: '',
        loading: false,
        error: '',

        async handleInfoSubmit(e) {
            if (this.email !== this.originalEmail && !this.otpRequired) {
                this.sendOtp('email_update');
                return;
            }
            e.target.submit();
        },

        async handlePasswordSubmit(e) {
            if (!this.otpRequired) {
                this.sendOtp('password_update');
                return;
            }
            e.target.submit();
        },

        async sendOtp(type) {
            this.loading = true;
            this.error = '';
            this.otpType = type;
            try {
                const response = await fetch('{{ route('otp.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email: type === 'email_update' ? this.email : this.originalEmail, type: type })
                });
                const result = await response.json();
                if (result.success) { this.otpRequired = true; } 
                else { this.error = result.message; }
            } catch (e) { this.error = 'Failed to send verification code.'; }
            finally { this.loading = false; }
        },

        async verifyOtp() {
            this.loading = true;
            this.error = '';
            try {
                const response = await fetch('{{ route('otp.verify') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ 
                        email: this.otpType === 'email_update' ? this.email : this.originalEmail, 
                        code: this.otpCode, 
                        type: this.otpType 
                    })
                });
                const result = await response.json();
                if (result.success) {
                    if (this.otpType === 'email_update') document.getElementById('info-form').submit();
                    else document.getElementById('password-form').submit();
                } else { this.error = result.message; }
            } catch (e) { this.error = 'Verification failed.'; }
            finally { this.loading = false; }
        }
    }">
        <!-- Profile Header Card -->
        <div
            class="bg-white rounded-[2.5rem] shadow-xl shadow-brand-100/20 border border-gray-100 overflow-hidden mb-10 transition-all duration-500 hover:shadow-2xl hover:shadow-brand-100/30">
            <div class="h-40 bg-gradient-to-r from-brand-600 via-brand-700 to-brand-900 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 0 L100 100 L0 100 Z"></path>
                    </svg>
                </div>
                <div
                    class="absolute top-4 right-4 bg-white/10 backdrop-blur-md rounded-full px-4 py-1.5 text-[10px] font-black text-white uppercase tracking-widest border border-white/20">
                    System Node: Alpha-1
                </div>
            </div>
            <div class="px-10 pb-10">
                <div class="relative flex flex-col md:flex-row items-center md:items-end gap-10 -mt-20">
                    <div class="relative group">
                        <div
                            class="relative w-40 h-40 rounded-[2.5rem] overflow-hidden ring-8 ring-white shadow-2xl bg-white transition-transform duration-500 group-hover:scale-105">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!avatarPreview">
                                @if($admin->avatar)
                                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center text-brand-600">
                                        <span
                                            class="text-5xl font-black">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </template>

                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                @click="$refs.avatarInput.click()">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>

                        <form x-ref="avatarForm" action="{{ route('admin.profile.avatar') }}" method="POST"
                            enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" name="avatar" x-ref="avatarInput" @change="handleAvatarChange">
                        </form>
                    </div>
                    <div class="text-center md:text-left flex-1 mb-2">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 rounded-full mb-3 border border-brand-100">
                            <span class="w-2 h-2 bg-brand-500 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-brand-600 uppercase tracking-widest">Active
                                Session</span>
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tight leading-none mb-2">
                            {{ $admin->name }}</h2>
                        <div class="flex items-center justify-center md:justify-start gap-4">
                            <p class="text-gray-400 font-bold text-sm">{{ $admin->email }}</p>
                            <span class="w-1.5 h-1.5 bg-gray-200 rounded-full"></span>
                            <p class="text-brand-600 font-black uppercase tracking-widest text-xs">Primary Admin Account
                            </p>
                        </div>
                    </div>
                    <div class="flex bg-gray-50 p-1.5 rounded-2xl border border-gray-100 mb-2">
                        <button @click="activeTab = 'settings'"
                            :class="activeTab === 'settings' ? 'bg-white text-brand-600 shadow-lg' : 'text-gray-500 hover:text-gray-900'"
                            class="px-8 py-3 rounded-xl text-sm font-black transition-all uppercase tracking-widest">Settings</button>
                        <button @click="activeTab = 'activity'"
                            :class="activeTab === 'activity' ? 'bg-white text-brand-600 shadow-lg' : 'text-gray-500 hover:text-gray-900'"
                            class="px-8 py-3 rounded-xl text-sm font-black transition-all uppercase tracking-widest">Activity
                            Log</button>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Settings Content -->
            <div class="lg:col-span-2 space-y-10" x-show="activeTab === 'settings'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <!-- Personal Info -->
                <div
                    class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200">
                    <div class="flex items-center gap-5 mb-10 border-b border-gray-50 pb-8">
                        <div class="p-4 bg-brand-50 text-brand-600 rounded-3xl">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Administrative Context
                            </h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Primary identity markers
                                for system-wide recognition</p>
                        </div>
                    </div>

                    <form id="info-form" action="{{ route('admin.profile.update') }}" method="POST" class="space-y-8" @submit.prevent="handleInfoSubmit">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Official
                                    Name</label>
                                <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-brand-500/20 focus:ring-4 focus:ring-brand-500/5 font-bold text-gray-700 transition-all placeholder-gray-300 shadow-inner">
                                @error('name') <p
                                    class="text-[10px] text-red-500 font-black uppercase mt-1 ml-2 tracking-wider">
                                {{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-3">
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Digital
                                    Signature (Email)</label>
                                <input type="email" name="email" value="{{ old('email', $admin->email) }}" x-model="email"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-brand-500/20 focus:ring-4 focus:ring-brand-500/5 font-bold text-gray-700 transition-all placeholder-gray-300 shadow-inner">
                                @error('email') <p
                                    class="text-[10px] text-red-500 font-black uppercase mt-1 ml-2 tracking-wider">
                                {{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="group px-12 py-4 bg-gray-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl shadow-gray-200 flex items-center gap-3 active:scale-95">
                                <span>Commit Changes</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div
                    class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200">
                    <div class="flex items-center gap-5 mb-10 border-b border-gray-50 pb-8">
                        <div class="p-4 bg-amber-50 text-amber-600 rounded-3xl">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Security Credentials
                            </h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Maintain account
                                integrity with randomized entropy</p>
                        </div>
                    </div>

                    <form id="password-form" action="{{ route('admin.profile.password') }}" method="POST" class="space-y-8" @submit.prevent="handlePasswordSubmit">
                        @csrf
                        @method('PUT')
                        <div class="space-y-8">
                            <div class="space-y-3">
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Current
                                    Authorization Key</label>
                                <input type="password" name="current_password" placeholder="••••••••••••"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-amber-500/20 focus:ring-4 focus:ring-amber-500/5 font-bold text-gray-700 transition-all shadow-inner">
                                @error('current_password', 'updatePassword') <p
                                    class="text-[10px] text-red-500 font-black uppercase mt-1 ml-2 tracking-wider">
                                {{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">New
                                        Security Token</label>
                                    <input type="password" name="password" placeholder="••••••••••••"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-amber-500/20 focus:ring-4 focus:ring-amber-500/5 font-bold text-gray-700 transition-all shadow-inner">
                                    @error('password', 'updatePassword') <p
                                        class="text-[10px] text-red-500 font-black uppercase mt-1 ml-2 tracking-wider">
                                    {{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Verify
                                        Token</label>
                                    <input type="password" name="password_confirmation" placeholder="••••••••••••"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-amber-500/20 focus:ring-4 focus:ring-amber-500/5 font-bold text-gray-700 transition-all shadow-inner">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="group px-12 py-4 bg-amber-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] hover:bg-amber-700 transition-all shadow-xl shadow-amber-100 flex items-center gap-3 active:scale-95">
                                <span>Cycle Credentials</span>
                                <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Log Content -->
            <div class="lg:col-span-3 space-y-6" x-show="activeTab === 'activity'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-brand-100/10 border border-gray-100">
                    <div class="flex items-center justify-between mb-10 border-b border-gray-50 pb-8">
                        <div class="flex items-center gap-5">
                            <div class="p-4 bg-brand-50 text-brand-600 rounded-3xl shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Security Audit Log
                                </h3>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Historical record of
                                    administrator access and changes</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left">
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                                        Type</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                                        Activity Description</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                                        Source Network</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50 text-right">
                                        Event Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($activityLogs as $log)
                                    <tr class="group hover:bg-brand-50/30 transition-all duration-300">
                                        <td class="px-6 py-6">
                                            <span
                                                class="px-4 py-1.5 bg-brand-100 text-brand-800 text-[9px] font-black rounded-full uppercase tracking-widest shadow-sm">{{ $log->action }}</span>
                                        </td>
                                        <td class="px-6 py-6 font-bold text-gray-700 text-sm">{{ $log->description }}</td>
                                        <td class="px-6 py-6">
                                            <div
                                                class="flex items-center gap-2 group-hover:translate-x-1 transition-transform">
                                                <div
                                                    class="w-2 h-2 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.5)]">
                                                </div>
                                                <span class="text-xs font-black text-gray-400">{{ $log->ip_address }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-right">
                                            <p class="text-[11px] font-black text-gray-900 uppercase tracking-tighter">
                                                {{ $log->created_at->format('M d, Y') }}</p>
                                            <p class="text-[10px] font-bold text-brand-400 italic">
                                                {{ $log->created_at->format('h:i A') }}</p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center gap-5">
                                                <div class="p-6 bg-brand-50 rounded-full text-brand-200">
                                                    <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-black text-gray-300 uppercase tracking-widest">No
                                                    Security Logs Found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-10">
                            {{ $activityLogs->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats/Info Sidebar (Settings Tab Only) -->
            <div class="space-y-8 h-fit" x-show="activeTab === 'settings'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0">
                <div
                    class="bg-brand-900 p-10 rounded-[2.5rem] text-white shadow-2xl shadow-brand-900/40 relative overflow-hidden group">
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition-colors">
                    </div>
                    <div class="relative">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] mb-8 text-brand-300">Identity
                            Verification</h4>
                        <div class="space-y-8">
                            <div class="flex items-start gap-5">
                                <div class="p-3 bg-brand-800 rounded-2xl shadow-lg border border-brand-700">
                                    <svg class="w-5 h-5 text-brand-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 6.247a11.964 11.964 0 00-1 4.753c0 6.627 5.373 12 12 12s12-5.373 12-12a11.964 11.964 0 00-1-4.753l-.382-.016z" />
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-[10px] font-black uppercase text-brand-400 tracking-wider mb-1">
                                        Status</p>
                                    <p class="text-sm font-bold text-white leading-none">Verified Administrator</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-5">
                                <div class="p-3 bg-brand-800 rounded-2xl shadow-lg border border-brand-700">
                                    <svg class="w-5 h-5 text-brand-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-[10px] font-black uppercase text-brand-400 tracking-wider mb-1">
                                        Registration</p>
                                    <p class="text-sm font-bold text-white leading-none">
                                        {{ $admin->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-5">
                                <div class="p-3 bg-brand-800 rounded-2xl shadow-lg border border-brand-700">
                                    <svg class="w-5 h-5 text-brand-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-[10px] font-black uppercase text-brand-400 tracking-wider mb-1">Trust
                                        Score</p>
                                    <p class="text-sm font-bold text-white leading-none">High Security Profile</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-gray-50/0 to-gray-50 group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="relative">
                        <div class="flex items-center gap-5 mb-8">
                            <div
                                class="w-14 h-14 bg-gray-900 rounded-[1.25rem] flex items-center justify-center text-brand-400 shadow-xl">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-black uppercase tracking-tight text-gray-900">Support Node</h4>
                        </div>
                        <p class="text-sm text-gray-500 font-bold mb-8 leading-relaxed">Administrator accounts require
                            strict adherence to security protocols. For critical issues, contact the IT Command Center.
                        </p>
                        <a href="mailto:support@university.edu"
                            class="block w-full text-center py-5 bg-gray-900 hover:bg-black text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all transform hover:-translate-y-1 active:translate-y-0 shadow-xl shadow-gray-200">Request
                            Technical Support</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global OTP Modal for Admin -->
        <div x-show="otpRequired" style="display: none;" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-brand-900/60 backdrop-blur-md animate-in fade-in duration-300">
            <div class="bg-white rounded-[3rem] shadow-2xl max-w-md w-full p-10 relative animate-in zoom-in-95 duration-300 border border-brand-100">
                <div class="w-20 h-20 bg-brand-50 rounded-[2rem] flex items-center justify-center text-brand-600 mb-8 mx-auto shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-black text-center text-gray-900 uppercase tracking-tight mb-2">Administrative Verification</h3>
                <p class="text-xs text-center text-gray-400 font-bold uppercase tracking-wider mb-8">Authorizing critical system change</p>

                <div x-show="error" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-[10px] font-black uppercase tracking-widest" x-text="error"></div>

                <div class="space-y-10">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 text-center">Enter 6-Digit System Code</label>
                        <input type="text" x-model="otpCode" maxlength="6"
                            class="w-full text-center text-4xl font-black tracking-[0.6em] py-6 border-2 border-brand-50 rounded-[2rem] focus:ring-4 focus:ring-brand-500/5 focus:border-brand-500/20 bg-gray-50 shadow-inner"
                            placeholder="000000">
                        <p class="mt-4 text-[10px] text-center text-gray-400 font-bold italic">Checking: <span class="text-brand-600" x-text="otpType === 'email_update' ? email : originalEmail"></span></p>
                    </div>

                    <div class="flex flex-col gap-4">
                        <button type="button" @click="verifyOtp" :disabled="loading"
                            class="w-full py-5 bg-gray-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black shadow-2xl transition-all disabled:opacity-50 active:scale-95">
                            <span x-show="!loading">Authorize Transaction</span>
                            <span x-show="loading">Validating...</span>
                        </button>
                        
                        <button type="button" @click="otpRequired = false; error = ''; otpCode = ''" class="w-full py-2 text-[10px] text-gray-400 font-black uppercase tracking-widest hover:text-gray-900 transition-colors">
                            Abort Process
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>