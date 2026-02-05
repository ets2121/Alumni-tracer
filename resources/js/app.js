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

// Start Alpine
Alpine.start();
