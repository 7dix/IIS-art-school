<?php

namespace Database\Seeders;

use App\Models\Atelier;
use App\Models\Equipment;
use App\Models\Type;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class AtelierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = Type::factory()->count(3)->create();
        $users = User::factory()->count(5)->create();
        $users->each(function ($user) {
            $user->assignRole('user');
        });

        Atelier::factory()
            ->count(3)
            ->sequence(fn($sequence) => ['name' => sprintf("Atelier %02d", $sequence->index)])
            ->create()
            ->each(function ($atelier) use ($types, $users) {
                // Assign a random manager to each atelier
                $manager = $users->random();
                $teacher = User::where('email', 'teacher@IIS.com')->first();

                // Ensure the manager is not one of the users that will be assigned
                $assignedUsers = $users->where('id', '!=', $manager->id)->random(2);

                // Assign the manager to the atelier
                $atelier->manager_id = $manager->id;
                $atelier->save();

                // Attach users and types
                $atelier->users()->attach($assignedUsers);
                $assignedTypes = $types->random(1);
                // Create equipment for this atelier with allowed types
                foreach ($assignedTypes as $type) {
                    $equipments = Equipment::factory()
                        ->count(fake()->numberBetween(0, 2))
                        ->create([
                            'type_id' => $type->id,
                            'owner_id' => $teacher->id,
                            'atelier_id' => $atelier->id,
                        ]);

                    // Assign the equipment to the atelier
                    $equipments->each(function ($equipment) use ($atelier) {
                        $equipment->atelier_id = $atelier->id;
                        $equipment->save();
                    });
                }

                // Create reservations for this atelier
                $atelier->equipments->each(function ($equipment) use ($assignedUsers) {
                    Reservation::factory()
                        ->count(1)
                        ->create([
                            'equipment_id' => $equipment->id,
                            'user_id' => $assignedUsers->random()->id,
                        ]);
                });
            });
    }
}