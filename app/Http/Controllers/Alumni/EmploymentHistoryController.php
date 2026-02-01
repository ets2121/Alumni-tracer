<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmploymentHistoryController extends Controller
{
    public function store(Request $request)
    {
        // Fix: Force boolean for is_current checkbox
        $request->merge(['is_current' => $request->boolean('is_current')]);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $request->user()->employmentHistories()->create($validated);

        if ($validated['is_current']) {
            $this->syncToProfile($request->user(), $validated);
        }

        return back()->with('success', 'Employment history added successfully.');
    }

    public function update(Request $request, $id)
    {
        $history = $request->user()->employmentHistories()->findOrFail($id);

        // Fix: Force boolean for is_current checkbox
        $request->merge(['is_current' => $request->boolean('is_current')]);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $history->update($validated);

        if ($validated['is_current']) {
            $this->syncToProfile($request->user(), $validated);
        }

        return back()->with('success', 'Employment history updated successfully.');
    }

    private function syncToProfile($user, $data)
    {
        // Mark all other roles as not current if this one is current
        $user->employmentHistories()
            ->where('id', '!=', $user->employmentHistories()->latest()->first()->id ?? 0)
            ->update(['is_current' => false]);

        $profile = $user->alumniProfile;
        if ($profile) {
            $profile->update([
                'company_name' => $data['company_name'],
                'position' => $data['position'],
                'work_address' => $data['location'] ?? null,
                'employment_status' => 'Employed' // Automatically set to Employed if adding a current job
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $history = $request->user()->employmentHistories()->findOrFail($id);
        $history->delete();

        return back()->with('success', 'Employment history deleted successfully.');
    }
}
