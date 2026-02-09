<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\ChedGtsResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChedGtsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user already submitted
        $alreadySubmitted = ChedGtsResponse::where('user_id', $user->id)->exists();

        if ($alreadySubmitted) {
            return view('alumni.tracer.ched_completed');
        }

        return view('alumni.tracer.ched', compact('user'));
    }

    public function show()
    {
        $user = Auth::user();
        $response = ChedGtsResponse::where('user_id', $user->id)->firstOrFail();

        return view('alumni.tracer.ched_preview', [
            'user' => $user,
            'response' => $response->response_data
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Basic validation for the required sections
        $request->validate([
            'q1_name' => 'required|string|max:255',
            'q2_address' => 'required|string',
            'q3_email' => 'required|email|max:255',
            'q5_mobile' => 'required|string|max:20',
            'q6_civil_status' => 'required|string',
            'q7_sex' => 'required|string',
            'q8_month' => 'required',
            'q8_day' => 'required',
            'q8_year' => 'required',
            'q9_region' => 'required',
            'q10_province' => 'required',
            'q11_location' => 'required',
            'q16_employed' => 'required',
        ]);

        ChedGtsResponse::create([
            'user_id' => $user->id,
            'department_name' => $user->alumniProfile->department_name ?? null,
            'response_data' => $request->all(),
        ]);

        return redirect()->route('ched-gts.index')->with('success', 'GTS Survey submitted successfully.');
    }
}
