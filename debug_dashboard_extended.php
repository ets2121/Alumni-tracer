<?php

use App\Models\User;
use App\Models\NewsEvent;
use Illuminate\Support\Facades\Auth;

// 1. Find a Dept Admin
$deptAdmin = User::where('role', 'dept_admin')->first();
if (!$deptAdmin) {
    die("No Dept Admin found.\n");
}
echo "Testing as User ID: " . $deptAdmin->id . " (Dept: " . $deptAdmin->department_name . ")\n";

// 2. Count TOTAL events (without scope)
$totalEvents = NewsEvent::withoutGlobalScopes()->count();
$globalEvents = NewsEvent::withoutGlobalScopes()->whereNull('department_name')->count();
$deptEvents = NewsEvent::withoutGlobalScopes()->where('department_name', $deptAdmin->department_name)->count();

echo "DB Stats:\n";
echo "- Total Events in DB: $totalEvents\n";
echo "- Global Events (Null Dept): $globalEvents\n";
echo "- Dept Events ($deptAdmin->department_name): $deptEvents\n";

// 3. Simulate Login and Check Scope
Auth::login($deptAdmin);
$visibleEvents = NewsEvent::count();

echo "Visible to Dept Admin: $visibleEvents\n";

if ($visibleEvents < ($deptEvents + $globalEvents)) {
    echo "ISSUE DETECTED: Dept Admin is seeing fewer events than (Dept + Global).\n";
    echo "Expected: " . ($deptEvents + $globalEvents) . "\n";
    echo "Actual: $visibleEvents\n";
    echo "Global Events are likely HIDDEN.\n";
} else {
    echo "Visibility seems correct.\n";
}

// 4. Check Check Integrity
$badAdmins = User::where('role', 'dept_admin')->whereNull('department_name')->count();
if ($badAdmins > 0) {
    echo "CRITICAL: Found $badAdmins Dept Admins with NULL department_name!\n";
}
