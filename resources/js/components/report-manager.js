import Chart from 'chart.js/auto';

export default () => ({
    // Report-specific independent filter states
    reportFilters: {
        detailed_labor: { fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '', establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '', batchYear: '', page: 1 },
        statistical_summary: { fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '', establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '', batchYear: '' },
        tracer_study: { fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '', establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '', batchYear: '' },
        master_list: { fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '', establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '', batchYear: '', subType: 'all', search: '', page: 1 },
        annual_distribution: { fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '', establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '', batchYear: '', subType: 'by_year', chartType: 'bar' },
        graduates_by_course: { fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '', establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '', batchYear: '', page: 1 },
    },
    activeCharts: {}, // Store Chart instances
    loading: false,
    previewOpen: false,
    evalModalOpen: false, // New State
    selectedEvaluationId: '', // New State
    currentReportType: 'detailed_labor',
    currentReportTitle: '',
    generateUrl: '/admin/reports/generate', // Default, should be passed in or configured
    evalUrl: '/admin/evaluations',

    init() {
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.previewOpen = false;
                this.evalModalOpen = false;
            }
        });

        // Expose function for print
        this.printReport = () => window.print();

        // Expose export function
        this.exportReport = (format) => {
            const url = new URL('/admin/reports/export', window.location.origin);
            const f = this.reportFilters[this.currentReportType];
            url.searchParams.set('type', this.currentReportType);
            url.searchParams.set('format', format);

            if (f) {
                // ... append all filters ...
                Object.keys(f).forEach(key => {
                    if (f[key]) {
                        // CamelCase to snake_case simple conversion for query params?
                        // Actually the object keys match the usage in generateReport URL construction logic.
                        // Let's copy the logic from generateReport but for export.
                        const paramMap = {
                            fromDate: 'from_date', toDate: 'to_date', workStatus: 'work_status',
                            establishmentType: 'establishment_type', workLocation: 'work_location',
                            fieldOfWork: 'field_of_work', courseId: 'course_id', batchYear: 'batch_year',
                            fromYear: 'from_year', toYear: 'to_year', subType: 'sub_type',
                            chartType: 'chart_type', search: 'search', page: 'page'
                        };
                        const param = paramMap[key] || key;
                        url.searchParams.set(param, f[key]);
                    }
                });
            }
            window.location.href = url.toString();
        }

        // Expose function for pagination
        this.changePage = (p) => {
            this.reportFilters[this.currentReportType].page = p;
            this.generateReport(this.currentReportType);
        }
    },

    openEvaluationModal() {
        this.evalModalOpen = true;
    },

    async launchAnalytics() {
        if (this.selectedEvaluationId) {
            this.loading = true;
            this.previewOpen = true;
            this.evalModalOpen = false;
            this.currentReportTitle = 'Evaluation Analytics';
            this.currentReportType = 'evaluation_results';

            try {
                const response = await fetch(`${this.evalUrl}/${this.selectedEvaluationId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const html = await response.text();
                const container = document.getElementById('injected-report-body');
                if (container) {
                    container.innerHTML = html;
                    container.closest('.overflow-y-auto').scrollTop = 0;
                }
            } catch (error) {
                console.error('Analytics load failed:', error);
            } finally {
                this.loading = false;
            }
        }
    },

    resetFilters(type = null) {
        const target = type || this.currentReportType;
        const defaults = {
            subType: this.reportFilters[target].subType === 'all' ? 'all' : (this.reportFilters[target].subType || 'all'),
            chartType: this.reportFilters[target].chartType || 'bar'
        };

        // Reset to empty strings but keep defaults
        this.reportFilters[target] = {
            fromDate: '', toDate: '', fromYear: '', toYear: '', workStatus: '',
            establishmentType: '', workLocation: '', fieldOfWork: '', courseId: '',
            batchYear: '', subType: defaults.subType, chartType: defaults.chartType
        };

        if (this.reportFilters[target].hasOwnProperty('page')) {
            this.reportFilters[target].page = 1;
        }

        if (target === 'master_list') {
            this.reportFilters[target].search = '';
        }

        this.generateReport(target);
    },

    async generateReport(type) {
        this.currentReportType = type;
        this.currentReportTitle = type.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
        this.loading = true;
        this.previewOpen = true;

        const f = this.reportFilters[type];
        const url = new URL(this.generateUrl, window.location.origin);
        url.searchParams.set('type', type);

        const paramMap = {
            fromDate: 'from_date', toDate: 'to_date', workStatus: 'work_status',
            establishmentType: 'establishment_type', workLocation: 'work_location',
            fieldOfWork: 'field_of_work', courseId: 'course_id', batchYear: 'batch_year',
            fromYear: 'from_year', toYear: 'to_year', subType: 'sub_type',
            chartType: 'chart_type', search: 'search', page: 'page'
        };

        if (f) {
            Object.keys(f).forEach(key => {
                if (f[key]) url.searchParams.set(paramMap[key] || key, f[key]);
            });
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const html = await response.text();

            const container = document.getElementById('injected-report-body');
            if (container) {
                container.innerHTML = html;
                container.closest('.overflow-y-auto').scrollTop = 0;
            }

            if (type === 'statistical_summary' || type === 'annual_distribution') {
                setTimeout(() => this.initCharts(), 100);
            }
        } catch (error) {
            console.error('Report failed:', error);
        } finally {
            this.loading = false;
        }
    },

    // ... Charts logic ...
    createChart(id, type, config, extra = {}) {
        const ctx = document.getElementById(id);
        if (ctx) {
            // Check if dataset is available
            if (!ctx.dataset.labels) return;

            const data = {
                labels: JSON.parse(ctx.dataset.labels),
                datasets: [{
                    data: JSON.parse(ctx.dataset.values || ctx.dataset.data || '[]'),
                    backgroundColor: extra.colors || '#6366f1',
                    borderWidth: 0,
                    borderRadius: extra.borderRadius || 0
                }]
            };

            // Handle specific dataset structures if needed (like pie charts often have one dataset with multiple colors)
            if (['pie', 'doughnut', 'polarArea'].includes(type) && extra.colors) {
                data.datasets[0].backgroundColor = extra.colors;
            }

            if (extra.cutout) config.cutout = extra.cutout;
            if (extra.indexAxis) config.indexAxis = extra.indexAxis;

            this.activeCharts[id] = new Chart(ctx, { type, data, options: config });
        }
    },

    initCharts() {
        Object.values(this.activeCharts).forEach(chart => chart.destroy());
        this.activeCharts = {};

        Chart.defaults.font.family = 'Figtree';
        const baseConfig = {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            plugins: { legend: { display: false } }
        };

        // 1. COMBINED MATRIX CHARTS
        const ctxCourse = document.getElementById('chartByCourse');
        if (ctxCourse) {
            new Chart(ctxCourse, {
                type: 'bar',
                data: {
                    labels: JSON.parse(ctxCourse.dataset.labels),
                    datasets: [
                        { label: 'Employed', data: JSON.parse(ctxCourse.dataset.employed), backgroundColor: '#10b981' },
                        { label: 'Unemployed', data: JSON.parse(ctxCourse.dataset.unemployed), backgroundColor: '#94a3b8' }
                    ]
                },
                options: baseConfig
            });
        }
        this.createChart('chartByEmployment', 'pie', baseConfig, { colors: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] });
        this.createChart('chartByGender', 'polarArea', baseConfig, { colors: ['rgba(99, 102, 241, 0.7)', 'rgba(236, 72, 153, 0.7)', 'rgba(100, 116, 139, 0.7)'] });

        // Sector Ranking (Grouped)
        const ctxSector = document.getElementById('chartEstablishmentGrouped');
        if (ctxSector) {
            new Chart(ctxSector, {
                type: 'bar',
                data: {
                    labels: JSON.parse(ctxSector.dataset.labels),
                    datasets: [
                        { label: 'Public', data: JSON.parse(ctxSector.dataset.public), backgroundColor: '#6366f1' },
                        { label: 'Private', data: JSON.parse(ctxSector.dataset.private), backgroundColor: '#a855f7' }
                    ]
                },
                options: baseConfig
            });
        }

        // Stacked Bar for Stability
        const ctxStability = document.getElementById('chartStabilityStacked');
        if (ctxStability) {
            new Chart(ctxStability, {
                type: 'bar',
                data: {
                    labels: JSON.parse(ctxStability.dataset.labels),
                    datasets: [
                        { label: 'Permanent', data: JSON.parse(ctxStability.dataset.permanent), backgroundColor: '#10b981' },
                        { label: 'Contractual', data: JSON.parse(ctxStability.dataset.contractual), backgroundColor: '#f59e0b' },
                        { label: 'Job Order', data: JSON.parse(ctxStability.dataset.jo), backgroundColor: '#ef4444' }
                    ]
                },
                options: { ...baseConfig, scales: { x: { stacked: true }, y: { stacked: true } } }
            });
        }

        // Combination Chart for Registration vs Employment
        const ctxCombo = document.getElementById('chartCombinationSummary');
        if (ctxCombo) {
            new Chart(ctxCombo, {
                type: 'bar',
                data: {
                    labels: JSON.parse(ctxCombo.dataset.labels),
                    datasets: [
                        { type: 'bar', label: 'Total Alumni', data: JSON.parse(ctxCombo.dataset.total), backgroundColor: 'rgba(99, 102, 241, 0.2)', borderColor: '#6366f1', borderWidth: 1 },
                        { type: 'line', label: 'Employed', data: JSON.parse(ctxCombo.dataset.employed), borderColor: '#10b981', borderWidth: 3, tension: 0.4, fill: false }
                    ]
                },
                options: baseConfig
            });
        }

        // 4. LABOR ANALYTICS ADDITIONS
        const ctxLoc = document.getElementById('chartLocationGrouped');
        if (ctxLoc) {
            new Chart(ctxLoc, {
                type: 'bar',
                data: {
                    labels: JSON.parse(ctxLoc.dataset.labels),
                    datasets: [
                        { label: 'Local', data: JSON.parse(ctxLoc.dataset.local), backgroundColor: '#3b82f6' },
                        { label: 'Overseas', data: JSON.parse(ctxLoc.dataset.overseas), backgroundColor: '#f97316' }
                    ]
                },
                options: baseConfig
            });
        }
        this.createChart('chartByWorkStatus', 'bar', baseConfig, { borderRadius: 12, colors: ['#8b5cf6'] });
        this.createChart('chartByEstablishment', 'pie', baseConfig, { colors: ['#6366f1', '#a855f7'] });
        this.createChart('chartByWorkLocation', 'doughnut', baseConfig, { cutout: '70%', colors: ['#3b82f6', '#f97316'] });
        this.createChart('chartTopFieldsMatrix', 'bar', baseConfig, { indexAxis: 'y', borderRadius: 20, colors: ['#6366f1'] });

        // Dynamic Focus Views
        this.createChart('chartProgramFocus', 'bar', baseConfig, { indexAxis: 'y', borderRadius: 20, colors: ['#6366f1'] });
        this.createChart('chartLaborFocus', 'bar', baseConfig, { borderRadius: 20, colors: ['#2563eb', '#10b981', '#f59e0b', '#ef4444'] });
        this.createChart('chartGenderFocus', 'doughnut', baseConfig, { cutout: '70%', colors: ['#6366f1', '#ec4899', '#64748b'] });
        this.createChart('chartCivilFocus', 'bar', baseConfig, { borderRadius: 12, colors: ['#8b5cf6'] });
        this.createChart('chartTopFieldsFocus', 'bar', baseConfig, { indexAxis: 'y', borderRadius: 20, colors: ['#6366f1'] });

        // Tracer Results
        this.createChart('chartTracerLikert', 'bar', baseConfig, { colors: ['#10b981', '#34d399', '#94a3b8', '#f87171', '#ef4444'] });
        this.createChart('chartTracerMultiple', 'bar', baseConfig, { indexAxis: 'y', colors: ['#6366f1'] });

        // 3. ANNUAL DISTRIBUTION (DYNAMIC MATRIX)
        const ctxDist = document.getElementById('distributionMainChart');
        if (ctxDist) {
            const chartType = ctxDist.dataset.type || 'bar';
            const subType = ctxDist.dataset.subtype;
            const labels = JSON.parse(ctxDist.dataset.labels);
            const rawData = JSON.parse(ctxDist.dataset.raw);
            let datasets = [];

            if (subType.includes('employment') || subType.includes('location')) {
                const keys = [...new Set(rawData.map(d => d.employment_status || d.work_location))];
                const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                datasets = keys.map((key, i) => {
                    const data = labels.map(label => {
                        const found = rawData.find(d => (d.batch_year == label || d.label == label) && (d.employment_status == key || d.work_location == key));
                        return found ? found.count : 0;
                    });
                    return {
                        label: key,
                        data: data,
                        backgroundColor: colors[i % colors.length],
                        borderColor: colors[i % colors.length],
                        borderWidth: 1,
                        borderRadius: chartType === 'bar' ? 8 : 0,
                    };
                });
            } else {
                datasets = [{
                    label: 'Total Alumni',
                    data: JSON.parse(ctxDist.dataset.values),
                    backgroundColor: ['pie', 'donut', 'doughnut'].includes(chartType) ?
                        ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'] : '#8b5cf6',
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: chartType === 'bar' ? 12 : 0,
                }];
            }

            this.activeCharts[ctxDist.id] = new Chart(ctxDist, {
                type: chartType === 'stacked' ? 'bar' : (chartType === 'donut' ? 'doughnut' : chartType),
                data: { labels, datasets },
                options: {
                    ...baseConfig,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    },
                    scales: (chartType === 'bar' || chartType === 'line') ? {
                        x: { stacked: chartType === 'stacked' },
                        y: { stacked: chartType === 'stacked' }
                    } : {}
                }
            });
        }
    }
});
