export default function moderationDashboard(config = {}) {
    return {
        tab: 'post',
        postId: config.postId,

        // Insights state
        insights: null,
        loadingInsights: false,

        // Reactions state
        reactions: [],
        reactionsPage: 1,
        hasMoreReactions: true,
        loadingReactions: false,

        // Comments state
        comments: [],
        commentsPage: 1,
        hasMoreComments: true,
        loadingComments: false,

        // Reply state
        replyMode: false,
        selectedComment: null,
        replyContent: '',
        submitting: false,

        init() {
            // Initial load if starting on a tab
        },

        setTab(newTab) {
            this.tab = newTab;
            if (newTab === 'reactions' && this.reactions.length === 0) {
                this.loadMoreReactions();
            }
            if (newTab === 'comments' && this.comments.length === 0) {
                this.loadMoreComments();
            }
            if (newTab === 'insights' && !this.insights) {
                this.fetchInsights();
            }
        },

        async fetchInsights() {
            if (this.loadingInsights) return;
            this.loadingInsights = true;

            try {
                const response = await axios.get(`/admin/news_events/${this.postId}/insights`);
                this.insights = response.data;
            } catch (e) {
                console.error('Failed to fetch insights', e);
            } finally {
                this.loadingInsights = false;
            }
        },

        async loadMoreReactions() {
            if (this.loadingReactions || !this.hasMoreReactions) return;
            this.loadingReactions = true;

            try {
                const response = await axios.get(`/admin/news_events/${this.postId}/reactions?page=${this.reactionsPage}`);
                const data = response.data;

                this.reactions.push(...data.data);
                this.hasMoreReactions = !!data.next_page_url;
                this.reactionsPage++;
            } catch (e) {
                console.error('Failed to load reactions', e);
            } finally {
                this.loadingReactions = false;
            }
        },

        async loadMoreComments() {
            if (this.loadingComments || !this.hasMoreComments) return;
            this.loadingComments = true;

            try {
                const response = await axios.get(`/admin/news_events/${this.postId}/comments?page=${this.commentsPage}`);
                const data = response.data;

                this.comments.push(...data.data);
                this.hasMoreComments = !!data.next_page_url;
                this.commentsPage++;
            } catch (e) {
                console.error('Failed to load comments', e);
            } finally {
                this.loadingComments = false;
            }
        },

        replyTo(comment) {
            this.selectedComment = comment;
            this.replyMode = true;
            this.replyContent = '';
        },

        async submitReply() {
            if (!this.replyContent.trim() || this.submitting) return;
            this.submitting = true;

            try {
                const response = await axios.post(`/admin/news_events/comments/${this.selectedComment.id}/reply`, {
                    content: this.replyContent
                });

                if (window.showToast) {
                    window.showToast(response.data.success || 'Reply sent');
                }

                this.replyMode = false;

                // Refresh comments to show new one at the top
                this.comments = [];
                this.commentsPage = 1;
                this.hasMoreComments = true;
                this.loadMoreComments();
            } catch (e) {
                if (window.showToast) {
                    window.showToast(e.response?.data?.message || 'Failed to send reply', 'error');
                }
            } finally {
                this.submitting = false;
            }
        },

        confirmDeleteComment(commentId) {
            if (confirm('Are you sure you want to delete this comment?')) {
                this.deleteComment(commentId);
            }
        },

        async deleteComment(commentId) {
            try {
                const response = await axios.delete(`/admin/news_events/comments/${commentId}`);
                if (window.showToast) {
                    window.showToast(response.data.success || 'Comment removed');
                }
                this.comments = this.comments.filter(c => c.id !== commentId);
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
