export default () => ({
    search: '',
    loading: false,
    saving: false,
    modalOpen: false,
    modalTitle: '',
    deleteModalOpen: false,
    itemToDelete: '',
    deleteUrl: '',

    init() {
        this.$watch('search', () => this.fetchData());
        this.interceptPagination();
        this.fetchData();

        // Use a unique event listener or scope it to the element
        const formSubmitHandler = async (e) => {
            if (e.target.id === 'course-form') {
                e.preventDefault();
                await this.saveForm(e.target);
            }
        };

        // Important: In SPA, we might need to remove this listener on cleanup, 
        // but for now we scope it to the document and check the target ID.
        document.addEventListener('submit', formSubmitHandler);
    },

    async fetchData(url = null) {
        this.loading = true;
        if (!url) {
            url = new URL(window.location.origin + window.location.pathname);
            if (this.search) url.searchParams.set('search', this.search);
        }
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const tableWrapper = document.getElementById('table-wrapper');
            if (tableWrapper) {
                tableWrapper.innerHTML = html;
                window.history.pushState({}, '', url);
                this.interceptPagination();
            }
        } catch (error) {
            console.error('Fetch failed:', error);
        } finally {
            this.loading = false;
        }
    },

    interceptPagination() {
        document.querySelectorAll('.pagination-container a').forEach(link => {
            link.onclick = (e) => {
                e.preventDefault();
                this.fetchData(e.currentTarget.href);
            };
        });
    },

    async openModal(url, title) {
        this.modalTitle = title;
        this.modalOpen = true;
        const modalContent = document.getElementById('modal-content');
        if (modalContent) {
            modalContent.innerHTML = '<div class="flex justify-center py-20"><svg class="animate-spin h-10 w-10 text-brand-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            try {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await response.text();
                modalContent.innerHTML = html;
            } catch (error) {
                this.modalOpen = false;
            }
        }
    },

    closeModal() {
        this.modalOpen = false;
    },

    async saveForm(form) {
        this.saving = true;
        const formData = new FormData(form);
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
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
                this.showFlash(data.success);
            } else if (response.status === 422) {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.querySelector(`.error-message[data-field="${field}"]`);
                        if (errorEl) errorEl.textContent = data.errors[field][0];
                    });
                    this.showFlash('Please correct the highlighted errors.', 'error');
                } else if (data.error) {
                    this.showFlash(data.error, 'error');
                }
            } else {
                const errorData = await response.json().catch(() => ({}));
                this.showFlash(errorData.message || 'Operation failed. Please check inputs.', 'error');
            }
        } catch (error) {
            console.error('Save failed:', error);
        } finally {
            this.saving = false;
        }
    },

    showFlash(message, type = 'success') {
        const flash = document.getElementById('flash-message');
        if (flash) {
            const bgClass = type === 'success' ? 'bg-brand-600/90' : 'bg-red-600/90';
            flash.innerHTML = `<div class="${bgClass} backdrop-blur-md text-white px-6 py-4 rounded-2xl mb-6 shadow-2xl flex items-center gap-3 animate-fade-in-down border border-white/20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold">${message}</span></div>`;
            setTimeout(() => {
                if (flash) flash.innerHTML = '';
            }, 4000);
        }
    }
});
