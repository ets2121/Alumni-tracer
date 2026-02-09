<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\Course;

class ChatManagementController extends Controller
{
    public function index()
    {
        // 1. Fetch groups with message counts
        $groups = ChatGroup::withCount(['messages', 'users'])->get();

        // 2. Pre-fetch counts for all scenarios to optimize loop (Universal Base)
        $globalAlumniCount = \App\Models\User::withoutGlobalScopes()->where('role', 'alumni')->where('status', 'active')->count();

        $deptAlumniCounts = \App\Models\AlumniProfile::withoutGlobalScopes()
            ->whereHas('user', fn($q) => $q->withoutGlobalScopes()->where('status', 'active'))
            ->select('department_name', \DB::raw('count(*) as count'))
            ->groupBy('department_name')
            ->pluck('count', 'department_name');

        $batchCounts = \App\Models\AlumniProfile::withoutGlobalScopes()
            ->whereHas('user', fn($q) => $q->withoutGlobalScopes()->where('status', 'active'))
            ->select('batch_year', 'department_name', \DB::raw('count(*) as count'))
            ->groupBy('batch_year', 'department_name')
            ->get();

        $courseCounts = \App\Models\AlumniProfile::withoutGlobalScopes()
            ->whereHas('user', fn($q) => $q->withoutGlobalScopes()->where('status', 'active'))
            ->select('course_id', 'department_name', \DB::raw('count(*) as count'))
            ->groupBy('course_id', 'department_name')
            ->get();

        $systemAdminCount = \App\Models\User::withoutGlobalScopes()->where('role', 'admin')->where('status', 'active')->count();
        $deptAdminCounts = \App\Models\User::withoutGlobalScopes()
            ->where('role', 'dept_admin')
            ->where('status', 'active')
            ->select('department_name', \DB::raw('count(*) as count'))
            ->groupBy('department_name')
            ->pluck('count', 'department_name');

        foreach ($groups as $group) {
            if ($group->type === 'general') {
                $group->members_count = $group->department_name
                    ? ($deptAlumniCounts[$group->department_name] ?? 0)
                    : $globalAlumniCount;
            } elseif ($group->type === 'batch') {
                if ($group->department_name) {
                    $group->members_count = $batchCounts->where('batch_year', $group->batch_year)
                        ->where('department_name', $group->department_name)
                        ->sum('count');
                } else {
                    $group->members_count = $batchCounts->where('batch_year', $group->batch_year)->sum('count');
                }
            } elseif ($group->type === 'course') {
                if ($group->department_name) {
                    $group->members_count = $courseCounts->where('course_id', $group->course_id)
                        ->where('department_name', $group->department_name)
                        ->sum('count');
                } else {
                    $group->members_count = $courseCounts->where('course_id', $group->course_id)->sum('count');
                }
            } elseif ($group->type === 'admin_dept') {
                if ($group->department_name) {
                    $group->members_count = $systemAdminCount + ($deptAdminCounts[$group->department_name] ?? 0);
                } else {
                    $group->members_count = $systemAdminCount + $deptAdminCounts->sum();
                }
            } else {
                $group->members_count = $group->users_count ?? 0;
            }
        }

        return view('admin.chat_management.index', compact('groups'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $coursesQuery = Course::query();
        if ($user->role === 'dept_admin') {
            $coursesQuery->where('department_name', $user->department_name);
        }
        $courses = $coursesQuery->get();

        $batchYears = \App\Models\AlumniProfile::distinct()
            ->whereNotNull('batch_year')
            ->orderBy('batch_year', 'desc')
            ->pluck('batch_year');

        if ($request->ajax()) {
            return view('admin.chat_management.partials._form', compact('courses', 'batchYears'));
        }

        return view('admin.chat_management.create', compact('courses', 'batchYears'));
    }



    public function show(ChatGroup $chat_management)
    {
        $group = $chat_management->load(['messages.user']);

        // Strictly fetch participants matching group criteria
        $participantsQuery = \App\Models\User::where('status', 'active')->withoutGlobalScopes();

        if ($group->type === 'admin_dept') {
            $participantsQuery->whereIn('role', ['admin', 'dept_admin']);
            if ($group->department_name) {
                $participantsQuery->where(function ($q) use ($group) {
                    $q->where('role', 'admin')
                        ->orWhere('department_name', $group->department_name);
                });
            }
        } else {
            $participantsQuery->where('role', 'alumni')->with('alumniProfile');

            // Explicit Manual Scoping to match Potential Counts
            if ($group->department_name) {
                $participantsQuery->where('department_name', $group->department_name);
            }

            if ($group->type === 'batch') {
                $participantsQuery->whereHas('alumniProfile', function ($q) use ($group) {
                    $q->where('batch_year', $group->batch_year);
                });
            } elseif ($group->type === 'course') {
                $participantsQuery->whereHas('alumniProfile', function ($q) use ($group) {
                    $q->where('course_id', $group->course_id);
                });
            }
        }

        $participants = $participantsQuery->get();

        return view('admin.chat_management.show', compact('group', 'participants'));
    }

    public function storeMessage(Request $request, ChatGroup $group)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $message = $group->messages()->create([
            'user_id' => \Auth::id(),
            'content' => $request->input('content'),
            'type' => 'text'
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 'Message posted.', 'message' => $message]);
        }

        return back()->with('success', 'Message posted as Moderator.');
    }

