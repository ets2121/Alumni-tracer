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

        // 2. Optimized counting for each type to avoid heavy pivot joins
        $activeAlumniCount = \App\Models\User::where('role', 'alumni')->where('status', 'active')->count();

        // Batch counts
        $batchCounts = \App\Models\AlumniProfile::whereHas('user', function ($q) {
            $q->where('status', 'active');
        })
            ->select('batch_year', \DB::raw('count(*) as count'))
            ->groupBy('batch_year')
            ->pluck('count', 'batch_year');

        // Course counts
        $courseCounts = \App\Models\AlumniProfile::whereHas('user', function ($q) {
            $q->where('status', 'active');
        })
            ->select('course_id', \DB::raw('count(*) as count'))
            ->groupBy('course_id')
            ->pluck('count', 'course_id');

        foreach ($groups as $group) {
            if ($group->type === 'general') {
                $group->members_count = $activeAlumniCount;
            } elseif ($group->type === 'batch') {
                $group->members_count = $batchCounts[$group->batch_year] ?? 0;
            } elseif ($group->type === 'course') {
                $group->members_count = $courseCounts[$group->course_id] ?? 0;
            } else {
                $group->members_count = $group->users_count ?? 0;
            }
        }

        return view('admin.chat_management.index', compact('groups'));
    }

    public function create(Request $request)
    {
        $courses = Course::all();
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
        $participantsQuery = \App\Models\User::where('status', 'active')
            ->where('role', 'alumni')
            ->with('alumniProfile');

        if ($group->type === 'batch') {
            $participantsQuery->whereHas('alumniProfile', function ($q) use ($group) {
                $q->where('batch_year', $group->batch_year);
            });
        } elseif ($group->type === 'course') {
            $participantsQuery->whereHas('alumniProfile', function ($q) use ($group) {
                $q->where('course_id', $group->course_id);
            });
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
        // System Admin sees all (or filtered), Dept Admin sees only theirs
        $words = \App\Models\BannedWord::with('creator')->latest()->paginate(20);
        return view('admin.chat_management.banned_words', compact('words'));
    }

    public function storeBannedWord(Request $request)
    {
        $request->validate(['word' => 'required|string|max:50|unique:banned_words,word,NULL,id,department_name,' . (auth()->user()->department_name ?? 'NULL')]);

        \App\Models\BannedWord::create([
            'word' => $request->word,
            'department_name' => auth()->user()->department_name, // NULL for System Admin, 'Dept' for Dept Admin
            'created_by' => auth()->id()
        ]);

        return back()->with('success', 'Banned word added.');
    }

    public function destroyBannedWord(\App\Models\BannedWord $bannedWord)
    {
        // Policy check usually goes here, but for now rely on Scope/Trait
        if (auth()->user()->role !== 'admin' && $bannedWord->department_name !== auth()->user()->department_name) {
            abort(403);
        }
        $bannedWord->delete();
        return back()->with('success', 'Banned word removed.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:batch,course,general,dept_admin',
            'description' => 'nullable|string',
            'batch_year' => 'required_if:type,batch|nullable|integer',
            'course_id' => 'required_if:type,course|nullable|exists:courses,id',
            'is_private' => 'boolean'
        ]);

        if ($validated['type'] === 'dept_admin' && auth()->user()->role !== 'admin') {
            abort(403, 'Only System Administrators can create Department Admin groups.');
        }

        // Explicitly enforce department isolation for Dept Admins
        if (auth()->user()->role === 'dept_admin') {
            $validated['department_name'] = auth()->user()->department_name;
        }

        ChatGroup::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => 'Group created successfully.']);
        }

        return redirect()->route('admin.chat-management.index')->with('success', 'Group created successfully.');
    }

    // ... (rest of controller)
}
