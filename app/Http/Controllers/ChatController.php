<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $this->syncUserGroups();
        return view('chat.index');
    }

    public function getGroups()
    {
        $user = Auth::user();
        if (!$user->alumniProfile) {
            return response()->json([]);
        }

        $profile = $user->alumniProfile;

        // Optimized filtering: Only show general, their specific batch, or their specific course
        $groups = ChatGroup::where(function ($query) use ($profile) {
            $query->where('type', 'general')
                ->orWhere(function ($q) use ($profile) {
                    $q->where('type', 'batch')->where('batch_year', $profile->batch_year);
                })
                ->orWhere(function ($q) use ($profile) {
                    $q->where('type', 'course')->where('course_id', $profile->course_id);
                });
        })
            ->with(['latestMessage.user'])
            ->get();

        return response()->json($groups);
    }

    public function getMessages(ChatGroup $group)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $profile = $user->alumniProfile;

            if (!$profile) {
                return response()->json(['error' => 'Alumni profile required'], 403);
            }

            // Strict Access Control: Prevent jumping into other batch/course rooms
            $isAuthorized = false;
            if ($group->type === 'general') {
                $isAuthorized = true;
            } elseif ($group->type === 'batch' && $group->batch_year == $profile->batch_year) {
                $isAuthorized = true;
            } elseif ($group->type === 'course' && $group->course_id == $profile->course_id) {
                $isAuthorized = true;
            }

            if (!$isAuthorized) {
                return response()->json(['error' => 'Access Denied: You do not belong to this group.'], 403);
            }
        }

        $messages = $group->messages()
            ->with('user:id,name,avatar,role')
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, ChatGroup $group)
    {
        $request->validate(['content' => 'required|string']);

        $message = $group->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'type' => 'text'
        ]);

        return response()->json($message->load('user:id,name,avatar,role'));
    }

    public function syncUserGroups()
    {
        $user = Auth::user();
        if (!$user->alumniProfile)
            return;

        $profile = $user->alumniProfile;

        // 1. Ensure General Chat exists and user is in it
        $generalChat = ChatGroup::firstOrCreate(
            ['type' => 'general'],
            ['name' => 'General Alumni Chat', 'description' => 'World-wide alumni discussion.']
        );
        $this->joinGroupIfNotMember($user, $generalChat);

        // 2. Batch Group
        if ($profile->batch_year) {
            $batchName = "Batch " . $profile->batch_year;
            $batchChat = ChatGroup::firstOrCreate(
                ['type' => 'batch', 'batch_year' => $profile->batch_year],
                ['name' => $batchName, 'description' => "Official room for {$batchName} alumni."]
            );
            $this->joinGroupIfNotMember($user, $batchChat);
        }

        // 3. Course Group
        if ($profile->course_id) {
            $course = Course::find($profile->course_id);
            if ($course) {
                $courseChat = ChatGroup::firstOrCreate(
                    ['type' => 'course', 'course_id' => $profile->course_id],
                    ['name' => $course->code . " Alumni", 'description' => "Graduates of {$course->name}."]
                );
                $this->joinGroupIfNotMember($user, $courseChat);
            }
        }
    }

    public function joinGroupIfNotMember($user, $group)
    {
        if (!$group->users()->where('user_id', $user->id)->exists()) {
            $group->users()->attach($user->id, ['role' => 'member']);
        }
    }
}
