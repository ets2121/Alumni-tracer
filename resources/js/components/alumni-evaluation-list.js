export default () => ({
    forms: [],
    loading: true,

    init() {
        this.fetchForms();
    },

    async fetchForms() {
        try {
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.forms = data;
        } catch (error) {
            console.error('Failed to fetch evaluations:', error);
        } finally {
            this.loading = false;
        }
    }
});
