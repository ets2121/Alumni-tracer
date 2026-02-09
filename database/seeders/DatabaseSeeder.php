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
            CourseSeeder::class,
            AdminSeeder::class,
            AlumniSeeder::class,
            EvaluationDemoSeeder::class,
        ]);

        // Create a predictable test alumni user for quick dev access
        User::updateOrCreate(
            ['email' => 'alumni@example.com'],
            [
                'name' => 'Test Alumni',
                'role' => 'alumni',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );
    }
}
