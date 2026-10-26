<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Website Development',
                'slug' => 'web-development',
                'route_name' => 'web-development',
                'icon' => 'code',
                'short_description' => 'We craft visually stunning, lightning-fast, and conversion-optimised websites — from custom WordPress builds to full-scale eCommerce platforms. Trusted by enterprises globally for over 10 years.',
                'sort_order' => 1,
            ],
            [
                'title' => 'SEO Optimization',
                'slug' => 'seo',
                'route_name' => 'seo',
                'icon' => 'search',
                'short_description' => 'We combine technical precision with content strategy to put your website in front of the right audience — consistently and sustainably.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Manage Cloud',
                'slug' => 'manage-cloud',
                'route_name' => 'manage-cloud',
                'icon' => 'cloud',
                'short_description' => 'VPS and cloud hosting configured, secured, and managed by experts — so your site stays fast, safe, and always online. No shared hosting limitations.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Automation',
                'slug' => 'automation',
                'route_name' => 'automation',
                'icon' => 'gear',
                'short_description' => 'Stop doing the same repetitive tasks manually. We build custom automation systems that handle your workflows, emails, and data — so you can focus on what matters.',
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
