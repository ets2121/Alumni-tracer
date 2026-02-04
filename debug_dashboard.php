<?php

use App\Models\User;
use App\Models\NewsEvent;
use App\Models\GalleryAlbum;
use Illuminate\Support\Facades\Auth;

// 1. Find a Dept Admin
$deptAdmin = User::where('role', 'dept_admin')->first();

if (!$deptAdmin) {
    // Create one if missing for testing
    echo "Creating dummy Dept Admin...\n";
    $deptAdmin = User::create([
        'name' => 'Debug Admin',
        'email' => 'debug_dept_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role' => 'dept_admin',
        'department_name' => 'BSCS', // Assume BSCS exists (or any string)
        'status' => 'active'
    ]);
}

echo "Testing as User ID: " . $deptAdmin->id . " (Dept: " . $deptAdmin->department_name . ")\n";

// 2. Simulate Login for Scope
Auth::login($deptAdmin);

try {
    echo "Querying Users count... ";
    $userCount = User::where('role', 'alumni')->where('status', 'active')->count();
    echo "OK ($userCount)\n";
} catch (\Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "Querying NewsEvent count... ";
    $eventCount = NewsEvent::count();
    echo "OK ($eventCount)\n";
} catch (\Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

try {
    echo "Querying GalleryAlbum count... ";
    $galleryCount = GalleryAlbum::count();
    echo "OK ($galleryCount)\n";
} catch (\Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
