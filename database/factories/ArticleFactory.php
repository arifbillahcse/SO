<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5), 'slug' => fake()->unique()->slug(),
            'article_category_id' => ArticleCategory::factory(),
            'excerpt' => fake()->paragraph(), 'body' => '<p>'.fake()->paragraphs(5, true).'</p>',
            'read_minutes' => 5, 'featured' => false, 'published_at' => now()->subDay(),
        ];
    }
}
