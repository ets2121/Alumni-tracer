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
        return view('chat.index');
    }

    public function getGroups()
    {
        $user = Auth::user();

        // 1. System Admin: See EVERYTHING
        if ($user->role === 'admin') {
            return response()->json(ChatGroup::with(['latestMessage.user'])->get());
        }

        // 2. Department Admin: See Dept groups + All Admin Dept groups
        if ($user->role === 'dept_admin') {
            $groups = ChatGroup::where('department_name', $user->department_name)
                ->orWhere('type', 'admin_dept')
                ->with(['latestMessage.user'])
                ->get();
            return response()->json($groups);
        }

        // 3. Alumni: Strict eligible groups
        $profile = $user->alumniProfile;
        if (!$profile) {
            return response()->json([]);
        }

        $groups = ChatGroup::where(function ($query) use ($profile) {
            // Must NEVER see 'admin_dept'
            $query->where('type', '!=', 'admin_dept');

            $query->where(function ($q) use ($profile) {
                // A. Dept Name Only (General) Group
                $q->where('type', 'general')
                    ->where('department_name', $profile->department_name);
            })
                ->orWhere(function ($q) use ($profile) {
                    // B. Batch-Based Group
                    $q->where('type', 'batch')
                        ->where('batch_year', $profile->batch_year)
                        ->where(function ($sq) use ($profile) {
                        // Scoped to Dept OR Global (created by Sys Admin)
                        $sq->where('department_name', $profile->department_name)
                            ->orWhereNull('department_name');
                    });
                })
                ->orWhere(function ($q) use ($profile) {
                    // C. Course-Based Group
                    $q->where('type', 'course')
                        ->where('course_id', $profile->course_id)
                        ->where(function ($sq) use ($profile) {
                        // Scoped to Dept OR Global (created by Sys Admin)
                        $sq->where('department_name', $profile->department_name)
                            ->orWhereNull('department_name');
                    });
                });
        })
            ->with(['latestMessage.user'])
            ->get();

        return response()->json($groups);
    }

    public function getMessages(ChatGroup $group)
    {
        $this->checkGroupAccess($group);

        $messages = $group->messages()
            ->with('user:id,name,avatar,role')
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, ChatGroup $group, \App\Services\MessageFilterService $filterService)
    {
        $this->checkGroupAccess($group);

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

    private function checkGroupAccess(ChatGroup $group)
    {
        $user = Auth::user();

        // 1. System Admin: Global Access
        if ($user->role === 'admin') {
            return;
        }

        // 2. Dept Admin: Dept scoped + Admin Dept
        if ($user->role === 'dept_admin') {
            if ($group->type !== 'admin_dept' && $group->department_name !== $user->department_name) {
                abort(403, 'Access Denied: Different Department.');
            }
            return;
        }

        // 3. Alumni: Strict access matching
        $profile = $user->alumniProfile;
        if (!$profile) {
            abort(403, 'Alumni profile required');
        }

        // Hard Block: Admin Dept
        if ($group->type === 'admin_dept') {
            abort(403, 'Access Denied: Admin Room.');
        }

        // Scoping: If group is department-scoped, it must match user's department
        if ($group->department_name && $group->department_name !== $profile->department_name) {
            abort(403, 'Access Denied: Different Department.');
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
            abort(403, 'Access Denied: You do not meet the group criteria.');
        }
    }
}
