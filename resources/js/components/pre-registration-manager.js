export default (config) => ({
    tab: config.tab || 'pending',
    search: config.search || '',
    sort: config.sort || 'created_at',
    direction: config.direction || 'desc',
    loading: false,
    modalOpen: false,
    modalTitle: '',
    saving: false,

    init() {
        this.$watch('search', () => this.fetchData());
        this.$watch('tab', () => this.fetchData());
        this.interceptPagination();
    },

    setTab(newTab) {
        this.tab = newTab;
    },

    async fetchData(url = null) {
        this.loading = true;
        if (!url) {
            url = new URL(window.location.origin + window.location.pathname);
            url.searchParams.set('tab', this.tab);
            if (this.search) url.searchParams.set('search', this.search);
            url.searchParams.set('sort', this.sort);
            url.searchParams.set('direction', this.direction);
        }
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();

            // Note: We need a way to target the wrapper from here. 
            // Usually this.$refs or a specific ID.
            const wrapper = document.getElementById('table-wrapper');
            if (wrapper) {
                wrapper.innerHTML = html;
            }

            window.history.pushState({}, '', url);
            this.interceptPagination();
        } catch (error) {
            console.error('Fetch failed:', error);
        } finally {
            this.loading = false;
        }
    },

    interceptPagination() {
        // We use nextTick to ensure the DOM is updated before we query for links
        this.$nextTick(() => {
            document.querySelectorAll('.pagination-container a, .pagination a').forEach(link => {
                link.onclick = (e) => {
                    e.preventDefault();
                    this.fetchData(e.currentTarget.href);
                };
            });
        });
    },

    async openModal(url, title) {
        this.modalTitle = title;
        this.modalOpen = true;
        const target = document.getElementById('modal-content');
        if (target) {
            target.innerHTML = '<div class="flex justify-center py-20"><svg class="animate-spin h-10 w-10 text-brand-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        }
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            if (target) target.innerHTML = html;
        } catch (error) {
            this.modalOpen = false;
        }
    },

    closeModal() { this.modalOpen = false; },

    async saveReview(form) {
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
            const data = await response.json();
            if (response.ok) {
                this.closeModal();
                this.fetchData();
                window.showToast(data.success);
            }
        } catch (error) {
            console.error('Save failed:', error);
        } finally {
            this.saving = false;
        }
    }
});
