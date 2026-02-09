<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isSystemAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if ($request->wantsJson() || $request->query('format') === 'json') {
            $search = $request->query('search');
            $users = \App\Models\User::whereIn('role', ['admin', 'dept_admin'])
                ->when($search, function ($q) use ($search) {
                    return $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->latest()
                ->paginate(15);

            return response()->json($users);
        }

        // For initial page load, we only need departments for the modal.
        // The table data will be fetched by Alpine.
        $departments = \App\Models\Course::distinct()->pluck('department_name')->filter()->values();

        return view('admin.users.index', compact('departments'));
    }

    public function create()
    {
        // Handled by modal in index
    }

    public function store(Request $request)
    {
        if (!$request->user()->isSystemAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,dept_admin',
            'department_name' => 'required_if:role,dept_admin|nullable|string',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'department_name' => $request->role === 'dept_admin' ? $request->department_name : null,
            'status' => 'active',
        ]);

        return response()->json(['success' => 'User created successfully.']);
    }

    public function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        if (!$request->user()->isSystemAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,dept_admin',
            'department_name' => 'required_if:role,dept_admin|nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department_name' => $request->role === 'dept_admin' ? $request->department_name : null,
        ];

        if ($request->password) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['success' => 'User updated successfully.']);
    }

    public function destroy(string $id)
    {
        if (!auth()->user()->isSystemAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $user = \App\Models\User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot delete yourself.'], 422);
        }

        $user->delete();

        return response()->json(['success' => 'User deleted successfully.']);
    }
}
