import _ from 'lodash';

export default () => ({
    newsEvents: { data: [], links: [], meta: {} },
    search: '',
    currentTab: 'all',
    sort: 'latest',
    loading: true,

    // Modal State
    modalOpen: false,
    modalTitle: '',
    saving: false,

    init() {
        // Initial Fetch
        this.fetchData();

        // Watchers
        this.$watch('search', () => this.debouncedFetch());
    },

    debouncedFetch: _.debounce(function () {
        this.fetchData();
    }, 300),

    setTab(tab) {
        this.currentTab = tab;
        this.fetchData();
    },

    async fetchData(url = null) {
        this.loading = true;

        // Construct URL
        let fetchUrl = url;
        if (!fetchUrl) {
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.currentTab !== 'all') params.append('type', this.currentTab);
            if (this.sort !== 'latest') params.append('sort', this.sort);
            fetchUrl = `${window.location.pathname}?${params.toString()}`;

            // Update Browser URL (History API)
            window.history.replaceState(null, '', fetchUrl);
        }

        try {
            const response = await fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            // Handle JSON response for Table
            const data = await response.json();
            this.newsEvents = data;

        } catch (error) {
            console.error('Fetch failed:', error);
        } finally {
            this.loading = false;
        }
    },

    // --- Modal Logic (HTML over Wire for Forms) ---
    async openModal(url, title) {
        this.modalTitle = title;
        this.modalOpen = true;
        document.getElementById('modal-content').innerHTML = '<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-600"></div></div>';

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            document.getElementById('modal-content').innerHTML = html;

            // Re-bind Scripts (if any inline scripts in modal)
            // Ideally forms should use x-data, but for now we manually bind submit
            const form = document.getElementById('news-form') || document.querySelector('#modal-content form');
            if (form) {
                form.onsubmit = (e) => {
                    e.preventDefault();
                    this.saveForm(e.target);
                };
            }
        } catch (error) {
            console.error('Modal load failed:', error);
            this.modalOpen = false;
        }
    },

    closeModal() {
        this.modalOpen = false;
        document.getElementById('modal-content').innerHTML = ''; // Cleanup
    },

    async saveForm(form) {
        this.saving = true;
        const formData = new FormData(form);

        // Clear previous errors
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
                this.fetchData(); // Refresh table
                this.showFlash(data.success || 'Saved successfully');
            } else if (response.status === 422) {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const cleanField = field.split('.')[0];
                        // Try to find error container
                        // Assuming standard Blade error display <p class="error-message" data-field="...">
                        // Or we might need to inject them. 
                        // For this refactor, let's assume `_form` handles errors or we alert them.
                        // Better: Use a dedicated error display logic if `_form` is not updated.
                        alert(`Error in ${cleanField}: ${data.errors[field][0]}`);
                    });
                }
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Save failed:', error);
            alert('Something went wrong. Please try again.');
        } finally {
            this.saving = false;
        }
    },

    showFlash(message, type = 'success') {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { message: message, type: type }
        }));
    },

    // --- Helpers ---
    formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
});
