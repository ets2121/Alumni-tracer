<?php

use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Support\Facades\Auth;

// 1. Log in as Dept Admin
$deptAdmin = User::where('role', 'dept_admin')->first();
if (!$deptAdmin)
    die("No Dept Admin found.\n");
Auth::login($deptAdmin);

echo "Logged in as {$deptAdmin->name} ({$deptAdmin->department_name})\n";

try {
    // 2. Run the exact query causing exception
    $groups = ChatGroup::withCount(['messages', 'users'])->get();
    echo "✅ Success! Query matched " . $groups->count() . " groups.\n";
} catch (\Exception $e) {
    echo "❌ Failed! " . $e->getMessage() . "\n";
}
