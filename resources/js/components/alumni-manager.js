export default (config) => ({
    ...window.dataLoader(config.baseUrl),
    search: config.search || '',
    sort: config.sort || 'name',
    direction: config.direction || 'asc',
    modalOpen: false,
    modalTitle: '',
    saving: false,

    init() {
        // Initialize data loading
        this.load();
        this.initManager();
    },

    initManager() {
        this.$watch('search', (val) => {
            const url = new URL(this.key, window.location.origin);
            if (val) url.searchParams.set('search', val);
            this.key = url.toString();
            this.load();
        });
    },

    sortBy(field) {
        if (this.sort === field) {
            this.direction = this.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.sort = field;
            this.direction = 'asc';
        }
        const url = new URL(this.key, window.location.origin);
        url.searchParams.set('sort', this.sort);
        url.searchParams.set('direction', this.direction);
        this.key = url.toString();
        this.load();
    },

    fetchData(url) {
        this.key = url;
        this.load();
        window.history.pushState({}, '', url);
    },

    async openModal(url, title, isEdit = false) {
        this.modalTitle = title;
        this.modalOpen = true;
        const modalContent = document.getElementById('modal-content');
        if (modalContent) {
            modalContent.innerHTML = '<div class=\'flex justify-center py-20\'><svg class=\'animate-spin h-10 w-10 text-brand-600\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg></div>';
        }

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            if (modalContent) {
                modalContent.innerHTML = html;
                const form = modalContent.querySelector('form');
                if (form) {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.submitAlumniForm(e);
                    });
                }
            }
        } catch (error) {
            console.error(error);
            this.modalOpen = false;
        }
    },

    closeModal() { this.modalOpen = false; },

    async submitAlumniForm(event) {
        const form = event.target;
        const formData = new FormData(form);
        this.saving = true;

        try {
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                window.showToast(data.success);
                this.closeModal();
                this.refresh();
            } else if (data.errors) {
                window.showToast(Object.values(data.errors).flat()[0], 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('An error occurred.', 'error');
        } finally {
            this.saving = false;
        }
    }
});
