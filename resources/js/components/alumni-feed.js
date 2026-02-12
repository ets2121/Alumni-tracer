export default (config) => ({
    endpoint: config.endpoint,
    isActive: config.isActive,
    tab: 'all',
    nextCursor: null,
    loading: false,
    hasMore: true,
    feedHtml: '',
    imageModal: {
        open: false,
        src: ''
    },
    discussionModal: {
        open: false,
        postId: null,
        html: ''
    },

    init() {
        console.log('Alumni Feed Component Initialized', { isActive: this.isActive });

        // SWR Logic: Try to load from cache first
        const cacheKey = `feed_${this.tab}`;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            this.feedHtml = cached;
        }

        if (this.isActive) {
            this.fetchFeed();
        }

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !this.loading && this.hasMore) {
                this.loadMore();
            }
        }, {
            threshold: 0.1,
            root: document.getElementById('feed-scroll-container')
        });

        if (this.$refs.sentinel) {
            observer.observe(this.$refs.sentinel);
        }
    },

    async openDiscussion(postId) {
        this.discussionModal.postId = postId;
        this.discussionModal.open = true;
        this.discussionModal.html = '<div class="h-full flex items-center justify-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-600"></div></div>';

        try {
            const response = await fetch(`/news/${postId}/discussion`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            this.discussionModal.html = await response.text();
        } catch (e) {
            this.discussionModal.html = '<div class="p-10 text-center text-red-500 font-bold italic">Failed to load discussion. Please try again.</div>';
        }
    },

    switchTab(newTab) {
        if (this.tab === newTab) return;
        this.tab = newTab;
        this.nextCursor = null;
        this.hasMore = true;

        const cacheKey = `feed_${this.tab}`;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            this.feedHtml = cached;
        } else {
            this.feedHtml = '';
        }

        if (this.isActive) {
            this.fetchFeed();
        }

        this.$dispatch('feed-tab-synced', newTab);

        const container = document.getElementById('feed-scroll-container');
        if (container) {
            container.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },

    async fetchFeed() {
        if (this.loading) return;
        this.loading = true;

        try {
            let url = `${this.endpoint}?tab=${this.tab}`;
            if (this.nextCursor) {
                url += `&cursor=${this.nextCursor}`;
            }

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!this.nextCursor) {
                this.feedHtml = data.html;
                sessionStorage.setItem(`feed_${this.tab}`, data.html);
            } else {
                this.feedHtml += data.html;
            }

            this.nextCursor = data.next_cursor;
            this.hasMore = data.has_more;
        } catch (error) {
            console.error('Feed error:', error);
        } finally {
            this.loading = false;
        }
    },

    loadMore() {
        if (this.hasMore && !this.loading) {
            this.fetchFeed();
        }
    }
});
