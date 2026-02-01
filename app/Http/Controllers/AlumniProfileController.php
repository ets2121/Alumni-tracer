<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->alumniProfile;
        $courses = Course::all();

        return view('alumni.profile.edit', compact('user', 'profile', 'courses'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date',
            'civil_status' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'batch_year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'employment_status' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'work_address' => 'nullable|string',
            'proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('proofs', 'public');
            $validatedData['proof_path'] = $path;
        }

        if ($user->latestEmployment) {
            $latest = $user->latestEmployment;
            $validatedData['company_name'] = $latest->company_name;
            $validatedData['position'] = $latest->position;
            $validatedData['work_address'] = $latest->location;
        }

        $profile = $user->alumniProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validatedData
        );

        return redirect()->route('alumni.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
