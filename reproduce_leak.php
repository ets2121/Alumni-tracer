<?php

use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;

// 1. Setup Data
// Group in CITCS
// Group in CITCS
ChatGroup::where('name', 'CITCS Secrets')->delete();
$citcsGroup = ChatGroup::firstOrCreate([
    'name' => 'CITCS Secrets',
    'type' => 'general',
    'department_name' => 'CITCS'
]);

// User in COE
$coeUser = User::where('role', 'alumni')->first();
if (!$coeUser)
    die("No Alumni found\n");

// Force user to be COE
$coeUser->department_name = 'COE';
$coeUser->save();
// Ensure profile exists and matches
if (!$coeUser->alumniProfile) {
    \App\Models\AlumniProfile::create([
        'user_id' => $coeUser->id,
        'department_name' => 'COE',
        // other required fields dummy
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@test.com',
        'student_id' => '000',
        'gender' => 'Male',
        'contact_number' => '000',
        'batch_year' => 2020,
        'course_id' => 1,
        'employment_status' => 'Unemployed',
        'address' => 'Test',
        'dob' => '2000-01-01',
        'civil_status' => 'Single'
    ]);
    $coeUser->refresh();
} else {
    // Force update via DB to avoid Model events/cache
    \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $coeUser->id)
        ->update(['department_name' => 'COE']);

    \Illuminate\Support\Facades\DB::table('alumni_profiles')
        ->where('user_id', $coeUser->id)
        ->update(['department_name' => 'COE']);
}

// FORCE RELOAD to ensure relationships are fresh
$coeUser = User::find($coeUser->id);
$coeUser->load('alumniProfile');

Auth::login($coeUser);

echo "Logged in as {$coeUser->name} (Dept: {$coeUser->department_name})\n";
echo "Profile Dept: {$coeUser->alumniProfile->department_name}\n"; // Debug verify
echo "Target Group: {$citcsGroup->name} (Dept: {$citcsGroup->department_name})\n";

// 2. Test Listing (getGroups)
$controller = new ChatController();
\Illuminate\Support\Facades\DB::flushQueryLog();
\Illuminate\Support\Facades\DB::enableQueryLog();
$response = $controller->getGroups();
$queries = \Illuminate\Support\Facades\DB::getQueryLog();
echo "SQL DEBUG: " . json_encode($queries) . "\n";
$groups = $response->getData(); // It returns JSON
$groups = $response->getData(); // It returns JSON

$canSee = false;
foreach ($groups as $g) {
    if ($g->id == $citcsGroup->id) {
        $canSee = true;
        break;
    }
}

if ($canSee) {
    echo "❌ FAIL: Alumni from COE can can see CITCS group!\n";
} else {
    echo "✅ PASS: Alumni from COE CANNOT see CITCS group.\n";
}

// 3. Test Access (getMessages)
try {
    $msgResponse = $controller->getMessages($citcsGroup);
    if ($msgResponse->status() == 200) {
        echo "❌ FAIL: Alumni from COE can READ messages in CITCS group!\n";
    } else {
        echo "✅ PASS: Access Denied to CITCS group (" . $msgResponse->status() . ")\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
