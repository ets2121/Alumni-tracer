<?php

use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;

// 1. Log in as Dept Admin
$deptAdmin = User::where('role', 'dept_admin')->first();
if (!$deptAdmin)
    die("No Dept Admin found.\n");
Auth::login($deptAdmin);

// 2. Create a test group
$group = ChatGroup::firstOrCreate([
    'name' => 'Dept Isolation Test',
    'type' => 'general',
    'department_name' => $deptAdmin->department_name
]);

// 3. Test Access
$controller = new ChatController();

try {
    $response = $controller->getMessages($group);

    if ($response->status() === 403) {
        echo "❌ Verified Error: 403 Forbidden\n";
        echo "Details: " . json_encode($response->getData()) . "\n";
    } else {
        echo "✅ Unexpected Success: Status " . $response->status() . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
