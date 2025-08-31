<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $areaTypes = [
            'North',
            'South',
            'East',
            'West',
            'Central',
            'Downtown',
            'Uptown',
            'Business District',
            'Residential',
            'Industrial',
            'Commercial',
            'Suburban',
            'Urban',
            'Rural',
            'Metropolitan',
            'District'
        ];

        return [
            'name' => fake()->randomElement($areaTypes) . ' ' . fake()->city(),
            'description' => fake()->sentence(10),
            'code' => strtoupper(fake()->lexify('???') . fake()->numerify('###')),
            'status' => fake()->boolean(85), // 85% chance of being active
            'created_by' => 1, // Default to admin user
            'updated_by' => 1, // Default to admin user
        ];
    }

    /**
     * Indicate that the area is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the area is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => false,
        ]);
    }
}
