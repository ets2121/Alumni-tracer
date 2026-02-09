export default function newsComments(config = {}) {
    return {
        postId: config.postId,
        comments: (config.initialComments || []).map(c => ({ ...c, replies: c.replies || [] })),
        cursor: config.nextPage || null,
        hasMore: !!config.nextPage,
        loading: false,
        totalComments: config.initialCount || 0,

        // UI State
        expandedComments: [], // IDs of comments whose replies are expanded

        // Form state
        content: '',
        parentId: null,
        submitting: false,

        // Reaction state
        reactionCount: config.initialReactionCount || 0,
        userReacted: config.userReacted || false,

        async init() {
            // No need to loadMore if we have initial comments and no more pages
        },

        isExpanded(id) {
            return this.expandedComments.includes(id);
        },

        toggleExpanded(id) {
            if (this.isExpanded(id)) {
                this.expandedComments = this.expandedComments.filter(cid => cid !== id);
            } else {
                this.expandedComments.push(id);
            }
        },

        async loadMore() {
            if (this.loading || !this.hasMore) return;
            this.loading = true;

            try {
                // Laravel cursor pagination uses a 'cursor' query parameter instead of 'page'
                let url = `/news/${this.postId}/comments`;
                if (this.cursor) {
                    // nextPage in config was already the full URL from Laravel sometimes, 
                    // but usually it's better to just pass the cursor value.
                    // Laravel's cursorPaginate uses ?cursor=...
                    url = this.cursor;
                }

                const response = await axios.get(url);
                const data = response.data;

                this.comments.push(...data.data.map(c => ({ ...c, replies: c.replies || [] })));
                this.cursor = data.next_page_url; // cursorPaginate returns next_page_url
                this.hasMore = !!data.next_page_url;
            } catch (e) {
                console.error('Failed to load comments', e);
            } finally {
                this.loading = false;
            }
        },

        async submitComment() {
            if (!this.content.trim() || this.submitting) return;
            this.submitting = true;

            try {
                const response = await axios.post(`/news/${this.postId}/comment`, {
                    content: this.content,
                    parent_id: this.parentId
                });

                if (response.data.status === 'success') {
                    const newComment = response.data.comment;

                    if (this.parentId) {
                        // Find the parent and add to its replies
                        const parent = this.comments.find(c => c.id === this.parentId);
                        if (parent) {
                            if (!parent.replies) parent.replies = [];
                            parent.replies.unshift(newComment);
                            // Auto-expand if not already
                            if (!this.isExpanded(this.parentId)) {
                                this.expandedComments.push(this.parentId);
                            }
                        }
                    } else {
                        // Add to top-level comments
                        newComment.replies = newComment.replies || [];
                        this.comments.unshift(newComment);
                    }

                    this.content = '';
                    this.parentId = null;
                    this.totalComments = response.data.count;

                    // Update global state
                    window.dispatchEvent(new CustomEvent('comment-added', {
                        detail: { postId: this.postId, count: this.totalComments }
                    }));

                    if (window.showToast) {
                        window.showToast('Comment posted successfully');
                    }
                }
            } catch (e) {
                if (window.showToast) {
                    window.showToast(e.response?.data?.message || 'Failed to post comment', 'error');
                }
            } finally {
                this.submitting = false;
            }
        },

        async toggleReaction(type = 'like') {
            try {
                const response = await axios.post(`/news/${this.postId}/react`, { type });
                if (response.data.status === 'success') {
                    this.reactionCount = response.data.count;
                    this.userReacted = response.data.action !== 'removed';

                    // Update global state
                    window.dispatchEvent(new CustomEvent('reaction-toggled', {
                        detail: {
                            postId: this.postId,
                            count: this.reactionCount,
                            userReacted: this.userReacted
                        }
                    }));
                }
            } catch (e) {
                console.error('Reaction failed', e);
            }
        },

        async deleteComment(id) {
            if (!confirm('Are you sure you want to delete this comment?')) return;

            try {
                const response = await axios.delete(`/news/comment/${id}`);
                if (response.data.status === 'success') {
                    // Remove from local state
                    this.comments = this.comments.filter(c => {
                        if (c.id === id) return false;
                        if (c.replies) {
                            c.replies = c.replies.filter(r => r.id !== id);
                        }
                        return true;
                    });

                    if (window.showToast) {
                        window.showToast('Comment deleted');
                    }
                }
            } catch (e) {
                if (window.showToast) {
                    window.showToast('Failed to delete comment', 'error');
                }
            }
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
}
