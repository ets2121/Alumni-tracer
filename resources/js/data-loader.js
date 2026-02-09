
export default (url, cacheKey = null) => ({
    data: null,
    loading: true,
    error: null,
    key: cacheKey || url,

    init() {
        this.load();
    },

    async load() {
        // 1. Check Cache
        const cached = sessionStorage.getItem(`cache_${this.key}`);
        if (cached) {
            try {
                const parsed = JSON.parse(cached);
                // Check if valid (e.g., expiry could be added here)
                this.data = parsed;
                this.loading = false;
            } catch (e) {
                console.warn('Cache parse error', e);
                sessionStorage.removeItem(`cache_${this.key}`);
            }
        }

        // 2. Fetch Fresh Data (Stale-While-Revalidate)
        // If we have data, we're not technically "loading" in the UI blocking sense,
        // but we might want a background indicator. For now, keep loading=true if no cache.
        if (!this.data) {
            this.loading = true;
        }

        try {
            // Append wantsJson or format=json to header or query
            // We'll use a header to keep URL clean, or a query param if controller expects it logic explicitly
            const response = await axios.get(this.key, {
                params: { format: 'json' },
                headers: { 'X-SPA-Data-Request': 'true' }
            });

            this.data = response.data;
            this.error = null;

            // Update Cache
            sessionStorage.setItem(`cache_${this.key}`, JSON.stringify(this.data));

        } catch (err) {
            console.error('Data fetch error:', err);
            this.error = 'Failed to load data.';
            // Only clear data if we really want to force validation, 
            // but keeping stale data might be better than empty state if network fails?
            // tailored decision: keep stale if exists, show error toast
            if (!this.data) {
                // If we had nothing, we stayed empty
            } else {
                // We have stale data, maybe show a toast?
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Could not refresh data', type: 'error' }
                }));
            }
        } finally {
            this.loading = false;
        }
    },

    refresh() {
        this.loading = true;
        this.load();
    }
});
