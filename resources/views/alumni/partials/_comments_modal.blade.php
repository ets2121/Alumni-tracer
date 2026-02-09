<div class="flex flex-col h-full bg-white" x-data="newsComments({ 
    postId: {{ $news_event->id }}, 
    initialCount: {{ $news_event->comments_count }},
    initialReactionCount: {{ $news_event->reactions_count }},
    userReacted: {{ $news_event->userReaction ? 'true' : 'false' }},
    initialComments: {{ Js::from($initialComments->items()) }},
    nextPage: '{{ $initialComments->nextPageUrl() }}'
})" x-init="init()">
    <!-- Modal Header -->
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
        <div>
            <h3 class="text-lg font-black text-gray-900 tracking-tight">Discussion</h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"
                x-text="totalComments + ' Comments'"></p>
        </div>
        <div class="flex items-center gap-4">
            <button @click="toggleReaction()"
                :class="userReacted ? 'text-red-600 bg-red-50 border-red-100' : 'text-gray-400 bg-gray-50 border-gray-100'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-black text-[10px] uppercase tracking-widest transition-all">
                <svg class="w-3.5 h-3.5" :fill="userReacted ? 'currentColor' : 'none'" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span x-text="reactionCount"></span>
            </button>
        </div>
    </div>

    <!-- Comments List Area -->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50 custom-scrollbar">
        <div class="space-y-6 max-w-2xl mx-auto">
            <template x-for="comment in comments" :key="comment.id">
                <div
                    class="group bg-white p-5 rounded-3xl border border-gray-100 shadow-sm transition-all hover:border-brand-200 hover:shadow-md mb-8">
                    <!-- 1. Main Comment Area (Header + Content) -->
                    <div class="flex flex-col gap-4">
                        <!-- Header: Avatar + Meta -->
                        <div class="flex items-center gap-3">
                            <div
                                class="w-11 h-11 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 font-black overflow-hidden border border-brand-100 flex-shrink-0 shadow-sm">
                                <template x-if="comment.user.avatar">
                                    <img :src="'/storage/' + comment.user.avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!comment.user.avatar">
                                    <span x-text="comment.user.name.charAt(0)"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-sm font-black text-gray-900 truncate tracking-tight"
                                        x-text="comment.user.name"></h5>
                                    <template x-if="comment.user_id === {{ auth()->id() }}">
                                        <button @click="deleteComment(comment.id)"
                                            class="text-gray-300 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5"
                                    x-text="formatDate(comment.created_at)"></p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="pl-0">
                            <p class="text-gray-600 text-[14px] font-medium leading-relaxed whitespace-pre-wrap px-1"
                                x-text="comment.content"></p>

                            <!-- Action Bar -->
                            <div class="mt-4 flex items-center gap-4">
                                <button @click="parentId = comment.id; $refs.commentInput.focus()"
                                    class="text-[10px] font-black text-brand-600 bg-brand-50/50 px-3 py-1.5 rounded-lg uppercase tracking-widest hover:bg-brand-100 transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    Reply
                                </button>
                            </div>

                            <!-- 2. Nested Replies (Inside the same container) -->
                            <div class="mt-6 pt-6 border-t border-gray-50 bg-gray-50/30 rounded-2xl p-4"
                                x-show="comment.replies && comment.replies.length > 0">
                                <!-- Facebook-style Toggle -->
                                <template x-if="comment.replies?.length >= 3 && !isExpanded(comment.id)">
                                    <button @click="toggleExpanded(comment.id)"
                                        class="flex items-center gap-3 py-2 px-4 rounded-xl bg-white border border-gray-100 hover:border-brand-200 transition-all shadow-sm mb-4 w-full justify-start text-left">
                                        <div class="flex -space-x-2">
                                            <template x-for="(reply, index) in comment.replies.slice(0, 3)"
                                                :key="reply.id">
                                                <div
                                                    class="w-6 h-6 rounded-full border-2 border-white bg-brand-50 shadow-sm overflow-hidden flex items-center justify-center">
                                                    <template x-if="reply.user.avatar">
                                                        <img :src="'/storage/' + reply.user.avatar"
                                                            class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!reply.user.avatar">
                                                        <span class="text-[8px] font-bold text-brand-600"
                                                            x-text="reply.user.name.charAt(0)"></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                        <span class="text-[11px] font-black text-gray-500 uppercase tracking-widest"
                                            x-text="'Show ' + comment.replies.length + ' Replies'"></span>
                                    </button>
                                </template>

                                <!-- Replies Vertical stream -->
                                <div class="space-y-6" x-show="isExpanded(comment.id) || comment.replies?.length < 3"
                                    x-collapse>
                                    <template x-for="reply in comment.replies" :key="reply.id">
                                        <div class="flex flex-col gap-2 relative">
                                            <!-- Vertical connection line -->
                                            <div class="absolute left-4 top-10 bottom-0 w-0.5 bg-gray-100 -ml-px"></div>

                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-brand-600 font-bold overflow-hidden border border-gray-100 flex-shrink-0 shadow-sm z-10">
                                                    <template x-if="reply.user.avatar">
                                                        <img :src="'/storage/' + reply.user.avatar"
                                                            class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!reply.user.avatar">
                                                        <span x-text="reply.user.name.charAt(0)"></span>
                                                    </template>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <h6 class="text-[11px] font-black text-gray-700"
                                                            x-text="reply.user.name"></h6>
                                                        <template x-if="reply.user_id === {{ auth()->id() }}">
                                                            <button @click="deleteComment(reply.id)"
                                                                class="text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                    <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest leading-none mt-0.5"
                                                        x-text="formatDate(reply.created_at)"></p>
                                                </div>
                                            </div>
                                            <div class="ml-11">
                                                <div
                                                    class="bg-white/60 p-2.5 rounded-xl border border-gray-100 shadow-sm relative w-full group-hover:border-brand-100 transition-all">
                                                    <p class="text-gray-600 text-[11px] leading-relaxed whitespace-pre-wrap"
                                                        x-text="reply.content"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="comment.replies?.length >= 3 && isExpanded(comment.id)">
                                        <button @click="toggleExpanded(comment.id)"
                                            class="mt-4 text-[10px] font-black text-gray-400 hover:text-brand-600 uppercase tracking-widest pl-11 transition-colors">
                                            Collapse Discussion
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Loading Spinner -->
            <div x-intersect="loadMore()" x-show="hasMore" class="py-10 flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-600"></div>
            </div>

            <div x-show="!hasMore && comments.length === 0"
                class="flex flex-col items-center justify-center py-20 text-gray-400">
                <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.827-1.233L3 20l1.341-5.022A9 9 0 1121 12z" />
                </svg>
                <p class="text-sm italic">Be the first to comment</p>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="p-4 bg-white border-t border-gray-100 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="max-w-2xl mx-auto">
            <!-- Reply Indicator -->
            <template x-if="parentId">
                <div
                    class="flex items-center justify-between bg-brand-50 px-3 py-1.5 rounded-t-xl border-x border-t border-brand-100 text-[10px] font-bold text-brand-700 uppercase tracking-widest">
                    <span x-text="'Replying to ' + comments.find(c => c.id === parentId)?.user.name"></span>
                    <button @click="parentId = null" class="hover:text-brand-900">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>

            <form @submit.prevent="submitComment()" class="relative flex gap-2">
                <input type="text" x-model="content" x-ref="commentInput"
                    :placeholder="parentId ? 'Write a reply...' : 'Add to the discussion...'"
                    class="flex-1 bg-gray-50 border-gray-100 rounded-xl text-sm py-3 px-4 focus:ring-brand-500 focus:border-brand-500 transition-all shadow-inner"
                    :class="parentId ? 'rounded-tl-none border-t-0' : ''">
                <button type="submit" :disabled="!content.trim() || submitting"
                    class="bg-brand-600 text-white rounded-xl px-5 hover:bg-brand-700 transition-all shadow-lg shadow-brand-200 flex items-center justify-center disabled:opacity-50 disabled:shadow-none">
                    <template x-if="!submitting">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </template>
                    <template x-if="submitting">
                        <div class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></div>
                    </template>
                </button>
            </form>
        </div>
    </div>
</div>