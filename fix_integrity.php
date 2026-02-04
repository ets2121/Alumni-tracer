<?php

use App\Models\Course;
use App\Models\AlumniProfile;
use App\Models\User;

echo "Starting Integrity Fix...\n";

// 1. Map Orphans to Departments
$mappings = [
    'BSIS' => 'CITCS',
    'MIT' => 'CITCS',
    'CCS' => 'CITCS',
    'CCNA' => 'CITCS',
    'BSCE' => 'COE', // College of Engineering
    'BSBA' => 'CBA', // College of Business Administration
    'MBA' => 'CBA',
];

foreach ($mappings as $code => $dept) {
    $updated = Course::where('code', $code)->update(['department_name' => $dept]);
    if ($updated) {
        echo "Updated $code -> $dept\n";
    } else {
        echo "Skipped $code (Not found)\n";
    }
}

// 2. Sync Alumni Profiles based on updated Courses
echo "Syncing Alumni Profiles...\n";
$profiles = AlumniProfile::whereNull('department_name')->get();
$count = 0;

foreach ($profiles as $profile) {
    if ($profile->course && $profile->course->department_name) {
        $profile->department_name = $profile->course->department_name;
        $profile->save();
        $count++;
    }
}
echo "Synced $count Alumni Profiles.\n";

// 3. Sync Users
echo "Syncing User Department Names...\n";
\DB::statement("UPDATE users 
               INNER JOIN alumni_profiles ON users.id = alumni_profiles.user_id 
               SET users.department_name = alumni_profiles.department_name 
               WHERE users.role = 'alumni' AND alumni_profiles.department_name IS NOT NULL");

echo "Integrity Fix Complete.\n";
