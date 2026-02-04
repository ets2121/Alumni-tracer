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

        // Optimized filtering: Strict Dept Isolation
        // 1. General groups MUST belong to their Department
        // 2. Batch/Course groups strictly isolate via relationships naturally, 
        //    but we add dept check to be safe against data integrity issues.
        $groups = ChatGroup::where(function ($query) use ($profile) {
            $query->where(function ($q) use ($profile) {
                $q->where('type', 'general')
                    ->where('department_name', $profile->department_name);
            })
                ->orWhere(function ($q) use ($profile) {
                    $q->where('type', 'batch')
                        ->where('batch_year', $profile->batch_year)
                        ->where('department_name', $profile->department_name);
                })
                ->orWhere(function ($q) use ($profile) {
                    $q->where('type', 'course')
                        ->where('course_id', $profile->course_id)
                        ->where('department_name', $profile->department_name);
                });
        })
            ->with(['latestMessage.user'])
            ->get();

        return response()->json($groups);
    }

    public function getMessages(ChatGroup $group)
    {
        $user = Auth::user();

        // Admin Bypass
        if ($user->role === 'admin') {
            // Admin has global access
        }
        // Dept Admin Access
        elseif ($user->role === 'dept_admin') {
            if ($user->department_name !== $group->department_name) {
                // Check if group is global? Dept admin shouldn't see global groups unless authorized?
                // Actually, general groups might have NULL department_name. 
                // If group->department_name is NULL (Global), Dept Admin can see it? Setup says strict isolation.
                // Let's assume strict isolation: Dept Admin only sees their Dept groups.

                // However, "General Group" might be Department-Specific General Group.

                // Let's use the Scope Trait logic essentially.
                if ($group->department_name && $group->department_name !== $user->department_name) {
                    return response()->json(['error' => 'Access Denied: Different Department.'], 403);
                }
                // If group is Global (NULL), Dept Admin can access if it's meant for them (e.g. Dept Admin Group).
                // For now, allow if department matches OR if group is scoped to them via relationship manually.
            }
        }
        // Alumni Access
        else {
            $profile = $user->alumniProfile;

            if (!$profile) {
                return response()->json(['error' => 'Alumni profile required'], 403);
            }

            // CRITICAL: Strict Department Isolation
            if ($group->department_name && $group->department_name !== $profile->department_name) {
                return response()->json(['error' => 'Access Denied: Different Department.'], 403);
            }

            // Strict Access Control: Prevent jumping into other batch/course rooms
            $isAuthorized = false;
            // ... (rest of alumni logic)
            if ($group->type === 'general') {
                $isAuthorized = true;
            } elseif ($group->type === 'batch' && $group->batch_year == $profile->batch_year) {
                $isAuthorized = true;
            } elseif ($group->type === 'course' && $group->course_id == $profile->course_id) {
                $isAuthorized = true;
            } else {
                // Check if user is attached to group manually
                if ($group->users()->where('user_id', $user->id)->exists()) {
                    $isAuthorized = true;
                }
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

    public function sendMessage(Request $request, ChatGroup $group, \App\Services\MessageFilterService $filterService)
    {
        $request->validate(['content' => 'required|string']);

        $user = Auth::user();

        try {
            $filterService->validateConfirmed($request->input('content'), $user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $message = $group->messages()->create([
            'user_id' => $user->id,
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
