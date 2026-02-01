<x-app-layout>
    <!-- Chat Wrapper (Full Height, No Global Scroll) -->
    <div class="h-full flex bg-white overflow-hidden relative border-t border-gray-200" x-data="chatSystem()"
        x-init="init()">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/80 z-40 md:hidden"></div>

        <!-- Sidebar: Room List (Fixed Width, Scrollable Content) -->
        <div class="w-full md:w-80 h-full flex flex-col border-r border-gray-200 bg-white transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static absolute z-50 rounded-r-2xl md:rounded-r-none shadow-2xl md:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Sidebar Header -->
            <div
                class="h-14 px-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0 bg-gray-50/80 backdrop-blur-sm">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Messages</h2>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Scrollable Group List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
                <template x-for="group in groups" :key="group.id">
                    <button @click="selectGroup(group); sidebarOpen = false;"
                        class="w-full text-left p-2 rounded-lg transition-all duration-200 group flex items-center gap-3 relative"
                        :class="activeGroup?.id === group.id ? 'bg-brand-50 shadow-sm ring-1 ring-brand-100' : 'hover:bg-gray-50'">

                        <!-- Compact Avatar -->
                        <div class="relative flex-shrink-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-sm bg-gradient-to-br"
                                :class="getGroupColor(group.type)" x-text="group.name.substring(0, 1)">
                            </div>
                            <div x-show="group.unread_count > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold h-3.5 min-w-[0.875rem] px-1 flex items-center justify-center rounded-full ring-2 ring-white"
                                x-text="group.unread_count"></div>
                        </div>

                        <!-- Compact Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <h4 class="text-xs font-bold text-gray-900 truncate" x-text="group.name"></h4>
                                <span class="text-[9px] text-gray-400 font-medium whitespace-nowrap"
                                    x-text="formatTime(group.latest_message?.created_at)"></span>
                            </div>
                            <p class="text-[10px] text-gray-500 truncate leading-tight"
                                x-text="group.latest_message ? group.latest_message.user.name.split(' ')[0] + ': ' + group.latest_message.content : (group.description || 'Start chatting...')">
                            </p>
                        </div>
                    </button>
                </template>

                <div x-show="groups.length === 0 && !loadingGroups" class="py-10 text-center">
                    <p class="text-xs text-gray-400">No conversations yet.</p>
                </div>
            </div>
        </div>

        <!-- Main Chat Panel (Flex Column, Fills Remaining Space) -->
        <div class="flex-1 flex flex-col bg-gray-50 min-w-0 h-full relative z-0">

            <!-- Default State -->
            <div x-show="!activeGroup"
                class="flex-1 flex flex-col items-center justify-center p-8 text-center text-gray-400">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-sm font-medium">Select a channel to start chatting</p>
                <button @click="sidebarOpen = true"
                    class="md:hidden mt-4 text-xs font-bold text-brand-600 hover:underline">
                    View Channels
                </button>
            </div>

            <!-- Active Chat Interface -->
            <div x-show="activeGroup" class="flex flex-col h-full w-full relative" style="display: none;">

                <!-- Chat Header (Fixed) -->
                <div
                    class="h-14 px-4 bg-white/90 backdrop-blur border-b border-gray-200 flex items-centerjustify-between flex-shrink-0 z-20 shadow-sm relative">
                    <div class="flex items-center gap-3 w-full">
                        <button @click="sidebarOpen = true"
                            class="md:hidden p-1 -ml-1 text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-sm"
                            :class="getGroupColor(activeGroup?.type || 'general')"
                            x-text="activeGroup?.name.substring(0, 1)">
                        </div>
                        <div class="overflow-hidden flex-1">
                            <h3 class="font-bold text-gray-900 truncate text-sm" x-text="activeGroup?.name"></h3>
                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-wide truncate"
                                x-text="activeGroup?.type + ' Channel'"></p>
                        </div>
                    </div>
                </div>

                <!-- Messages List (Scrollable, Fills Flex Space) -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-4 bg-slate-50 relative"
                    id="message-container">
                    <div x-show="loadingMessages"
                        class="absolute inset-0 flex items-center justify-center bg-white/50 z-10">
                        <div class="animate-spin rounded-full h-6 w-6 border-2 border-brand-600 border-t-transparent">
                        </div>
                    </div>

                    <!-- Readability Wrapper -->
                    <div class="max-w-4xl mx-auto space-y-4">
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex gap-2.5 max-w-2xl group"
                                :class="message.user_id == {{ Auth::id() }} ? 'ml-auto flex-row-reverse' : ''">

                                <!-- Avatar -->
                                <div class="flex-shrink-0 self-end" x-show="message.user_id != {{ Auth::id() }}">
                                    <div class="w-7 h-7 rounded-lg bg-gray-200 shadow-sm overflow-hidden">
                                        <img :src="message.user.avatar ? '{{ asset('storage') }}/' + message.user.avatar : 'https://ui-avatars.com/api/?name=' + message.user.name"
                                            class="w-full h-full object-cover">
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="flex flex-col min-w-0"
                                    :class="message.user_id == {{ Auth::id() }} ? 'items-end' : 'items-start'">
                                    <div class="flex items-baseline gap-2 mb-1 px-1">
                                        <span class="text-[10px] font-bold text-gray-600" x-text="message.user.name"
                                            x-show="message.user_id != {{ Auth::id() }}"></span>
                                    </div>
                                    <div class="px-3.5 py-2 rounded-2xl text-sm leading-relaxed shadow-sm max-w-[260px] sm:max-w-md break-words relative transition-all"
                                        :class="message.user_id == {{ Auth::id() }} 
                                        ? 'bg-brand-600 text-white rounded-br-none' 
                                        : 'bg-white border border-gray-200/60 text-gray-800 rounded-bl-none'">
                                        <p x-text="message.content" class="text-[13px]"></p>
                                    </div>
                                    <span
                                        class="text-[9px] text-gray-400 font-medium px-1 mt-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                        x-text="formatTime(message.created_at)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="h-2"></div>
                </div>

                <!-- Input Area (Fixed Bottom) -->
                <div class="p-3 bg-white border-t border-gray-200 flex-shrink-0 z-20">
                    <form @submit.prevent="sendMessage()" class="flex items-end gap-2 max-w-4xl mx-auto relative">
                        <div
                            class="flex-1 bg-gray-50 rounded-xl border border-gray-200 focus-within:bg-white focus-within:border-brand-300 focus-within:ring-2 focus-within:ring-brand-500/10 transition-all">
                            <textarea x-model="newMessage" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                                placeholder="Type a message..." rows="1"
                                class="w-full bg-transparent border-0 focus:ring-0 text-sm py-3 px-4 resize-none max-h-32 custom-scrollbar placeholder-gray-400 leading-normal"></textarea>
                        </div>
                        <button type="submit" :disabled="!newMessage.trim() || sending"
                            class="p-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl shadow-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex-shrink-0 transform active:scale-95 group">
                            <svg x-show="!sending"
                                class="w-5 h-5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <svg x-show="sending" style="display: none;" class="w-5 h-5 animate-spin" fill="none"
                                viewBox="0 0 24 24">
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

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function chatSystem() {
                return {
                    sidebarOpen: false,
                    groups: [],
                    messages: [],
                    activeGroup: null,
                    loadingGroups: false,
                    loadingMessages: false,
                    newMessage: '',
                    sending: false,
                    pollingInterval: null,

                    async init() {
                        await this.fetchGroups();
                    },

                    async fetchGroups() {
                        this.loadingGroups = true;
                        try {
                            const response = await fetch('{{ route('chat.groups') }}');
                            this.groups = await response.json();
                        } catch (error) { console.error(error); }
                        finally { this.loadingGroups = false; }
                    },

                    async selectGroup(group) {
                        if (this.activeGroup?.id === group.id) return;

                        this.activeGroup = group;
                        this.messages = [];
                        this.loadingMessages = true;
                        if (this.pollingInterval) clearInterval(this.pollingInterval);

                        await this.fetchMessages();
                        this.loadingMessages = false;
                        this.scrollToBottom();

                        this.pollingInterval = setInterval(() => this.fetchMessages(true), 3000);
                    },

                    async fetchMessages(silent = false) {
                        if (!this.activeGroup) return;
                        try {
                            const response = await fetch(`/chat/groups/${this.activeGroup.id}/messages`);
                            const data = await response.json();
                            if (JSON.stringify(this.messages) !== JSON.stringify(data)) {
                                const wasBottom = this.isAtBottom();
                                this.messages = data;
                                if (wasBottom || !silent) this.$nextTick(() => this.scrollToBottom());
                            }
                        } catch (error) { console.error(error); }
                    },

                    async sendMessage() {
                        if (!this.newMessage.trim() || !this.activeGroup || this.sending) return;
                        const content = this.newMessage;
                        this.newMessage = '';
                        this.sending = true;

                        try {
                            await fetch(`/chat/groups/${this.activeGroup.id}/messages`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ content: content })
                            });
                            await this.fetchMessages(true);
                            this.scrollToBottom();
                        } catch (error) { console.error(error); }
                        finally { this.sending = false; }
                    },

                    scrollToBottom() {
                        const container = document.getElementById('message-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    },

                    isAtBottom() {
                        const container = document.getElementById('message-container');
                        if (!container) return true;
                        return container.scrollHeight - container.scrollTop <= container.clientHeight + 150;
                    },

                    formatTime(timestamp) {
                        if (!timestamp) return '';
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
</x-app-layout>