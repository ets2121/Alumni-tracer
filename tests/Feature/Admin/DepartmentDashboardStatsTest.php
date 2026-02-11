<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    protected $cceAdmin;
    protected $casAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup departments/courses
        $cceCourse = Course::factory()->create(['department_name' => 'CCE', 'code' => 'BSCS']);
        $casCourse = Course::factory()->create(['department_name' => 'CAS', 'code' => 'BSPsych']);

        // Setup Admins
        $this->cceAdmin = User::factory()->create([
            'role' => 'dept_admin',
            'department_name' => 'CCE'
        ]);

        $this->casAdmin = User::factory()->create([
            'role' => 'dept_admin',
            'department_name' => 'CAS'
        ]);

        // Setup Alumni
        User::factory()->count(3)->create(['role' => 'alumni', 'department_name' => 'CCE', 'status' => 'active']);
        User::factory()->count(2)->create(['role' => 'alumni', 'department_name' => 'CAS', 'status' => 'active']);
    }

    public function test_cce_admin_only_sees_cce_counts()
    {
        $response = $this->actingAs($this->cceAdmin)->getJson(route('admin.stats.counts'));

        $response->assertStatus(200)
            ->assertJson([
                'alumni_total' => 3,
                'alumni_verified' => 3,
                'total_departments' => 1,
            ]);
    }

    public function test_cas_admin_only_sees_cas_counts()
    {
        $response = $this->actingAs($this->casAdmin)->getJson(route('admin.stats.counts'));

        $response->assertStatus(200)
            ->assertJson([
                'alumni_total' => 2,
                'alumni_verified' => 2,
                'total_departments' => 1,
            ]);
    }

    public function test_system_admin_sees_all_counts()
    {
        $sysAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($sysAdmin)->getJson(route('admin.stats.counts'));

        $response->assertStatus(200)
            ->assertJson([
                'alumni_total' => 5,
                'total_departments' => 2,
            ]);
    }
}
