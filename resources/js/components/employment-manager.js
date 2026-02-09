export default (config = {}) => ({
    isOpen: false,
    isEdit: false,
    updateUrl: '',
    employmentStatus: config.currentStatus || '',
    form: {
        company_name: '',
        position: '',
        industry: '',
        location: '',
        start_date: '',
        end_date: '',
        is_current: false,
        description: ''
    },

    init() {
        // Any initialization logic if needed
    },

    openModal() {
        this.isOpen = true;
        this.isEdit = false;
        this.resetForm();
    },

    editHistory(history) {
        this.isOpen = true;
        this.isEdit = true;
        this.updateUrl = `/employment/${history.id}`;
        this.form = {
            company_name: history.company_name,
            position: history.position,
            industry: history.industry,
            location: history.location,
            start_date: history.start_date ? history.start_date.split('T')[0] : '',
            end_date: history.end_date ? history.end_date.split('T')[0] : '',
            is_current: !!history.is_current,
            description: history.description
        };
    },

    closeModal() {
        this.isOpen = false;
        this.resetForm();
    },

    resetForm() {
        this.form = {
            company_name: '',
            position: '',
            industry: '',
            location: '',
            start_date: '',
            end_date: '',
            is_current: false,
            description: ''
        };
    },

    async deleteHistory(url) {
        if (!confirm('Are you sure you want to remove this record?')) return;

        try {
            const response = await axios.delete(url);
            if (response.data.success || response.data.status === 'success') {
                if (window.showToast) {
                    window.showToast(response.data.success || 'Record deleted successfully');
                }
                // Since this is a full page reload in the current logic, 
                // we'll stick to that if needed, or refresh?
                // The original logic used form.submit() which causes a reload.
                window.location.reload();
            }
        } catch (error) {
            console.error('Delete error:', error);
            if (window.showToast) {
                window.showToast('Failed to delete history record', 'error');
            }
        }
    }
});
