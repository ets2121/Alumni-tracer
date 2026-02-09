<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\Course;
use App\Models\NewsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_counts()
    {
        // Create some data
        User::factory()->count(5)->create(['role' => 'alumni', 'status' => 'active']);
        User::factory()->count(3)->create(['role' => 'alumni', 'status' => 'pending']);

        $response = $this->actingAs($this->admin)->getJson(route('admin.stats.counts'));

        $response->assertStatus(200)
            ->assertJson([
                'alumni_total' => 8,
                'alumni_verified' => 5,
                'alumni_pending' => 3,
            ]);
    }

    public function test_admin_can_access_charts()
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.stats.charts'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'registration_trends',
                'employment_status',
                'alumni_by_dept',
                'gender_distribution',
            ]);
    }

    public function test_alumni_cannot_access_admin_stats()
    {
        $alumni = User::factory()->create(['role' => 'alumni']);

        $this->actingAs($alumni)->getJson(route('admin.stats.counts'))
            ->assertStatus(403);
    }
}
