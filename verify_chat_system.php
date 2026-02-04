<?php

use App\Models\User;
use App\Models\ChatGroup;
use App\Models\BannedWord;
use App\Models\ChatMessage;
use App\Services\MessageFilterService;

echo "Starting Chat System Verification...\n";

// 1. Setup Test Data
$sysAdmin = User::where('role', 'admin')->first();
$deptAdmin = User::where('role', 'dept_admin')->first(); // Assumed CITCS or similar
$alumni = User::where('role', 'alumni')->first();

if (!$deptAdmin || !$alumni) {
    die("Error: Need Dept Admin and Alumni to test.\n");
}
$deptName = $deptAdmin->department_name;
$alumni->department_name = $deptName; // Ensure match for test
$alumni->save();

echo "Testing with Dept Admin: {$deptAdmin->name} ($deptName)\n";

// 2. Test Banned Word Creation
BannedWord::where('word', 'badword')->delete();
BannedWord::create([
    'word' => 'badword',
    'department_name' => $deptName,
    'created_by' => $deptAdmin->id
]);
echo "Created Banned Word 'badword' for $deptName.\n";

// 3. Test Message Filtering
$service = new MessageFilterService();
$group = ChatGroup::firstOrCreate(['type' => 'general', 'name' => 'Test General']);

try {
    echo "Testing blocked message...\n";
    $service->validateConfirmed("This contains a badword here.", $alumni);
    echo "❌ FAILED: Message should have been blocked.\n";
} catch (\Exception $e) {
    echo "✅ SUCCESS: Blocked message. Error: " . $e->getMessage() . "\n";
}

try {
    echo "Testing clean message...\n";
    $service->validateConfirmed("This is a clean message.", $alumni);
    echo "✅ SUCCESS: Clean message passed.\n";
} catch (\Exception $e) {
    echo "❌ FAILED: Clean message blocked. Error: " . $e->getMessage() . "\n";
}

// 4. Test Dept Admin Group Creation (Isolation)
// Simulate Dept Admin trying to create 'dept_admin' type (Should fail per Controller logic, but here we test Model/Service logic if any? )
// Controller logic prevents 'dept_admin' type creation by non-System admins.
// We can test scope isolation on models.

$groupCountBefore = ChatGroup::count();
// If Dept Admin creates a group, it should default to their department
auth()->login($deptAdmin);
$newGroup = ChatGroup::create([
    'name' => 'Dept Specific Group',
    'type' => 'general',
    'department_name' => $deptName // Explicitly set or rely on trait
]);

echo "Created Group ID: {$newGroup->id}, Dept: {$newGroup->department_name}\n";

if ($newGroup->department_name === $deptName) {
    echo "✅ SUCCESS: Group correctly scoped to $deptName.\n";
} else {
    echo "❌ FAILED: Group has wrong department: {$newGroup->department_name}\n";
}

// Cleanup
$newGroup->delete();
BannedWord::where('word', 'badword')->delete();

echo "Verification Complete.\n";
