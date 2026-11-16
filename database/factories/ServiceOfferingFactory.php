<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceOffering>
 */
class ServiceOfferingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'scope' => [fake()->sentence()],
            'action_label' => 'Discuss Your Project',
            'action_route' => 'home',
            'action_fragment' => 'contact',
            'is_published' => false,
            'featured' => false,
            'sort_order' => 0,
        ];
    }
}
