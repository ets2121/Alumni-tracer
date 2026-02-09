export default (initialGroups = []) => ({
    modalOpen: false,
    modalTitle: '',
    saving: false,
    search: '',
    filterType: 'all',
    groups: initialGroups,

    init() {
        console.log('Group Manager Polished initialized');
    },

    get filteredGroups() {
        return this.groups.filter(g => {
            const matchesSearch = g.name.toLowerCase().includes(this.search.toLowerCase()) ||
                g.type.toLowerCase().includes(this.search.toLowerCase());
            const matchesType = this.filterType === 'all' || g.type === this.filterType;
            return matchesSearch && matchesType;
        });
    },

    async openModal(url, title) {
        this.modalTitle = title;
        this.modalOpen = true;

        // Wait for next tick so the modal shell is rendered before we try to find the content div
        this.$nextTick(async () => {
            const contentDiv = document.getElementById('modal-content');
            if (!contentDiv) return;

            contentDiv.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="relative w-16 h-16 mb-4">
                        <div class="absolute inset-0 border-4 border-brand-100 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-brand-600 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Loading Configuration...</p>
                </div>
            `;
            try {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                contentDiv.innerHTML = await response.text();

                const form = document.getElementById('chat-group-form');
                if (form) {
                    form.onsubmit = async (e) => {
                        e.preventDefault();
                        await this.saveForm(e.target);
                    };
                }
            } catch (error) {
                console.error('Modal load error:', error);
                this.modalOpen = false;
            }
        });
    },

    closeModal() {
        this.modalOpen = false;
    },

    async saveForm(form) {
        this.saving = true;
        const formData = new FormData(form);
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Save failed:', error);
        } finally {
            this.saving = false;
        }
    }
});
