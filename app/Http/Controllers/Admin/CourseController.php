<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $courses = \App\Models\Course::withCount('alumni')
            ->when($search, function ($q) use ($search) {
                return $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('department_name', 'like', "%{$search}%");
            })->latest()->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('admin.courses.partials._table', compact('courses'));
        }

        return view('admin.courses.index', compact('courses', 'search'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.courses.partials._form');
        }
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code',
            'name' => 'required|string|max:255',
            'department_name' => 'required|string|max:50',
            'category' => 'required|in:Undergraduate,Graduate,Certificate',
            'description' => 'nullable|string',
        ]);

        \App\Models\Course::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => 'Course created successfully.']);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function show(string $id)
    {
        $course = \App\Models\Course::withCount('alumni')->findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Request $request, string $id)
    {
        $course = \App\Models\Course::findOrFail($id);
        if ($request->ajax()) {
            return view('admin.courses.partials._form', compact('course'));
        }
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, string $id)
    {
        $course = \App\Models\Course::findOrFail($id);
        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code,' . $id,
            'name' => 'required|string|max:255',
            'department_name' => 'required|string|max:50',
            'category' => 'required|in:Undergraduate,Graduate,Certificate',
            'description' => 'nullable|string',
        ]);

        $course->update($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => 'Course updated successfully.']);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Request $request, string $id)
    {
        $course = \App\Models\Course::withCount('alumni')->findOrFail($id);

        if ($course->alumni_count > 0) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Cannot delete course with associated alumni.'], 422);
            }
            return redirect()->back()->with('error', 'Cannot delete course with associated alumni.');
        }

        $course->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Course deleted successfully.']);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
