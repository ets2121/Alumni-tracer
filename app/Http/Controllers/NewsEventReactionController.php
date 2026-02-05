<?php

namespace App\Http\Controllers;

use App\Models\NewsEvent;
use App\Models\NewsEventReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsEventReactionController extends Controller
{
    public function toggle(Request $request, NewsEvent $news_event)
    {
        $userId = Auth::id();
        $type = $request->input('type', 'like');

        $existing = NewsEventReaction::where('news_event_id', $news_event->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->type === $type) {
                $existing->delete();
                $status = 'removed';
            } else {
                $existing->update(['type' => $type]);
                $status = 'updated';
            }
        } else {
            NewsEventReaction::create([
                'news_event_id' => $news_event->id,
                'user_id' => $userId,
                'type' => $type
            ]);
            $status = 'added';
        }

        return response()->json([
            'status' => 'success',
            'action' => $status,
            'count' => $news_event->reactions()->count()
        ]);
    }
}
