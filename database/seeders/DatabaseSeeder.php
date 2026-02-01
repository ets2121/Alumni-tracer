<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CourseSeeder::class,
            AlumniSeeder::class,
            EvaluationSeeder::class,
        ]);

        // Create a test alumni user
        User::factory()->create([
            'name' => 'Test Alumni',
            'email' => 'alumni@example.com',
            'role' => 'alumni',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);
    }
}
