<?php

namespace Database\Seeders;

use App\Models\Atelier;
use App\Models\Equipment;
use App\Models\Type;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtelierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = Type::factory()->count(12)->create();
        $users = User::factory()->count(50)->create();

        Atelier::factory()
            ->count(20)
            ->sequence(fn($sequence) => ['name' => sprintf("Atelier %02d", $sequence->index)])
            ->create()
            ->each(function ($atelier) use ($types, $users) {
                // Assign a random manager to each atelier
                $manager = $users->random();

                // Ensure the manager is not one of the users that will be assigned
                $assignedUsers = $users->where('id', '!=', $manager->id)->random(5);

                // Assign the manager to the atelier
                $atelier->manager_id = $manager->id;
                $atelier->save();

                // Attach users and types
                $atelier->users()->attach($assignedUsers);
                $assignedTypes = $types->random(3);
                $atelier->types()->attach($assignedTypes);

                // Create equipment for this atelier with allowed types
                foreach ($assignedTypes as $type) {
                    $equipments = Equipment::factory()
                        ->count(fake()->numberBetween(0, 3))
                        ->create([
                            'type_id' => $type->id,
                            'owner_id' => $manager->id,
                            'atelier_id' => $atelier->id,
                        ]);
                    // $atelier->equipments()->attach($equipments);
                }

                // Create reservations for this atelier
                Reservation::factory()
                    ->count(5)
                    ->create([
                        'equipment_id' => $atelier->equipments->random()->id, // Associate with a random equipment
                        'user_id' => $assignedUsers->random()->id, // Associate with a random assigned user
                    ]);
            });
    }
}
