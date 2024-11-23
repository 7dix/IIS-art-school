<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Equipment;
use  App\Models\Reservation;
use Carbon\Carbon;
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

    private function hasCollision($start_date, $end_date, $equipment_id): bool
    {
        return Reservation::where('equipment_id', $equipment_id)
            ->whereIn('status', ['ongoing', 'approved'])
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date])
                    ->orWhere(function ($query) use ($start_date, $end_date) {
                        $query->where('start_date', '<=', $start_date)
                            ->where('end_date', '>=', $end_date);
                    });
            })
            ->exists();
    }
    private function isWithinLeasingPeriod($start_date, $end_date, $equipment): bool
    {
        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);
        $days = $end->diffInDays($start);
        
        return $days <= $equipment->maximum_leasing_period;
    }

    public function definition(): array
    {
        $maxAttempts = 100;
        $attempt = 0;

        do {
            $equipment = Equipment::inRandomOrder()->first();
            $start_date = $this->faker->dateTimeBetween('-2 months', 'now');
            $end_date = $this->faker->dateTimeBetween(
                $start_date, 
                Carbon::parse($start_date)->addDays($equipment->maximum_leasing_period)
            );
            $state = $this->faker->randomElement(['pending', 'approved', 'rejected', 'ongoing', 'completed', 'cancelled']);
            $valid_period = $this->isWithinLeasingPeriod($start_date, $end_date, $equipment);


            if ($state !== 'pending' && $state !== 'approved' && $state !== 'ongoin' && $valid_period) {
                break;
            }
            if ((!$this->hasCollision($start_date, $end_date, $equipment->id) && $valid_period) || ++$attempt === $maxAttempts) {
                break;
            }
        } while (true);

        return [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $state,
            'user_id' => User::inRandomOrder()->first()->id,
            'equipment_id' => $equipment->id,
        ];
    }
}
