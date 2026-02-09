export default () => ({
    counts: {
        alumni_total: 0,
        alumni_verified: 0,
        alumni_pending: 0,
        dept_admins: 0,
        total_departments: 0,
        active_events: 0,
        upcoming_events: 0,
        past_events: 0,
    },
    loading: {
        counts: true,
        charts: true,
        recentUsers: true
    },
    recentUsers: {
        verified: [],
        pending: []
    },
    charts: {},

    async init() {
        await Promise.all([
            this.fetchCounts(),
            this.fetchCharts(),
            this.fetchRecentUsers()
        ]);

        // Listen for theme changes if using window-level event or observer
        window.addEventListener('theme-changed', () => {
            this.refreshCharts();
        });
    },

    async fetchCounts() {
        this.loading.counts = true;
        try {
            const response = await axios.get('/admin/stats/counts');
            this.counts = response.data;
        } catch (error) {
            console.error('Failed to fetch counts:', error);
        } finally {
            this.loading.counts = false;
        }
    },

    async fetchCharts() {
        this.loading.charts = true;
        try {
            const response = await axios.get('/admin/stats/charts');
            this.renderCharts(response.data);
        } catch (error) {
            console.error('Failed to fetch charts:', error);
        } finally {
            this.loading.charts = false;
        }
    },

    async fetchRecentUsers() {
        this.loading.recentUsers = true;
        try {
            const response = await axios.get('/admin/stats/recent-users');
            this.recentUsers = response.data;
        } catch (error) {
            console.error('Failed to fetch recent users:', error);
        } finally {
            this.loading.recentUsers = false;
        }
    },

    renderCharts(data) {
        // Shared options
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)';

        Chart.defaults.color = textColor;
        Chart.defaults.font.family = "'Inter', sans-serif";

        // 1. Registration Trends (Line)
        this.initLineChart('registrationTrendsChart', data.registration_trends, 'Alumni Registrations', '#10b981');

        // 2. Alumni by Department (Bar)
        this.initBarChart('alumniByDeptChart', data.alumni_by_dept, 'Alumni per Department', '#6366f1');

        // 3. Employment Status (Doughnut)
        this.initDoughnutChart('employmentStatusChart', data.employment_status);

        // 4. Gender Distribution (Pie)
        this.initPieChart('genderDistChart', data.gender_distribution);

        // 5. Civil Status Distribution (Bar)
        this.initBarChart('civilStatusChart', data.civil_status, 'Civil Status', '#f59e0b', 'y');

        // 6. Employment Type (Doughnut)
        this.initDoughnutChart('employmentTypeChart', data.employment_type);
    },

    initLineChart(ref, data, label, color) {
        const ctx = this.$refs[ref].getContext('2d');
        if (this.charts[ref]) this.charts[ref].destroy();

        this.charts[ref] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: label,
                    data: data.data,
                    borderColor: color,
                    backgroundColor: color + '22',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'transparent' } },
                    x: { grid: { color: 'transparent' } }
                }
            }
        });
    },

    initBarChart(ref, data, label, color, axis = 'x') {
        const ctx = this.$refs[ref].getContext('2d');
        if (this.charts[ref]) this.charts[ref].destroy();

        this.charts[ref] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: label,
                    data: data.data,
                    backgroundColor: color,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: axis,
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'transparent' } },
                    x: { grid: { color: 'transparent' } }
                }
            }
        });
    },

    initDoughnutChart(ref, data) {
        const ctx = this.$refs[ref].getContext('2d');
        if (this.charts[ref]) this.charts[ref].destroy();

        this.charts[ref] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });
    },

    initPieChart(ref, data) {
        const ctx = this.$refs[ref].getContext('2d');
        if (this.charts[ref]) this.charts[ref].destroy();

        this.charts[ref] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: ['#3b82f6', '#ec4899', '#94a3b8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });
    },

    refreshCharts() {
        // Redraw with updated theme colors if necessary
        Object.values(this.charts).forEach(chart => chart.update());
    }
});
