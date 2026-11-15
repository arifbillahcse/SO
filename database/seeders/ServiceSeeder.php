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
                'short_description' => 'Custom WordPress and business websites built for performance, scalability, and conversions.',
                'features' => ['WordPress Development', 'Custom Business Sites', 'WooCommerce / eCommerce', 'Landing Pages'],
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'SEO Optimization',
                'slug' => 'seo',
                'route_name' => 'seo',
                'icon' => 'search',
                'short_description' => 'Technical SEO, on-page optimization, and content strategies that drive organic traffic and rankings.',
                'features' => ['Technical SEO Audit', 'On-page Optimization', 'Speed & Core Web Vitals', 'Keyword Strategy'],
                'featured' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Manage Cloud',
                'slug' => 'manage-cloud',
                'route_name' => 'manage-cloud',
                'icon' => 'cloud',
                'short_description' => 'VPS and cloud hosting solutions configured for speed, security, and 99.9% uptime.',
                'features' => ['VPS / Cloud Servers', 'Domain & DNS Setup', 'SSL & Security', 'Backup & Monitoring'],
                'featured' => false,
                'sort_order' => 3,
            ],
            [
                'title' => 'Automation',
                'slug' => 'automation',
                'route_name' => 'automation',
                'icon' => 'gear',
                'short_description' => 'Streamline repetitive tasks and workflows with smart automation tools and integrations.',
                'features' => ['Workflow Automation', 'API Integrations', 'Email Automation', 'CRM Automation'],
                'featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
