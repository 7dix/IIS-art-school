<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_date' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+7 days'),
            'status' => $this->faker->randomElement(['pending','approved','rejected','ongoing', 'completed', 'cancelled']),
        ];
    }
}
