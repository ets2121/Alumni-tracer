<?php

use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\Course;

// 1. Check for Alumni with NULL department_name
$floatingAlumni = AlumniProfile::whereNull('department_name')->count();
echo "Alumni with NULL department: $floatingAlumni\n";

if ($floatingAlumni > 0) {
    echo "  -> CRITICAL: These alumni bypass granular isolation. Listing IDs...\n";
    $ids = AlumniProfile::whereNull('department_name')->pluck('id')->take(5);
    echo "  ids: " . $ids->implode(', ') . "\n";
}

// 2. Check for Mismatch between Profile Dept and User Dept
// They should be identical for performance filtering
echo "Checking Profile <-> User Dept Sync...\n";
$mismatches = AlumniProfile::join('users', 'users.id', '=', 'alumni_profiles.user_id')
    ->whereColumn('users.department_name', '!=', 'alumni_profiles.department_name')
    ->count();

echo "Sync Mismatches: $mismatches\n";

// 3. Check for Courses with NULL department
$badCourses = Course::whereNull('department_name')->count();
echo "Courses with NULL department: $badCourses\n";
