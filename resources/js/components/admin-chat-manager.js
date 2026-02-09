export default (config) => ({
    messages: [],
    newMessage: '',
    loading: true,
    sending: false,
    groupId: config.groupId,
    pollingInterval: null,
    storeUrl: config.storeUrl,
    deleteBaseUrl: config.deleteBaseUrl,

    async init() {
        await this.fetchMessages();
        this.scrollToBottom();
        this.pollingInterval = setInterval(() => this.fetchMessages(true), 3000);
    },

    async fetchMessages(silent = false) {
        try {
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
        } catch (error) {
            console.error('Error fetching messages:', error);
        } finally {
            if (!silent) this.loading = false;
        }
    },

    async sendMessage() {
        if (!this.newMessage.trim() || this.sending) return;
        this.sending = true;

        try {
            const response = await fetch(this.storeUrl, {
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
        } catch (error) {
            console.error('Error sending message:', error);
        } finally {
            this.sending = false;
        }
    },

    async deleteMessage(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) return;

        try {
            const response = await fetch(`${this.deleteBaseUrl}/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                await this.fetchMessages(true);
            }
        } catch (error) {
            console.error('Error deleting message:', error);
        }
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
    },

    // Note: Alpine 3.x uses $cleanup, but if it doesn't work standardly, 
    // we can use a custom destroyer or just let it poll if the user navigates.
    // However, setInterval can accumulate if not cleared.
    // In SPA, if we navigate away, the DOM node is removed.
    // Alpine's x-init logic can sometimes handle this or we use $destroy.
    destroy() {
        if (this.pollingInterval) clearInterval(this.pollingInterval);
    }
});
