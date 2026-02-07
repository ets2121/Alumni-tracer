import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';
import tiptapEditor from './components/tiptap-editor';
import moderationDashboard from './components/moderation-dashboard';
import newsComments from './components/news-comments';

// Register plugins
Alpine.plugin(collapse);
Alpine.plugin(intersect);

// Register data components
Alpine.data('tiptapEditor', tiptapEditor);
Alpine.data('moderationDashboard', moderationDashboard);
Alpine.data('newsComments', newsComments);

// Global exposure for debugging and fallback
window.tiptapEditor = tiptapEditor;
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

// Global Click Listener for Perceived Performance on Page Transitions
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link &&
        link.href &&
        link.href.startsWith(window.location.origin) &&
        !link.href.includes('#') &&
        link.target !== '_blank' &&
        !e.metaKey && !e.ctrlKey) {
        window.dispatchEvent(new CustomEvent('loading-start'));
    }
});

// Native Fetch Interceptor (Optional but good for completeness)
const originalFetch = window.fetch;
window.fetch = async (...args) => {
    startLoading();
    try {
        const response = await originalFetch(...args);
        stopLoading();
        return response;
    } catch (error) {
        stopLoading();
        throw error;
    }
};

// Start Alpine
Alpine.start();
