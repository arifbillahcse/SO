<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'How to Harden Your WordPress Site Against Hackers',
                'category' => 'WordPress',
                'excerpt' => 'A practical checklist covering login protection, file permissions, plugin security, and firewall setup for WordPress.',
                'read_minutes' => 8,
            ],
            [
                'title' => 'Core Web Vitals: The Complete 2025 Optimisation Guide',
                'category' => 'SEO',
                'excerpt' => 'LCP, FID, CLS — what they are, why they matter for rankings, and exactly how to fix them on a WordPress site.',
                'read_minutes' => 12,
            ],
            [
                'title' => 'How to Set Up a WordPress VPS on DigitalOcean (2025)',
                'category' => 'Cloud',
                'excerpt' => 'Step-by-step guide to provisioning, securing, and optimising a Ubuntu VPS for WordPress — from zero to production.',
                'read_minutes' => 10,
            ],
            [
                'title' => '5 Business Workflows You Should Automate Today',
                'category' => 'Automation',
                'excerpt' => 'The most impactful automations for small businesses — lead routing, email follow-up, invoice sending, and reporting.',
                'read_minutes' => 7,
            ],
            [
                'title' => 'WooCommerce Checkout Optimisation: Reduce Cart Abandonment',
                'category' => 'eCommerce',
                'excerpt' => 'Proven strategies to streamline your checkout, reduce friction, and recover lost sales using WooCommerce tools.',
                'read_minutes' => 9,
            ],
            [
                'title' => 'Local SEO for Small Businesses: A Practical Playbook',
                'category' => 'SEO',
                'excerpt' => 'Get your business found locally — Google Business Profile, local citations, review strategy, and location page optimisation.',
                'read_minutes' => 11,
            ],
        ];

        foreach ($articles as $i => $article) {
            $article['slug'] = Str::slug($article['title']);
            $article['published_at'] = now()->subDays(($i + 1) * 7);
            Article::updateOrCreate(['slug' => $article['slug']], $article);
        }
    }
}
