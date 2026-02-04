<?php
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\AlumniProfile;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

function test($name, $callback)
{
    try {
        $callback();
        echo "✅ PASS: $name\n";
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        if ($e->getStatusCode() === 403) {
            echo "✅ PASS (Blocked): $name - " . $e->getMessage() . "\n";
        } else {
            echo "❌ FAIL: $name - Status " . $e->getStatusCode() . " - " . $e->getMessage() . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ FAIL: $name - " . $e->getMessage() . "\n";
    }
}

// 1. Setup Test Data
$sysAdmin = User::find(1);
$citcsAdmin = User::find(28);
$cteAdmin = User::find(32);
$citcsAlumni = User::find(4);
$cteAlumni = User::find(6);

if (!$sysAdmin || !$citcsAdmin || !$cteAdmin || !$citcsAlumni || !$cteAlumni) {
    echo "⚠️ Missing test users (IDs 1, 28, 32, 4, 6). Please ensure they exist.\n";
    exit;
}

// Cleanup previous test groups
ChatGroup::where('name', 'LIKE', 'TEST_%')->delete();

// Create Test Groups
$adminRoom = ChatGroup::create(['name' => 'TEST_AdminRoom', 'type' => 'admin_dept']);
$citcsGeneral = ChatGroup::create(['name' => 'TEST_CITCS_Gen', 'type' => 'general', 'department_name' => 'CITCS']);
$coeGeneral = ChatGroup::create(['name' => 'TEST_CTE_Gen', 'type' => 'general', 'department_name' => 'CTE']);
$globalBatch2020 = ChatGroup::create(['name' => 'TEST_Global_Batch_2020', 'type' => 'batch', 'batch_year' => 2020]);

$controller = new ChatController();

// --- TEST SCENARIOS ---

// Scenario A: System Admin Access
test("System Admin can see everything", function () use ($sysAdmin, $controller) {
    Auth::login($sysAdmin);
    $groups = $controller->getGroups()->getOriginalContent();
    if (count($groups) < 4)
        throw new \Exception("SysAdmin see too few groups");
});

// Scenario B: Dept Admin Scoping
test("CITCS Admin can see Admin Room and CITCS Room, but NOT CTE Room", function () use ($citcsAdmin, $controller, $adminRoom, $citcsGeneral, $coeGeneral) {
    Auth::login($citcsAdmin);
    $groups = collect($controller->getGroups()->getOriginalContent());
    if (!$groups->contains('id', $adminRoom->id))
        throw new \Exception("Missing Admin Room");
    if (!$groups->contains('id', $citcsGeneral->id))
        throw new \Exception("Missing CITCS Room");
    if ($groups->contains('id', $coeGeneral->id))
        throw new \Exception("Leaking CTE Room");
});

// Scenario C: Alumni Isolation
test("CITCS Alumni cannot see Admin Room", function () use ($citcsAlumni, $controller, $adminRoom) {
    Auth::login($citcsAlumni);
    $groups = collect($controller->getGroups()->getOriginalContent());
    if ($groups->contains('id', $adminRoom->id))
        throw new \Exception("Leaking Admin Room to Alumni");
});

test("CITCS Alumni cannot see CTE General", function () use ($citcsAlumni, $controller, $coeGeneral) {
    Auth::login($citcsAlumni);
    $groups = collect($controller->getGroups()->getOriginalContent());
    if ($groups->contains('id', $coeGeneral->id))
        throw new \Exception("Leaking CTE Room to CITCS Alumni");
});

test("Alumni can see Global Batch Room if match", function () use ($citcsAlumni, $controller, $globalBatch2020) {
    // Ensure alumni is batch 2020
    $citcsAlumni->alumniProfile->update(['batch_year' => 2020]);
    Auth::login($citcsAlumni);
    $groups = collect($controller->getGroups()->getOriginalContent());
    if (!$groups->contains('id', $globalBatch2020->id))
        throw new \Exception("Missing Global Batch Room");
});

// Scenario D: Access Control (getMessages)
test("CITCS Alumni blocked from Admin Room message access", function () use ($citcsAlumni, $controller, $adminRoom) {
    Auth::login($citcsAlumni);
    $resp = $controller->getMessages($adminRoom);
    if ($resp->status() !== 403)
        throw new \Exception("Should return 403 for Admin Room");
});

test("CITCS Alumni blocked from CTE Room message access", function () use ($citcsAlumni, $controller, $coeGeneral) {
    Auth::login($citcsAlumni);
    $resp = $controller->getMessages($coeGeneral);
    if ($resp->status() !== 403)
        throw new \Exception("Should return 403 for CTE Room");
});

echo "\n--- ALL RBAC TESTS COMPLETE ---\n";
