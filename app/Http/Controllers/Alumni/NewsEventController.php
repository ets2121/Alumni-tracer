<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\NewsEvent;
use Illuminate\Http\Request;

class NewsEventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');

        $user = $request->user();
        $profile = $user->alumniProfile;

        $newsEvents = NewsEvent::query()
            ->where(function ($query) use ($profile) {
                $query->where('target_type', 'all')
                    ->orWhere(function ($q) use ($profile) {
                        $q->where('target_type', 'batch')
                            ->where('target_batch', $profile->batch_year ?? $profile->graduation_year);
                    })
                    ->orWhere(function ($q) use ($profile) {
                        $q->where('target_type', 'course')
                            ->where('target_course_id', $profile->course_id);
                    })
                    ->orWhere(function ($q) use ($profile) {
                        $q->where('target_type', 'batch_course')
                            ->where('target_batch', $profile->batch_year ?? $profile->graduation_year)
                            ->where('target_course_id', $profile->course_id);
                    });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($q) use ($type) {
                if ($type === 'pinned') {
                    $q->where('is_pinned', true);
                } elseif (in_array($type, ['news', 'event', 'announcement'])) {
                    $q->where('type', $type);
                }
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        if ($request->ajax()) {
            return view('alumni.news.partials._list', compact('newsEvents'));
        }

        return view('alumni.news.index', compact('newsEvents', 'search'));
    }

    public function show(NewsEvent $newsEvent)
    {
        return view('alumni.news.show', compact('newsEvent'));
    }
}
