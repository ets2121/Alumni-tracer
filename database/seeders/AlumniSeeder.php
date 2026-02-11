<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\Course;
use Faker\Factory as Faker;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        if ($courses->isEmpty()) {
            $this->command->warn('No courses found. Skipping Alumni seeding.');
            return;
        }

        $verifiedCount = config('demo.counts.verified_per_course', 10);
        $nonVerifiedCount = config('demo.counts.non_verified_per_course', 10);

        foreach ($courses as $course) {
            // Generate Verified Alumni
            User::factory($verifiedCount)
                ->create([
                    'role' => 'alumni',
                    'status' => 'active',
                    'department_name' => $course->department_name,
                    'created_at' => now()->subDays(rand(0, 365)), // Spread over last year
                ])
                ->each(function ($user) use ($course) {
                    $profile = AlumniProfile::factory()
                        ->verified()
                        ->create([
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'department_name' => $course->department_name,
                            'created_at' => $user->created_at, // Sync with user
                        ]);

                    // Seed Employment History
                    \App\Models\EmploymentHistory::factory(rand(1, 3))->create([
                        'user_id' => $user->id,
                    ]);
                });

            // Generate Non-Verified Alumni (Pending)
            User::factory($nonVerifiedCount)
                ->pending()
                ->create([
                    'role' => 'alumni',
                    'department_name' => $course->department_name,
                    'created_at' => now()->subDays(rand(0, 365)),
                ])
                ->each(function ($user) use ($course) {
                    AlumniProfile::factory()
                        ->nonVerified()
                        ->create([
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'department_name' => $course->department_name,
                            'created_at' => $user->created_at,
                        ]);
                });
        }
    }
}
