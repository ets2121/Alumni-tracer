<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\NewsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniFeedController extends Controller
{
    public function fetch(Request $request)
    {
        $user = Auth::user();

        if ($user->status !== 'active') {
            return response()->json([
                'html' => '<div class="flex flex-col items-center justify-center p-12 text-center">
                            <p class="text-gray-500 dark:text-dark-text-muted font-medium">Account verification required to view feed content.</p>
                          </div>',
                'next_cursor' => null,
                'has_more' => false
            ]);
        }

        $profile = $user->alumniProfile;
        $tab = $request->query('tab', 'all');
        $page = $request->query('page', 1);

        $query = NewsEvent::query();

        // Simplified Visibility Logic
        $query->where(function ($q) use ($user) {
            $q->where('visibility_type', 'all')
                ->orWhere(function ($sq) use ($user) {
                    $sq->where('visibility_type', 'department')
                        ->where('department_name', $user->department_name);
                });
        });

        // Tab Filtering
        switch ($tab) {
            case 'news':
                $query->where('type', 'news');
                break;
            case 'event':
                $query->where('type', 'event')
                    ->where(function ($q) {
                        $q->whereNull('event_date')
                            ->orWhere('event_date', '>=', now());
                    });
                break;
            case 'announcement':
                $query->where('type', 'announcement')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
                break;
            case 'job':
                $query->where('type', 'job')
                    ->where(function ($q) {
                        $q->whereNull('job_deadline')
                            ->orWhere('job_deadline', '>=', now());
                    });
                break;
        }

        // Global Announcement/Pinned Logic
        // Pinned first, then latest
        $posts = $query->withCount(['reactions', 'comments'])
            ->with(['userReaction', 'photos'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->cursorPaginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('alumni.partials._feed_items', compact('posts'))->render(),
                'next_cursor' => $posts->nextCursor()?->encode(),
                'has_more' => $posts->hasMorePages()
            ]);
        }

        return response()->json($posts);
    }
}
