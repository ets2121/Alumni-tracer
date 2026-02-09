export default () => ({
    modalOpen: false,
    questions: [
        { id: Date.now(), text: '', type: 'text', required: true, options: [''] }
    ],

    // Analytics Modal Logic
    analyticsModalOpen: false,
    analyticsLoading: false,
    analyticsContent: '',
    currentEvaluationId: null,
    currentEvaluationTitle: '',

    openModal() {
        this.modalOpen = true;
    },

    closeModal() {
        this.modalOpen = false;
        // Optional: Confirm if dirty? For now just close.
    },

    openAnalyticsModal(id, title = 'Evaluation Results') {
        this.analyticsModalOpen = true;
        this.currentEvaluationId = id;
        this.currentEvaluationTitle = title;
        this.fetchAnalytics(id);
    },

    closeAnalyticsModal() {
        this.analyticsModalOpen = false;
        this.analyticsContent = '';
        this.currentEvaluationId = null;
        this.currentEvaluationTitle = '';
    },

    fetchAnalytics(id, params = {}) {
        this.analyticsLoading = true;

        // Construct URL with query parameters
        let url = `/admin/evaluations/${id}/analytics`;
        const searchParams = new URLSearchParams(params);
        if (searchParams.toString()) {
            url += `?${searchParams.toString()}`;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                this.analyticsContent = html;
                this.analyticsLoading = false;
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
                this.analyticsContent = '<div class="text-center p-8 text-red-500">Failed to load analytics. Please try again.</div>';
                this.analyticsLoading = false;
            });
    },

    handleFilters(filters) {
        if (this.currentEvaluationId) {
            this.fetchAnalytics(this.currentEvaluationId, filters);
        }
    },

    addQuestion() {
        this.questions.push({
            id: Date.now() + Math.random(),
            text: '',
            type: 'text',
            required: true,
            options: ['']
        });
    }
});
