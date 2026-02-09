export default (config) => ({
    endpoint: config.endpoint,
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
        // SWR Logic: Try to load from cache first
        const cacheKey = `feed_${this.tab}`;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            this.feedHtml = cached;
            // Still fetch in background to refresh
        }

        this.fetchFeed();

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
            this.$nextTick(() => {
                if (this.$refs.discussionModalContent) {
                    // Check if Alpine.initTree is available, otherwise rely on x-html reactivity or re-scan
                    // Alpine v3 automatically observes new DOM from x-html? 
                    // Actually x-html typically DOES NOT initialize Alpine components inside.
                    // But if discussion modal content is just HTML/Tailwind, it's fine.
                    // If it has Alpine interactions, we might need a helper.
                    // For now, assume it's mostly static or handled by global listeners.
                }
            });
        } catch (e) {
            this.discussionModal.html = '<div class="p-10 text-center text-red-500 font-bold italic">Failed to load discussion. Please try again.</div>';
        }
    },

    switchTab(newTab) {
        if (this.tab === newTab) return;
        this.tab = newTab;
        this.nextCursor = null;
        this.hasMore = true;

        // SWR for tab switching
        const cacheKey = `feed_${this.tab}`;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            this.feedHtml = cached;
        } else {
            this.feedHtml = '';
        }

        this.fetchFeed();

        this.$dispatch('feed-tab-synced', newTab);

        const container = document.getElementById('feed-scroll-container');
        if (container) {
            container.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    },

    async fetchFeed() {
        if (this.loading) return;
        this.loading = true;

        try {
            // We need the route URL. Since we are in a JS file, we can't use blade {{ route() }}.
            // We'll trust that the API endpoint is consistent or pass it via data-attribute if needed.
            // For now, hardcoding based on existing route or using a global config object is best.
            // Let's assume '/alumni/feed' based on typical routing, but the dashboard used {{ route('alumni.feed.fetch') }}
            // We should probably pass the endpoint into the component init or check window.routes.

            // BETTER: Use a relative URL or a known API path.
            // unique route: alumni.feed.fetch -> /alumni/feed/fetch (guess)
            // I'll check web.php to be sure, but for now I'll use a relative path that likely works or inject it.
            // Actually, I can pass it as an argument to the component: x-data="alumniFeed('/alumni/feed/fetch')"

            let url = `${this.endpoint}?tab=${this.tab}`;
            if (this.nextCursor) {
                url += `&cursor=${this.nextCursor}`;
            }

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
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

            if (!this.nextCursor) {
                this.hasMore = false;
            }

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
