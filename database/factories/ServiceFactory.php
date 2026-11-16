<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'route_name' => fake()->unique()->slug(),
            'icon' => 'code',
            'short_description' => fake()->sentence(),
            'features' => [fake()->sentence()],
            'featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
