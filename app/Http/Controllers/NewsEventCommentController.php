<?php

namespace App\Http\Controllers;

use App\Models\NewsEvent;
use App\Models\NewsEventComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsEventCommentController extends Controller
{
    public function index(NewsEvent $news_event)
    {
        $comments = NewsEventComment::where('news_event_id', $news_event->id)
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies.user' => function ($q) {
                    $q->latest();
                }
            ])
            ->latest()
            ->cursorPaginate(10);

        return response()->json($comments);
    }

    public function getDiscussionModal(NewsEvent $news_event)
    {
        $news_event->loadCount(['reactions', 'comments'])->load(['userReaction', 'photos']);

        // Load initial batch of comments
        $initialComments = NewsEventComment::where('news_event_id', $news_event->id)
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies.user' => function ($q) {
                    $q->latest();
                }
            ])
            ->latest()
            ->paginate(10);

        return view('alumni.partials._comments_modal', compact('news_event', 'initialComments'));
    }

    public function store(Request $request, NewsEvent $news_event)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:news_event_comments,id'
        ]);

        $comment = NewsEventComment::create([
            'news_event_id' => $news_event->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content')
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'comment' => $comment->load('user'),
                'count' => $news_event->comments()->count()
            ]);
        }

        return back()->with('success', 'Comment posted.');
    }

    public function destroy(NewsEventComment $comment)
    {
        // Only author or admin can delete
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'dept_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['status' => 'success']);
    }
}