    public function destroy(ChatGroup $chat_management)
    {
        $chat_management->delete();
        return redirect()->route('admin.chat-management.index')->with('success', 'Group deleted successfully.');
    }

    public function deleteMessage(\App\Models\ChatMessage $message)
    {
        $message->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => 'Message deleted.']);
        }

        return back()->with('success', 'Message deleted by moderator.');
    }
    // ... (previous methods)

    // --- Banned Words Management ---

    public function bannedWords()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only System Administrators can manage banned words.');
        }

        $words = \App\Models\BannedWord::with('creator')->latest()->paginate(20);
        $isEnabled = \App\Models\SystemSetting::where('key', 'banned_words_enabled')->value('value') === '1';

        return view('admin.chat_management.banned_words', compact('words', 'isEnabled'));
    }

    public function toggleBannedWords(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $isEnabled = $request->has('enabled') ? '1' : '0';
        \App\Models\SystemSetting::updateOrCreate(
            ['key' => 'banned_words_enabled'],
            ['value' => $isEnabled]
        );

        return back()->with('success', 'Banned words feature updated.');
    }

    public function storeBannedWord(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate(['word' => 'required|string|max:50|unique:banned_words,word']);

        \App\Models\BannedWord::create([
            'word' => $request->word,
            'department_name' => null, // Always Global for System Admin
            'created_by' => auth()->id()
        ]);

        return back()->with('success', 'Banned word added.');
    }

    public function destroyBannedWord(\App\Models\BannedWord $bannedWord)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $bannedWord->delete();
        return back()->with('success', 'Banned word removed.');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:admin_dept,general,batch,course',
            'description' => 'nullable|string',
            'batch_year' => 'required_if:type,batch|nullable|integer',
            'course_id' => 'required_if:type,course|nullable|exists:courses,id',
        ]);

        // Role-Based Validation & Scoping
        if ($user->role === 'dept_admin') {
            // Dept Admin Restrictions
            if ($validated['type'] === 'admin_dept') {
                abort(403, 'Unauthorized. Dept Admins cannot create Admin Department groups.');
            }

            // Force scoped to their department
            $validated['department_name'] = $user->department_name;

            // If course-based, verify course belongs to their department
            if ($validated['type'] === 'course') {
                $course = Course::find($validated['course_id']);
                if ($course->department_name !== $user->department_name) {
                    return back()->with('error', 'Unauthorized course selecion.')->withInput();
                }
            }
        } else {
            // System Admin: Global (or optionally scoped if we added a field, but for now NULL)
            $validated['department_name'] = null;
        }

        ChatGroup::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => 'Group created successfully.']);
        }

        return redirect()->route('admin.chat-management.index')->with('success', 'Group created successfully.');
    }

    // ... (rest of controller)
}
