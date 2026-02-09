export default () => ({
    search: new URLSearchParams(window.location.search).get('search') || '',
    loading: false,

    init() {
        this.$watch('search', () => this.fetchData());
        this.interceptPagination();
    },

    async fetchData(url = null) {
        this.loading = true;
        if (!url) {
            url = new URL(window.location.origin + window.location.pathname);
            if (this.search) url.searchParams.set('search', this.search);
        }

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            document.getElementById('list-wrapper').innerHTML = html;
            window.history.pushState({}, '', url);
            this.interceptPagination();
        } catch (error) {
            console.error('Failed to fetch albums:', error);
        } finally {
            this.loading = false;
        }
    },

    interceptPagination() {
        const wrapper = document.getElementById('list-wrapper');
        if (!wrapper) return;

        wrapper.querySelectorAll('.pagination-container a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.fetchData(e.currentTarget.href);
            });
        });
    }
});
