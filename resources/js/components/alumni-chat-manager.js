export default (config) => ({
    sidebarOpen: false,
    groups: [],
    messages: [],
    activeGroup: null,
    loadingGroups: false,
    loadingMessages: false,
    newMessage: '',
    sending: false,
    pollingInterval: null,
    groupsUrl: config.groupsUrl,

    async init() {
        await this.fetchGroups();
    },

    async fetchGroups() {
        this.loadingGroups = true;
        try {
            const response = await fetch(this.groupsUrl);
            this.groups = await response.json();
        } catch (error) {
            console.error('Error fetching groups:', error);
        } finally {
            this.loadingGroups = false;
        }
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
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
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
        } catch (error) {
            console.error('Error sending message:', error);
        } finally {
            this.sending = false;
        }
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
    },

    destroy() {
        if (this.pollingInterval) clearInterval(this.pollingInterval);
    }
});
