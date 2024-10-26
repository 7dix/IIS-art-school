<?php

namespace Database\Seeders;

use App\Models\Atelier;
use App\Models\Equipment;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtelierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = Type::factory()->count(5)->create();
        $users = User::factory()->count(50)->create();

        Atelier::factory()
            ->count(10)
            ->sequence(fn($sequence) => ['name' => "Atelier {$sequence->index}"])
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
                    Equipment::factory()
                        ->count(fake()->numberBetween(0, 3))
                        ->create([
                            'type_id' => $type->id,
                            'atelier_id' => $atelier->id,
                            'owner_id' => $manager->id,
                        ]);
                }
            });
    }
}
