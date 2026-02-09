<?php

namespace Database\Factories;

use App\Models\EmploymentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmploymentHistory>
 */
class EmploymentHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('en_PH');

        $startDate = $faker->date('Y-m-d', '-1 year');
        $isCurrent = $faker->boolean(70);

        return [
            'company_name' => $faker->company,
            'position' => $faker->jobTitle,
            'industry' => $faker->randomElement(['Technology', 'Education', 'Health', 'Finance', 'Manufacturing', 'Retail']),
            'location' => $faker->city . ', Philippines',
            'start_date' => $startDate,
            'end_date' => $isCurrent ? null : $faker->date('Y-m-d', 'now'),
            'is_current' => $isCurrent,
            'description' => $faker->sentence,
        ];
    }
}
