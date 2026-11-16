<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => 'Business owner',
            'avatar_initial' => null,
            'stars' => 5,
            'text' => fake()->paragraph(),
            'featured' => false,
            'sort_order' => 0,
        ];
    }
}
