export default () => ({
    category: 'all',
    loading: false,

    init() {
        // Optional: Infinite scroll observer could go here
    },

    setCategory(cat) {
        this.category = cat;
        this.fetchFeed();
    },

    async fetchFeed() {
        this.loading = true;
        const url = new URL(window.location.href);
        if (this.category !== 'all') {
            url.searchParams.set('type', this.category);
        } else {
            url.searchParams.delete('type');
        }

        try {
            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await res.text();
            document.getElementById('feed-container').innerHTML = html;
            window.history.pushState({}, '', url.toString());
        } catch (e) {
            console.error(e);
        } finally {
            this.loading = false;
        }
    }
});
