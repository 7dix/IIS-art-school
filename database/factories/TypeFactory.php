<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Type>
 */
class TypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Drawing Tools',
                'Painting Supplies',
                'Sculpting Tools',
                'Craft Supplies',
                'Printmaking Tools',
                'Ceramics Tools',
                'Computers',
                'Graphic Tablets',
                'Digital Cameras',
                '3D Printers',
                'Scanners',
                'Musical Instruments',
                'Audio Equipment',
                'Woodworking Tools',
                'Metalworking Tools',
                'Textile Tools',
                'Jewelry Making Tools',
            ]),
        ];
    }
}
