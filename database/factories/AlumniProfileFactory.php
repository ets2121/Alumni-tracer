<?php

namespace Database\Factories;

use App\Models\AlumniProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AlumniProfile>
 */
class AlumniProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use Filipino locale for more realistic local data
        $faker = \Faker\Factory::create('en_PH');

        return [
            'first_name' => $faker->firstName,
            'middle_name' => $faker->lastName,
            'last_name' => $faker->lastName,
            'gender' => $faker->randomElement(['male', 'female']),
            'dob' => $faker->date('Y-m-d', '2000-01-01'),
            'civil_status' => $faker->randomElement(['single', 'married', 'widowed', 'separated']),
            'contact_number' => $faker->phoneNumber,
            'address' => $faker->address,
            'batch_year' => $faker->numberBetween(2010, 2024),
            'employment_status' => $faker->randomElement(['Employed', 'Self-employed', 'Unemployed', 'Working Student']),
        ];
    }

    /**
     * State for verified alumni (complete profile + employment data)
     */
    public function verified(): static
    {
        $faker = \Faker\Factory::create('en_PH');
        return $this->state(fn(array $attributes) => [
            'employment_status' => 'Employed',
            'company_name' => $faker->company,
            'position' => $faker->jobTitle,
            'work_address' => $faker->address,
            'work_location' => $faker->randomElement(['Local', 'Overseas']),
            'field_of_work' => $faker->randomElement(['Information Technology', 'Human Resources', 'Education', 'Finance', 'Engineering', 'Marketing', 'Sales', 'Healthcare']),
            'work_status' => $faker->randomElement(['Permanent', 'Contractual', 'Job Order']),
            'establishment_type' => $faker->randomElement(['Public', 'Private']),
        ]);
    }

    /**
     * State for non-verified alumni
     */
    public function nonVerified(): static
    {
        return $this->state(fn(array $attributes) => [
            'proof_path' => 'proofs/demo_proof.jpg',
        ]);
    }
}
