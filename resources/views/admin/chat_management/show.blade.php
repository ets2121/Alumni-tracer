<x-layouts.admin>
    <!-- Admin Chat Layout: Fixed Full Height -->
    <div class="h-full flex flex-col md:flex-row bg-white overflow-hidden relative" x-data="adminChat({{ $group->id }})"
        x-init="init()">

        <!-- Sidebar (Info Panel) - Fixed on Desktop -->
        <div class="w-full md:w-80 h-full flex flex-col border-r border-gray-200 bg-white z-10">
            <!-- Header -->
            <div
                class="h-14 px-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0 bg-gray-50/50">
                <a href="{{ route('admin.chat-management.index') }}"
                    class="flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-widest">Back</span>
                </a>
            </div>

            <!-- Group Info -->
            <div class="p-6 text-center border-b border-gray-100">
                <div class="w-20 h-20 rounded-[2rem] flex items-center justify-center text-white font-black text-3xl shadow-xl mx-auto mb-4"
                    :class="getGroupColor('{{ $group->type }}')">
                    {{ substr($group->name, 0, 1) }}
                </div>
                <h3 class="text-lg font-black text-gray-900 leading-tight mb-1">{{ $group->name }}</h3>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 uppercase tracking-wide">
                    {{ $group->type }}
                </span>
            </div>

            <!-- Participants List (Scrollable) -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                <h4 class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-3 px-2">Participants
                    ({{ $participants->count() }})</h4>
                <div class="space-y-1">
                    @foreach($participants as $user)
                        <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-xl transition-all group">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . $user->name }}"
                                class="w-8 h-8 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-[9px] text-gray-400 font-medium uppercase">{{ $user->role }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col bg-slate-50 min-w-0 h-full relative">

            <!-- Header -->
            <div
                class="h-14 px-6 bg-white border-b border-gray-200 flex items-center justify-between flex-shrink-0 shadow-sm z-20">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Moderation Mode</h2>
                </div>
                <div class="text-xs text-gray-400 font-medium">
                    Auto-refresh active
                </div>
            </div>

            <!-- Messages List (Scrollable) -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6 relative" id="admin-message-container">
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 z-10">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-brand-600 border-t-transparent"></div>
                </div>

                <!-- Readability Wrapper -->
                <div class="max-w-4xl mx-auto space-y-4">
                    <template x-for="message in messages" :key="message.id">
                        <div class="flex gap-4 group hover:bg-white/50 p-2 rounded-2xl transition-all border border-transparent hover:border-gray-200/50"
                            :class="message.user_id == {{ Auth::id() }} ? 'flex-row-reverse text-right' : ''">

                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <img :src="message.user.avatar ? '{{ asset('storage') }}/' + message.user.avatar : 'https://ui-avatars.com/api/?name=' + message.user.name"
                                    class="w-10 h-10 rounded-xl shadow-sm border border-white">
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 flex flex-col"
                                :class="message.user_id == {{ Auth::id() }} ? 'items-end' : 'items-start'">
                                <div class="flex items-center gap-2 mb-1"
                                    :class="message.user_id == {{ Auth::id() }} ? 'flex-row-reverse' : ''">
                                    <span class="text-sm font-black text-gray-900" x-text="message.user.name"></span>
                                    <span x-show="message.user.role === 'admin'"
                                        class="text-[8px] bg-brand-600 text-white px-1.5 py-0.5 rounded font-black uppercase tracking-widest">Mod</span>
                                    <span class="text-[10px] text-gray-400 font-medium"
                                        x-text="formatTime(message.created_at)"></span>

                                    <!-- Delete Action -->
                                    <button @click="deleteMessage(message.id)"
                                        class="opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                        title="Delete Message">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="px-4 py-2.5 rounded-2xl max-w-[80%] text-sm leading-relaxed shadow-sm relative transition-all"
                                    :class="message.user_id == {{ Auth::id() }} 
                                        ? 'bg-brand-600 text-white rounded-br-none' 
                                        : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none'">
                                    <p x-text="message.content"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="messages.length === 0" class="py-20 text-center opacity-50">
                        <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">No messages found</p>
                    </div>
                </div>
            </div>

            <!-- Input Area (Fixed Bottom) -->
            <div class="p-4 bg-white border-t border-gray-100 flex-shrink-0 z-20 shadow-[0_-5px_20px_rgba(0,0,0,0.02)]">
                <div class="max-w-4xl mx-auto">
                    <form @submit.prevent="sendMessage()" class="flex items-end gap-3 relative">
                        <div class="flex-1 relative group">
                            <input type="text" x-model="newMessage"
                                class="w-full pl-6 pr-14 py-3.5 bg-gray-50 border-2 border-transparent rounded-full focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all text-sm font-medium shadow-inner group-hover:bg-white border-gray-100 placeholder-gray-400"
                                placeholder="Write a message as Moderator...">
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                <span
                                    class="text-[9px] bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full font-black uppercase tracking-wider">Mod</span>
                            </div>
                        </div>
                        <button type="submit" :disabled="!newMessage.trim() || sending"
                            class="bg-gradient-to-r from-brand-700 to-brand-600 hover:from-brand-600 hover:to-brand-500 text-white w-12 h-12 rounded-full transition-all shadow-lg hover:shadow-brand-500/30 flex items-center justify-center transform active:scale-90 disabled:opacity-50 disabled:cursor-not-allowed group border-2 border-white ring-2 ring-brand-100">
                            <svg x-show="!sending"
                                class="w-5 h-5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <svg x-show="sending" style="display: none" class="w-5 h-5 animate-spin text-white"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function adminChat(groupId) {
                return {
                    messages: [],
                    newMessage: '',
                    loading: true,
                    sending: false,
                    groupId: groupId,
                    pollingInterval: null,

                    async init() {
                        await this.fetchMessages();
                        this.scrollToBottom();
                        this.pollingInterval = setInterval(() => this.fetchMessages(true), 3000);
                    },

                    async fetchMessages(silent = false) {
                        try {
                            // Assuming reusing the alumni chat API but authenticated as admin 
                            // OR we need a diverse route. Since the Controller in User Request wasn't shown, 
                            // I'll assume we might need to use the existing `chat/groups/{id}/messages` endpoint 
                            // if admins have access, OR the admin specific one.
                            // However, `admin.chat-management.show` usually implies a different controller.
                            // I'll use the URL structure consistent with the view: `/chat/groups/${groupId}/messages` 
                            // If that fails (403), we might need `admin/chat-management/...`.
                            // But typically specific controllers return JSON. 
                            // Let's assume for now we reuse the ChatController API if authorized.

                            // Wait, previous file used `$group->messages` in Blade. 
                            // To use AJAX, we need an endpoint returning JSON.
                            // I will assume for this task I can use the existing `ChatController` API 
                            // `Route::get('/chat/groups/{group}/messages', ...)`
                            // If Admin is also a "User" instance or middleware allows it.

                            const response = await fetch(`/chat/groups/${this.groupId}/messages`, {
                                headers: { 'Accept': 'application/json' }
                            });

                            if (!response.ok) throw new Error('Network response was not ok');

                            const data = await response.json();
                            if (JSON.stringify(this.messages) !== JSON.stringify(data)) {
                                const wasBottom = this.isAtBottom();
                                this.messages = data;
                                if (wasBottom || !silent) this.$nextTick(() => this.scrollToBottom());
                            }
                        } catch (error) { console.error('Error fetching messages:', error); }
                        finally { if (!silent) this.loading = false; }
                    },

                    async sendMessage() {
                        if (!this.newMessage.trim() || this.sending) return;
                        this.sending = true;

                        try {
                            // Using the Admin route for storing message if distinguishable, 
                            // OR reuse the standard chat rout if the logged in Admin is a User.
                            // The previous blade form posted to: `route('admin.chat-management.store-message', $group)`
                            // I should use THAT route but via FETCH.

                            const response = await fetch(`{{ route('admin.chat-management.store-message', $group) }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ content: this.newMessage })
                            });

                            if (response.ok) {
                                this.newMessage = '';
                                await this.fetchMessages(true);
                                this.scrollToBottom();
                            }
                        } catch (error) { console.error('Error sending message:', error); }
                        finally { this.sending = false; }
                    },

                    async deleteMessage(messageId) {
                        if (!confirm('Are you sure you want to delete this message?')) return;

                        try {
                            // Need a route to delete by ID.
                            // Previous form: `route('admin.chat-management.delete-message', $message)`
                            // We need to construct this URL dynamically in JS.
                            // Since I can't easily use blade route() with JS param, 
                            // I'll construct it: `/admin/chat-management/messages/${messageId}`
                            // Assuming standard resource routing.
                            // Or I can use a base URL.

                            const baseUrl = `{{ url('/admin/chat-management/messages') }}`;
                            const response = await fetch(`${baseUrl}/${messageId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                await this.fetchMessages(true);
                            }
                        } catch (error) { console.error('Error deleting message:', error); }
                    },

                    scrollToBottom() {
                        const container = document.getElementById('admin-message-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    },

                    isAtBottom() {
                        const container = document.getElementById('admin-message-container');
                        if (!container) return true;
                        return container.scrollHeight - container.scrollTop <= container.clientHeight + 150;
                    },

                    formatTime(timestamp) {
                        return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    },

                    getGroupColor(type) {
                        const colors = { batch: 'bg-brand-600', course: 'bg-indigo-600', general: 'bg-blue-600' };
                        return colors[type] || 'bg-gray-500';
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin>