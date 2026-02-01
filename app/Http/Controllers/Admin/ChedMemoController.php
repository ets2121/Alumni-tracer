<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChedMemo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChedMemoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $year = $request->query('year');

        $memos = ChedMemo::when($search, function ($q) use ($search) {
            return $q->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('memo_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        })
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->when($year, function ($q) use ($year) {
                return $q->whereYear('date_issued', $year);
            })
            ->latest('date_issued')
            ->paginate(10)
            ->withQueryString();

        $years = ChedMemo::selectRaw('YEAR(date_issued) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $categories = [
            'Graduate tracer study guidelines',
            'Alumni tracking requirements',
            'Institutional policies'
        ];

        if ($request->ajax()) {
            return view('admin.memos.partials._table', compact('memos'));
        }

        return view('admin.memos.index', compact('memos', 'search', 'years', 'categories'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.memos.partials._form');
        }
        return view('admin.memos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'memo_number' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'date_issued' => 'required|date',
            'file' => 'required|file|mimes:pdf,doc,docx|max:30720', // Increased to 30MB
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('memos', 'public');
            $validated['file_path'] = $path;
        }

        \App\Models\ChedMemo::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => 'CHED Memorandum uploaded successfully.']);
        }

        return redirect()->route('admin.memos.index')->with('success', 'CHED Memorandum uploaded successfully.');
    }

    public function edit(Request $request, \App\Models\ChedMemo $memo)
    {
        if ($request->ajax()) {
            return view('admin.memos.partials._form', compact('memo'));
        }
        return view('admin.memos.edit', compact('memo'));
    }

    public function update(Request $request, \App\Models\ChedMemo $memo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'memo_number' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'date_issued' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:30720',
        ]);

        if ($request->hasFile('file')) {
            if ($memo->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($memo->file_path);
            }
            $path = $request->file('file')->store('memos', 'public');
            $validated['file_path'] = $path;
        }

        $memo->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => 'CHED Memorandum updated successfully.']);
        }

        return redirect()->route('admin.memos.index')->with('success', 'CHED Memorandum updated successfully.');
    }

    public function destroy(Request $request, \App\Models\ChedMemo $memo)
    {
        if ($memo->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($memo->file_path);
        }
        $memo->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'CHED Memorandum deleted successfully.']);
        }

        return redirect()->route('admin.memos.index')->with('success', 'CHED Memorandum deleted successfully.');
    }
}
