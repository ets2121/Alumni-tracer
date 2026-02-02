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
        $profile = $user->alumniProfile;
        $tab = $request->query('tab', 'all');
        $page = $request->query('page', 1);

        $query = NewsEvent::query();

        // Targeting Logic
        $query->where(function ($q) use ($profile) {
            $q->where('target_type', 'all')
                ->orWhere(function ($sq) use ($profile) {
                    $sq->where('target_type', 'batch')
                        ->where('target_batch', $profile->graduation_year);
                })
                ->orWhere(function ($sq) use ($profile) {
                    $sq->where('target_type', 'course')
                        ->where('target_course_id', $profile->course_id);
                })
                ->orWhere(function ($sq) use ($profile) {
                    $sq->where('target_type', 'batch_course')
                        ->where('target_batch', $profile->graduation_year)
                        ->where('target_course_id', $profile->course_id);
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
        }

        // Global Announcement/Pinned Logic
        // Pinned first, then latest
        $posts = $query->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        if ($request->ajax()) {
            return view('alumni.partials._feed_items', compact('posts'))->render();
        }

        return response()->json($posts);
    }
}
