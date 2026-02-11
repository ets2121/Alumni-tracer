<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('??-###'),
            'name' => $this->faker->words(3, true),
            'department_name' => $this->faker->randomElement(['CITE', 'COE', 'CBA', 'CAS']),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['Undergraduate', 'Graduate', 'Certificate']),
        ];
    }
}
