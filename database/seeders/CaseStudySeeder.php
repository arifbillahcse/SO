<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $caseStudies = [
            [
                'title' => 'Ask Media: Scaling to Millions',
                'slug' => 'ask-media-scaling-to-millions',
                'client' => 'Ask Media Group',
                'tags' => ['Featured', 'WordPress', 'Enterprise'],
                'description' => 'Ask Media Group needed an enterprise-grade WordPress platform capable of handling millions of monthly page views — with zero downtime, blazing speed, and editorial flexibility for a large content team. We built and deployed a custom WordPress VIP-compliant platform with CDN integration, Redis caching, and a custom editorial workflow — delivering a site that loads in under 1.5 seconds at peak traffic.',
                'metrics' => [
                    ['value' => '1.2s', 'label' => 'Page Load Time'],
                    ['value' => '0', 'label' => 'Downtime Events'],
                    ['value' => '5M+', 'label' => 'Monthly Page Views'],
                ],
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Tropicana Online Store',
                'slug' => 'tropicana-online-store',
                'client' => 'Tropicana (PepsiCo)',
                'tags' => ['eCommerce', 'WooCommerce'],
                'description' => "Complete WooCommerce store rebuild with custom checkout, multi-currency support, and email automation for Tropicana's online sales.",
                'metrics' => [
                    ['value' => '+220%', 'label' => 'Conversion Rate'],
                    ['value' => '0.8s', 'label' => 'Checkout Speed'],
                    ['value' => 'Stripe', 'label' => '+ PayPal'],
                ],
                'featured' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'AAP Publisher Platform',
                'slug' => 'aap-publisher-platform',
                'client' => 'Association of American Publishers',
                'tags' => ['WordPress', 'Publishing'],
                'description' => 'Multi-author publishing platform with custom editorial workflows, member access control, and advanced content permissions.',
                'metrics' => [
                    ['value' => '50+', 'label' => 'Authors'],
                    ['value' => '99.9%', 'label' => 'Uptime'],
                    ['value' => '3x', 'label' => 'Publish Speed'],
                ],
                'featured' => false,
                'sort_order' => 3,
            ],
            [
                'title' => 'Storyful Cloud Migration',
                'slug' => 'storyful-cloud-migration',
                'client' => 'Storyful (NewsCorp)',
                'tags' => ['Cloud', 'Infrastructure'],
                'description' => 'Full server migration from shared hosting to a managed VPS with CDN, mail server, and automated backups for zero-downtime publishing.',
                'metrics' => [
                    ['value' => '99.97%', 'label' => 'Uptime'],
                    ['value' => '4x', 'label' => 'Faster Loading'],
                    ['value' => '$0', 'label' => 'Downtime Cost'],
                ],
                'featured' => false,
                'sort_order' => 4,
            ],
            [
                'title' => 'MarketHive Automation',
                'slug' => 'markethive-automation',
                'client' => 'MarketHive',
                'tags' => ['Automation', 'CRM'],
                'description' => 'End-to-end sales automation — lead capture, CRM pipeline, email sequences, and weekly reporting. Saved 15 hours per week.',
                'metrics' => [
                    ['value' => '15h', 'label' => 'Saved/Week'],
                    ['value' => '3x', 'label' => 'Lead Response'],
                    ['value' => '+40%', 'label' => 'Close Rate'],
                ],
                'featured' => false,
                'sort_order' => 5,
            ],
            [
                'title' => 'HKP Group SEO Campaign',
                'slug' => 'hkp-group-seo-campaign',
                'client' => 'HKP Group',
                'tags' => ['SEO', 'Content'],
                'description' => 'Full technical SEO audit, on-page optimisation, and 6-month content campaign that took 18 keywords from page 4 to page 1.',
                'metrics' => [
                    ['value' => '+340%', 'label' => 'Organic Traffic'],
                    ['value' => '18', 'label' => 'Page 1 Keywords'],
                    ['value' => '4 mo', 'label' => 'Time to Results'],
                ],
                'featured' => false,
                'sort_order' => 6,
            ],
            [
                'title' => 'DC Innovation Marketplace',
                'slug' => 'dc-innovation-marketplace',
                'client' => 'DC Innovation',
                'tags' => ['eCommerce', 'B2B'],
                'description' => 'Multi-vendor B2B marketplace with custom vendor dashboards, bulk ordering, and invoice automation for a UK-based distribution company.',
                'metrics' => [
                    ['value' => '120+', 'label' => 'Vendors'],
                    ['value' => '+180%', 'label' => 'GMV Growth'],
                    ['value' => 'Auto', 'label' => 'Invoicing'],
                ],
                'featured' => false,
                'sort_order' => 7,
            ],
        ];

        foreach ($caseStudies as $caseStudy) {
            CaseStudy::updateOrCreate(['slug' => $caseStudy['slug']], $caseStudy);
        }
    }
}
