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

        if ($request->ajax()) {
            return view('admin.alumni.partials._table_content', compact('alumni', 'search', 'sortBy', 'sortDir'));
        }

        return view('admin.alumni.index', compact('alumni', 'search', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        // Optional: specific View for creating alumni if needed
    }

    public function store(Request $request)
    {
        //
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

        // Trigger notification when approved
        if ($oldStatus !== 'active' && $alumni->status === 'active') {
            try {
                $alumni->notify(new \App\Notifications\RegistrationApproved($alumni));
            } catch (\Exception $e) {
                // Log the error but don't block the update
                \Illuminate\Support\Facades\Log::error("Failed to send approval email: " . $e->getMessage());
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
