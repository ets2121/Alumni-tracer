import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';
import tiptapEditor from './components/tiptap-editor';
import moderationDashboard from './components/moderation-dashboard';
import newsComments from './components/news-comments';
import router from './router'; // New SPA Router
import dataLoader from './data-loader'; // New Data Loader
import alumniFeed from './components/alumni-feed';
import newsManager from './components/news-manager'; // Static Import
import photoManager from './components/photo-manager'; // Static Import
import reportManager from './components/report-manager';
import evaluationManager from './components/evaluation-manager';
import alumniEvaluationList from './components/alumni-evaluation-list';
import tracerDashboard from './components/tracer-dashboard';
import tracerForm from './components/tracer-form';
import alumniNewsFeed from './components/alumni-news-feed';
import alumniGallery from './components/alumni-gallery';
import groupManager from './components/group-manager';
import adminChatManager from './components/admin-chat-manager';
import alumniChatManager from './components/alumni-chat-manager';
import preRegistrationManager from './components/pre-registration-manager';
import alumniManager from './components/alumni-manager';
import userManager from './components/user-manager';
import analyticsDashboard from './components/analytics-dashboard';
import courseManager from './components/course-manager';
import employmentManager from './components/employment-manager';

// Register plugins
Alpine.plugin(collapse);
Alpine.plugin(intersect);

// Register data components
Alpine.data('tiptapEditor', tiptapEditor);
Alpine.data('moderationDashboard', moderationDashboard);
Alpine.data('newsComments', newsComments);
Alpine.data('dataLoader', dataLoader); // Register Data Loader
Alpine.data('alumniFeed', alumniFeed);

Alpine.data('alumniNewsFeed', alumniNewsFeed);
Alpine.data('alumniGallery', alumniGallery);
Alpine.data('reportManager', reportManager);
Alpine.data('evaluationManager', evaluationManager);
Alpine.data('alumniEvaluationList', alumniEvaluationList);
Alpine.data('tracerDashboard', tracerDashboard);
Alpine.data('tracerForm', (config) => tracerForm(config));
Alpine.data('newsManager', newsManager);
Alpine.data('photoManager', photoManager);
Alpine.data('groupManager', (groups) => groupManager(groups));
Alpine.data('adminChatManager', (config) => adminChatManager(config));
Alpine.data('alumniChatManager', (config) => alumniChatManager(config));
Alpine.data('preRegistrationManager', (config) => preRegistrationManager(config));
Alpine.data('alumniManager', (config) => alumniManager(config));
Alpine.data('userManager', (config) => userManager(config));
Alpine.data('analyticsDashboard', (initData) => analyticsDashboard(initData));
Alpine.data('courseManager', () => courseManager());
Alpine.data('employmentManager', (config) => employmentManager(config));

// Global exposure for debugging and fallback
window.dataLoader = dataLoader;
window.tiptapEditor = tiptapEditor;
window.photoManager = photoManager;
window.Alpine = Alpine;

// Global Toast Helper
window.showToast = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
};

// Global Loading State Management
let requestCount = 0;
const startLoading = () => {
    requestCount++;
    window.dispatchEvent(new CustomEvent('loading-start'));
};
const stopLoading = () => {
    requestCount = Math.max(0, requestCount - 1);
    if (requestCount === 0) {
        window.dispatchEvent(new CustomEvent('loading-end'));
    }
};

// Axios Interceptors
if (window.axios) {
    window.axios.interceptors.request.use((config) => {
        // Only trigger global loading if it's NOT a background polling request
        // (Optional refinement, but good for now)
        startLoading();
        return config;
    }, (error) => {
        stopLoading();
        return Promise.reject(error);
    });

    window.axios.interceptors.response.use((response) => {
        stopLoading();
        return response;
    }, (error) => {
        stopLoading();
        return Promise.reject(error);
    });
}

// Initialize SPA Router
router();

// Start Alpine
Alpine.start();
