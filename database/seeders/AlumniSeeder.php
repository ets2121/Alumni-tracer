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
        $faker = Faker::create();
        $courses = Course::all();

        if ($courses->isEmpty())
            return;

        // Generate Active Alumni
        for ($i = 0; $i < 20; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => 'alumni',
                'status' => 'active',
            ]);

            AlumniProfile::create([
                'user_id' => $user->id,
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement(['male', 'female']),
                'dob' => $faker->date('Y-m-d', '2000-01-01'),
                'civil_status' => $faker->randomElement(['single', 'married', 'divorced']),
                'contact_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'course_id' => $courses->random()->id,
                'batch_year' => $faker->numberBetween(2010, 2023),
                'employment_status' => $faker->randomElement(['Employed', 'Self-employed', 'Unemployed']),
                'company_name' => $faker->company,
                'position' => $faker->jobTitle,
                'work_address' => $faker->address,
            ]);
        }

        // Generate Pending Applications
        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => 'alumni',
                'status' => 'pending',
            ]);

            AlumniProfile::create([
                'user_id' => $user->id,
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'course_id' => $courses->random()->id,
                'batch_year' => $faker->numberBetween(2020, 2024),
                'proof_path' => 'proofs/dummy.jpg'
            ]);
        }
    }
}
