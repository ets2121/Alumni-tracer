export default () => ({
    stats: {
        total: 0,
        deptStats: []
    },
    responses: { data: [], links: [] }, // Pagination structure
    departments: [], // Filter options
    search: '',
    department: '',
    year: '',
    loading: true,

    init() {
        // Initial fetch
        this.fetchData();

        // Watch for filter changes
        this.$watch('search', () => this.debouncedFetch());
        this.$watch('department', () => this.fetchData());
        this.$watch('year', () => this.fetchData());
    },

    debouncedFetch: _.debounce(function () {
        this.fetchData();
    }, 300),

    async fetchData(url = null) {
        this.loading = true;
        const endpoint = url || window.location.href;

        // Build URL if not provided (for filters)
        let fetchUrl = endpoint;
        if (!url) {
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.department) params.append('department', this.department);
            if (this.year) params.append('year', this.year);
            fetchUrl = `${window.location.pathname}?${params.toString()}`;

            // Update URL without reload
            window.history.replaceState(null, '', fetchUrl);
        }

        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            this.stats.total = data.totalResponses;
            this.stats.deptStats = data.deptStats;
            this.responses = data.responses;
            this.departments = data.departments; // If returned
        } catch (error) {
            console.error('Error fetching tracer data:', error);
        } finally {
            this.loading = false;
        }
    },

    // Delete action wrapper if needed, or stick to standard form submission
    confirmDelete(id) {
        if (confirm('Are you sure you want to delete this response?')) {
            // submit form programmatically or use ajax delete
            const form = document.getElementById(`delete-form-${id}`);
            if (form) form.submit();
        }
    }
});
