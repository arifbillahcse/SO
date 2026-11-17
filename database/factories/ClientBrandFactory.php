<?php

namespace Database\Factories;

use App\Models\ClientBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClientBrand> */
class ClientBrandFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->company(), 'logo_path' => null, 'is_published' => true, 'sort_order' => 0];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes): array => ['is_published' => false]);
    }
}
