export default (config) => ({
    ...window.dataLoader(config.baseUrl),
    search: config.search || '',
    modalOpen: false,
    modalTitle: '',
    editMode: false,
    saving: false,
    actionUrl: config.storeUrl,
    formData: {
        id: '',
        name: '',
        email: '',
        role: 'admin',
        department_name: '',
        password: '',
        password_confirmation: ''
    },

    init() {
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

    fetchData(url) {
        this.key = url;
        this.load();
        window.history.pushState({}, '', url);
    },

    openModal(url, title, userId = null) {
        this.modalTitle = title;
        this.modalOpen = true;
        this.resetForm();

        if (userId) {
            this.editMode = true;
            this.actionUrl = url;
            this.fetchUser(userId);
        } else {
            this.editMode = false;
            this.actionUrl = config.storeUrl;
        }
    },

    async fetchUser(id) {
        try {
            const response = await axios.get(`${config.baseUrl}/${id}/edit`);
            const user = response.data;
            this.formData.id = user.id;
            this.formData.name = user.name;
            this.formData.email = user.email;
            this.formData.role = user.role;
            this.formData.department_name = user.department_name ?? '';
        } catch (error) {
            console.error('Failed to fetch user:', error);
            window.showToast('Failed to load user details', 'error');
        }
    },

    resetForm() {
        this.formData = {
            id: '',
            name: '',
            email: '',
            role: 'admin',
            department_name: '',
            password: '',
            password_confirmation: ''
        };
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    },

    closeModal() { this.modalOpen = false; },

    async saveForm() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        this.saving = true;

        try {
            const method = this.editMode ? 'put' : 'post';
            const response = await axios[method](this.actionUrl, this.formData);

            if (response.data.success) {
                this.closeModal();
                this.refresh();
                window.showToast(response.data.success);
            }
        } catch (error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                Object.keys(errors).forEach(field => {
                    const errorEl = document.querySelector(`.error-message[data-field='${field}']`);
                    if (errorEl) errorEl.textContent = errors[field][0];
                });
            } else {
                console.error('Save failed:', error);
                window.showToast('An unexpected error occurred.', 'error');
            }
        } finally {
            this.saving = false;
        }
    }
});
