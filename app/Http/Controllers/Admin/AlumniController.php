<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort', 'name');
        $sortDir = $request->query('direction', 'asc');

        $query = \App\Models\User::where('role', 'alumni')
            ->where('status', 'active')
            ->with(['alumniProfile.course'])
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $alumni = $query->orderBy($sortBy, $sortDir)->paginate(15)->withQueryString();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json($alumni);
        }

        if ($request->ajax()) {
            return view('admin.alumni.partials._table_content', compact('alumni', 'search', 'sortBy', 'sortDir'));
        }

        return view('admin.alumni.index', compact('alumni', 'search', 'sortBy', 'sortDir'));
    }

    public function create(Request $request)
    {
        $coursesQuery = \App\Models\Course::query()->orderBy('code');

        $user = $request->user();
        if ($user->role === 'dept_admin' && $user->department_name) {
            $coursesQuery->where('department_name', $user->department_name);
        }

        $courses = $coursesQuery->get();

        if ($request->ajax()) {
            return view('admin.alumni.partials._create_form', compact('courses'));
        }
        return view('admin.alumni.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'course_id' => 'required|exists:courses,id',
            'batch_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'dob' => 'required|date|before:today',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            // 1. Create User
            $user = \App\Models\User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'alumni',
                'status' => 'active', // Manually added by admin/mod, so auto-active
            ]);

            // 2. Create Profile
            // Note: Department assignment is handled by Observer in AlumniProfile model based on Course
            // or by HasDepartmentIsolation Global Scope if the creator is a Dept Admin

            $user->alumniProfile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'course_id' => $request->course_id,
                'batch_year' => $request->batch_year,
                'dob' => $request->dob,
                // Default required fields
                'gender' => $request->gender ?? 'Prefer not to say',
                'civil_status' => 'Single',
                'contact_number' => 'N/A', // Defaulting required fields not in quick form
                'address' => 'N/A',
                'employment_status' => 'Unemployed', // Default to Unemployed
            ]);

            // 3. Mark email as verified since admin created it
            $user->markEmailAsVerified();
        });

        if ($request->ajax()) {
            return response()->json(['success' => 'Alumni registered successfully.']);
        }

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni registered successfully.');
    }

    public function show(Request $request, string $id)
    {
        $alumni = \App\Models\User::with(['alumniProfile.course', 'employmentHistories'])->findOrFail($id);
        if ($request->ajax()) {
            return view('admin.alumni.partials._profile_detail', compact('alumni'));
        }
        return view('admin.alumni.show', compact('alumni'));
    }

    public function edit(Request $request, string $id)
    {
        $alumni = \App\Models\User::with(['alumniProfile.course', 'employmentHistories'])->findOrFail($id);
        if ($request->ajax()) {
            return view('admin.alumni.partials._review_form', compact('alumni'));
        }
        return view('admin.alumni.edit', compact('alumni'));
    }

    public function update(Request $request, string $id)
    {
        $alumni = \App\Models\User::findOrFail($id);
        $oldStatus = $alumni->status;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'status' => 'required|in:pending,active,rejected',
            'admin_remarks' => 'nullable|string',
        ]);

        $alumni->update($request->only('name', 'email', 'status', 'admin_remarks'));

        // Trigger notification when approved or rejected
        if ($oldStatus !== 'active' && $alumni->status === 'active') {
            try {
                $alumni->notify(new \App\Notifications\RegistrationApproved($alumni));

                // AUTOMATED: Send invitations for upcoming events matching this alumni's profile
                $upcomingEvents = \App\Models\NewsEvent::where('type', 'event')
                    ->where('event_date', '>=', now())
                    ->where(function ($query) use ($alumni) {
                        $profile = $alumni->alumniProfile;
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
                    ->get();

                foreach ($upcomingEvents as $event) {
                    $alumni->notify(new \App\Notifications\EventInvitation($event));
                }
            } catch (\Exception $e) {
                // Log the error but don't block the update
                \Illuminate\Support\Facades\Log::error("Failed to process approval notifications: " . $e->getMessage());
            }
        } elseif ($oldStatus !== 'rejected' && $alumni->status === 'rejected') {
            try {
                $alumni->notify(new \App\Notifications\RegistrationRejected($alumni, $alumni->admin_remarks));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send rejection notification: " . $e->getMessage());
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Alumni status updated successfully.']);
        }

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni updated successfully.');
    }

    public function destroy(Request $request, string $id)
    {
        \App\Models\User::findOrFail($id)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Alumni record deleted successfully.']);
        }

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni deleted successfully.');
    }
}
