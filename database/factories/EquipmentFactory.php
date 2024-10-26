<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'year_of_manufacture' => $this->faker->year(),
            'date_of_purchase' => $this->faker->dateTimeBetween('-7 year', '-1 month'),
            'maximum_leasing_period' => $this->faker->numberBetween(1, 14),
            'allowed_leasing_hours' => json_encode($this->faker->randomElements(
                range(0, 23), 
                $this->faker->numberBetween(1, 24))),
        ];
    }
}
