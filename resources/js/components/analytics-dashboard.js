export default (initData) => ({
    analytics: initData.analytics,
    trendLabels: initData.trendLabels,
    trendValues: initData.trendValues,
    courseDistLabels: initData.courseDistLabels || [],
    courseDistValues: initData.courseDistValues || [],
    batchDistLabels: initData.batchDistLabels || [],
    batchDistValues: initData.batchDistValues || [],
    activeTab: 'overview', // 'overview' or 'insights'

    init() {
        this.$watch('activeTab', (value) => {
            this.$nextTick(() => {
                if (value === 'insights') {
                    this.initInsightsCharts();
                } else {
                    this.initCharts();
                }
            });
        });

        // Initialize default tab charts
        this.initCharts();
    },

    applyFilters(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // Dispatch event for parent to handle (Modal context)
        this.$dispatch('filters-applied', Object.fromEntries(formData));

        // If not in a modal context (standalone page), reload
        if (!this.$el.closest('[x-data="evaluationManager"]')) {
            // We use the router to navigate if it's an SPA
            if (window.router && typeof window.router.navigate === 'function') {
                window.router.navigate(`${window.location.pathname}?${params.toString()}`);
            } else {
                window.location.search = params.toString();
            }
        }
    },

    hasStats(item) {
        return item.stats && Object.keys(item.stats).length > 0 && Object.values(item.stats).some(v => v > 0);
    },

    initInsightsCharts() {
        this.$nextTick(() => {
            this.initTrendChart();
            this.initCourseDistChart();
            this.initBatchDistChart();
        });
    },

    initTrendChart() {
        if (this.trendValues.length === 0 || !this.$refs.trendChart) return;

        const ctx = this.$refs.trendChart.getContext('2d');
        if (window.Chart.getChart(ctx)) window.Chart.getChart(ctx).destroy();

        new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: this.trendLabels,
                datasets: [{
                    label: 'New Responses',
                    data: this.trendValues,
                    borderColor: '#4F46E5',
                    backgroundColor: (context) => {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
                        return gradient;
                    },
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#4F46E5',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#F3F4F6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    },

    initCourseDistChart() {
        if (!this.$refs.courseDistChart) return;
        const ctx = this.$refs.courseDistChart.getContext('2d');
        if (window.Chart.getChart(ctx)) window.Chart.getChart(ctx).destroy();

        new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: this.courseDistLabels,
                datasets: [{
                    label: 'Responses',
                    data: this.courseDistValues,
                    backgroundColor: '#0ea5e9',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } }
                }
            }
        });
    },

    initBatchDistChart() {
        if (!this.$refs.batchDistChart) return;
        const ctx = this.$refs.batchDistChart.getContext('2d');
        if (window.Chart.getChart(ctx)) window.Chart.getChart(ctx).destroy();

        new window.Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: this.batchDistLabels,
                datasets: [{
                    label: 'Responses',
                    data: this.batchDistValues,
                    backgroundColor: [
                        '#6366f1', '#ec4899', '#8b5cf6', '#10b981', '#f59e0b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    },

    initCharts() {
        window.Chart.defaults.font.family = "'Inter', sans-serif";
        window.Chart.defaults.color = '#6B7280';
        window.Chart.defaults.scale.grid.color = '#F3F4F6';
        window.Chart.defaults.scale.grid.borderColor = 'transparent';

        this.$nextTick(() => {
            this.analytics.forEach(item => {
                if (['radio', 'checkbox', 'scale'].includes(item.type) && this.hasStats(item)) {
                    const ctx = document.getElementById('chart-' + item.id);
                    if (!ctx) return;

                    if (window.Chart.getChart(ctx)) return; // Already exists

                    const labels = Object.keys(item.stats);
                    const data = Object.values(item.stats);

                    let chartType = 'bar';
                    let indexAxis = 'x';
                    let legendDisplay = false;

                    const getGradient = (ctx, colorStart, colorEnd) => {
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, colorStart);
                        gradient.addColorStop(1, colorEnd);
                        return gradient;
                    };

                    const palettes = [
                        ['#4F46E5', '#818cf8'],
                        ['#10B981', '#34d399'],
                        ['#F59E0B', '#fbbf24'],
                        ['#EF4444', '#f87171'],
                        ['#8B5CF6', '#a78bfa'],
                        ['#EC4899', '#f472b6'],
                        ['#06B6D4', '#22d3ee'],
                    ];

                    let backgroundColors = labels.map((_, i) => {
                        const p = palettes[i % palettes.length];
                        return ctx.getContext('2d') ? getGradient(ctx.getContext('2d'), p[0], p[1]) : p[0];
                    });
                    let borderColors = 'transparent';

                    if (item.type === 'radio') {
                        chartType = 'doughnut';
                        legendDisplay = true;
                    } else if (item.type === 'checkbox') {
                        chartType = 'bar';
                        indexAxis = 'y';
                    } else if (item.type === 'scale') {
                        chartType = 'bar';
                        indexAxis = 'x';
                        const scaleColors = {
                            1: 'rgba(239, 68, 68, 0.7)',
                            2: 'rgba(249, 115, 22, 0.7)',
                            3: 'rgba(234, 179, 8, 0.7)',
                            4: 'rgba(59, 130, 246, 0.7)',
                            5: 'rgba(16, 185, 129, 0.7)'
                        };
                        backgroundColors = labels.map(l => scaleColors[l] || '#4F46E5');
                        borderColors = labels.map(l => scaleColors[l] ? scaleColors[l].replace('0.7', '1') : '#4F46E5');
                    }

                    new window.Chart(ctx, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Responses',
                                data: data,
                                backgroundColor: item.type === 'scale' ? backgroundColors : (chartType === 'doughnut' ? backgroundColors : '#4F46E5'),
                                borderColor: item.type === 'scale' ? borderColors : 'transparent',
                                borderRadius: 4,
                                borderWidth: item.type === 'scale' ? 1 : 0,
                                hoverOffset: 6
                            }]
                        },
                        options: {
                            indexAxis: indexAxis,
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 800, easing: 'easeOutQuart' },
                            plugins: {
                                legend: {
                                    display: legendDisplay,
                                    position: 'right',
                                    labels: { usePointStyle: true, boxWidth: 6, font: { size: 10 } }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    padding: 10,
                                    callbacks: {
                                        label: function (c) {
                                            const val = c.raw;
                                            const total = c.chart._metasets[c.datasetIndex].total;
                                            const arrayTotal = c.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((val / (chartType === 'doughnut' ? total : arrayTotal)) * 100);
                                            return ` ${val} votes (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            scales: chartType !== 'doughnut' ? {
                                y: {
                                    beginAtZero: true,
                                    grid: { borderDash: [2, 2], drawBorder: false },
                                    ticks: { stepSize: 1, font: { size: 10 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10 } }
                                }
                            } : { x: { display: false }, y: { display: false } },
                            layout: { padding: 10 }
                        }
                    });
                }
            });
        });
    }
});
