<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class PortfolioItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Enterprise Media Platform', 'client' => 'Ask Media Group — North America', 'category' => 'wordpress', 'tags' => ['WordPress', 'Enterprise']],
            ['title' => 'Online Store — WooCommerce', 'client' => 'Tropicana (PepsiCo) — USA', 'category' => 'ecommerce', 'tags' => ['WooCommerce', 'eCommerce']],
            ['title' => 'Cloud Infrastructure Setup', 'client' => 'Storyful (NewsCorp) — Ireland', 'category' => 'cloud', 'tags' => ['Cloud', 'Infrastructure']],
            ['title' => 'Publisher Platform', 'client' => 'Association of American Publishers — USA', 'category' => 'wordpress', 'tags' => ['WordPress', 'Publishing']],
            ['title' => 'Multi-Vendor Marketplace', 'client' => 'DC Innovation — UK', 'category' => 'ecommerce', 'tags' => ['WooCommerce', 'B2B']],
            ['title' => 'Business Automation System', 'client' => 'MarketHive — USA', 'category' => 'automation', 'tags' => ['Automation', 'CRM']],
            ['title' => 'SEO Growth Campaign', 'client' => 'HKP Group — Asia', 'category' => 'seo', 'tags' => ['SEO', 'Content']],
            ['title' => 'Dedicated Server Migration', 'client' => 'STEM Academy — Europe', 'category' => 'cloud', 'tags' => ['Cloud', 'VPS']],
            ['title' => 'News & Media Portal', 'client' => 'Scan Media — Bangladesh', 'category' => 'wordpress', 'tags' => ['WordPress', 'News']],
        ];

        foreach ($items as $i => $item) {
            PortfolioItem::updateOrCreate(
                ['title' => $item['title'], 'client' => $item['client']],
                $item + ['sort_order' => $i + 1]
            );
        }
    }
}
