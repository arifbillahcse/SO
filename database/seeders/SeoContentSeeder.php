<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use App\Support\Seo;
use Illuminate\Database\Seeder;

/**
 * Hand-written SEO for every public page, based on each page's actual
 * content. Only fills fields that are still empty, so anything already
 * edited in the admin dashboard is never overwritten. Safe to re-run.
 */
class SeoContentSeeder extends Seeder
{
    private const STATIC_PAGES = [
        'home' => [
            'title' => 'Softorio: Web Development Agency for WordPress, Cloud & SEO',
            'meta_description' => 'Softorio is a web development agency building fast WordPress websites, managing cloud hosting, and running SEO and automation for growing businesses.',
            'focus_keyword' => 'web development agency',
            'secondary_keywords' => ['WordPress development', 'managed cloud hosting', 'SEO services', 'business automation', 'web development company Bangladesh'],
            'content_notes' => 'Brand + navigational intent. Hub page: should link to all four services and three solutions.',
        ],
        'web-development' => [
            'title' => 'Website Development Services | Custom WordPress Sites',
            'meta_description' => 'Website development services on WordPress and WooCommerce: custom business sites, online stores, redesigns and plugins built for speed and conversions.',
            'focus_keyword' => 'website development services',
            'secondary_keywords' => ['custom WordPress development', 'WooCommerce development', 'website redesign', 'WordPress plugin development', 'web development company in Bangladesh'],
            'content_notes' => 'Commercial intent. Pricing plans and FAQ are on the page; keep FAQ answers current.',
        ],
        'seo' => [
            'title' => 'SEO Services | Technical, On-Page & Local SEO | Softorio',
            'meta_description' => 'Professional SEO services: technical SEO audits, on-page optimisation, link building, local and eCommerce SEO, with monthly reporting. Get a free SEO report.',
            'focus_keyword' => 'SEO services',
            'secondary_keywords' => ['technical SEO audit', 'on-page SEO', 'local SEO', 'eCommerce SEO', 'SEO agency Bangladesh'],
            'content_notes' => 'Commercial intent. Strong trust signal: link to SEO case studies. Free SEO report form is the main CTA.',
        ],
        'manage-cloud' => [
            'title' => 'Managed WordPress Hosting on Cloud VPS | From $29/mo',
            'meta_description' => 'Managed WordPress hosting on cloud VPS: server setup, security hardening, daily offsite backups, CDN and performance tuning. From $29/mo with free migration.',
            'focus_keyword' => 'managed WordPress hosting',
            'secondary_keywords' => ['managed VPS hosting', 'cloud hosting', 'WordPress hosting', 'free website migration', 'WordPress hosting Bangladesh'],
            'content_notes' => 'Transactional intent. Prices shown on page ($29/$69/$149) must match this title if they change.',
        ],
        'automation' => [
            'title' => 'Business Automation Services | Workflow, CRM & Email',
            'meta_description' => 'Business automation services that remove repetitive work: custom workflow, email, CRM and reporting automation that connects your tools and APIs.',
            'focus_keyword' => 'business automation services',
            'secondary_keywords' => ['workflow automation', 'CRM automation', 'email automation', 'API integration', 'Zapier and Make automation'],
            'content_notes' => 'Commercial intent. Links to the Automation Solution page for end-to-end systems.',
        ],
        'ecommerce-solution' => [
            'title' => 'eCommerce Solution | WooCommerce Store, Hosting & SEO',
            'meta_description' => 'A complete eCommerce solution: WooCommerce store build, payment gateways, cloud hosting, eCommerce SEO and email marketing automation in one package.',
            'focus_keyword' => 'eCommerce solution',
            'secondary_keywords' => ['WooCommerce store development', 'online store setup', 'payment gateway integration', 'eCommerce SEO', 'eCommerce website Bangladesh'],
            'content_notes' => 'Solution/bundle intent. Distinct from Web Development (single service): emphasise \'complete\' and \'one package\'.',
        ],
        'cloud-solution' => [
            'title' => 'Cloud Infrastructure Setup | VPS, Security & Monitoring',
            'meta_description' => 'Complete cloud infrastructure setup: VPS provisioning, firewall and server hardening, DNS, mail server, cron jobs and uptime monitoring, managed by experts.',
            'focus_keyword' => 'cloud infrastructure setup',
            'secondary_keywords' => ['VPS server setup', 'server hardening', 'mail server setup', 'DNS management', 'uptime monitoring'],
            'content_notes' => 'Project intent (one-off setup). Distinct from Managed Cloud (ongoing hosting) — cross-linked.',
        ],
        'automation-solution' => [
            'title' => 'Business Automation System | Lead to CRM to Reporting',
            'meta_description' => 'An end-to-end business automation system: lead capture, CRM and lead routing, email nurturing, order fulfilment and reporting, built to run on its own.',
            'focus_keyword' => 'business automation system',
            'secondary_keywords' => ['lead capture automation', 'CRM lead routing', 'email nurturing', 'order fulfilment automation', 'automated reporting'],
            'content_notes' => 'Solution intent. Distinct from Automation Services (single workflows).',
        ],
        'our-works' => [
            'title' => 'Our Work: Web Development Portfolio | Softorio',
            'meta_description' => 'Our web development portfolio: WordPress, WooCommerce, cloud infrastructure, automation and SEO projects delivered by Softorio for clients on three continents.',
            'focus_keyword' => 'web development portfolio',
            'secondary_keywords' => ['WordPress portfolio', 'WooCommerce projects', 'cloud projects', 'client projects'],
            'content_notes' => 'Trust/navigational. Project detail pages stay noindex until each has a written description.',
        ],
        'case-study' => [
            'title' => 'Client Case Studies | Web, Cloud & SEO Results | Softorio',
            'meta_description' => 'Real client case studies from Softorio: WordPress platforms, WooCommerce stores, cloud migrations, SEO campaigns and automation, with measurable results.',
            'focus_keyword' => 'client case studies',
            'secondary_keywords' => ['web development case study', 'SEO case study', 'cloud migration case study', 'WooCommerce case study'],
            'content_notes' => 'Trust intent. Case study pages become indexable once each has a full write-up (120+ words).',
        ],
        'our-resources' => [
            'title' => 'Free WordPress Guides, SEO & Cloud Tips | Softorio',
            'meta_description' => 'Free WordPress guides from Softorio: speed and security, Core Web Vitals, local SEO, VPS setup, WooCommerce checkout tips and business automation.',
            'focus_keyword' => 'WordPress guides',
            'secondary_keywords' => ['SEO guides', 'Core Web Vitals guide', 'WordPress security', 'VPS setup guide', 'WooCommerce tips'],
            'content_notes' => 'Informational hub. Articles are noindex until their full body is written; publish full guides to earn search traffic.',
        ],
        'about-us' => [
            'title' => 'About Softorio | Our Story, Team, Values & How We Work',
            'meta_description' => 'Meet Softorio: a web, cloud and SEO team built on reliability, technical excellence and the WordPress community. Learn our story, values and how we work.',
            'focus_keyword' => 'Softorio',
            'secondary_keywords' => ['Softorio team', 'WordPress agency', 'WordPress community contributor'],
            'content_notes' => 'Brand intent. Note: this page says \'over 10 years\' while Our Founders says founded in 2021 — reconcile for trust.',
        ],
        'affiliate-program' => [
            'title' => 'Affiliate Program | Earn 20% Lifetime Profit | Softorio',
            'meta_description' => 'Join the Softorio affiliate program: refer businesses that need a website, hosting or cloud services and earn 20% of the profit from every client, for life.',
            'focus_keyword' => 'affiliate program',
            'secondary_keywords' => ['referral program', 'web agency affiliate program', 'hosting affiliate program', 'earn referral commission'],
            'content_notes' => 'Transactional intent for partners. Commission terms in the title must match the page.',
        ],
        'our-founders' => [
            'title' => 'Meet the Softorio Co-Founders: Arif Billah & Asma Akter',
            'meta_description' => 'Meet the Softorio co-founders, Md. Arif Billah and Asma Akter: CSE engineers leading Softorio\'s web development, cloud and automation work worldwide.',
            'focus_keyword' => 'Softorio co-founders',
            'secondary_keywords' => ['Md. Arif Billah', 'Asma Akter'],
            'content_notes' => 'Intentionally noindex (hidden page). Metadata kept accurate in case it is ever made public.',
        ],
    ];

    private const ARTICLES = [
        'the-complete-wordpress-speed-optimisation-guide-2025' => [
            'title' => 'WordPress Speed Optimisation Guide: Score 90+ on PageSpeed',
            'meta_description' => 'A step-by-step WordPress speed optimisation guide: server caching, image optimisation, database cleanup and Core Web Vitals fixes to score 90+ on PageSpeed.',
            'focus_keyword' => 'WordPress speed optimisation',
            'secondary_keywords' => ['PageSpeed Insights', 'WordPress caching', 'image optimisation', 'Core Web Vitals'],
        ],
        'how-to-harden-your-wordpress-site-against-hackers' => [
            'title' => 'WordPress Security Hardening: A Practical Checklist',
            'meta_description' => 'A practical WordPress security hardening checklist: login protection, file permissions, plugin security and firewall setup to keep hackers out of your site.',
            'focus_keyword' => 'WordPress security hardening',
            'secondary_keywords' => ['WordPress security checklist', 'WordPress firewall', 'file permissions', 'login protection'],
        ],
        'core-web-vitals-the-complete-2025-optimisation-guide' => [
            'title' => 'Core Web Vitals Guide: Fix LCP, INP & CLS on WordPress',
            'meta_description' => 'What LCP, INP and CLS measure, why Core Web Vitals matter for rankings, and exactly how to fix each one on a WordPress site, step by step.',
            'focus_keyword' => 'Core Web Vitals',
            'secondary_keywords' => ['LCP optimisation', 'CLS fix', 'INP', 'WordPress performance'],
        ],
        'how-to-set-up-a-wordpress-vps-on-digitalocean-2025' => [
            'title' => 'How to Set Up a WordPress VPS on DigitalOcean',
            'meta_description' => 'Step by step: provision, secure and optimise an Ubuntu WordPress VPS on DigitalOcean, from a fresh droplet to a fast, production-ready server.',
            'focus_keyword' => 'WordPress VPS on DigitalOcean',
            'secondary_keywords' => ['Ubuntu VPS setup', 'DigitalOcean droplet', 'WordPress server setup', 'VPS security'],
        ],
        '5-business-workflows-you-should-automate-today' => [
            'title' => '5 Business Workflows You Should Automate Today',
            'meta_description' => 'The five business workflows small companies should automate first: lead routing, email follow-up, invoice sending and reporting, with practical tips.',
            'focus_keyword' => 'business workflows',
            'secondary_keywords' => ['small business automation', 'lead routing', 'invoice automation', 'email follow-up'],
        ],
        'woocommerce-checkout-optimisation-reduce-cart-abandonment' => [
            'title' => 'WooCommerce Checkout Optimisation: Cut Cart Abandonment',
            'meta_description' => 'Proven WooCommerce checkout optimisation strategies to streamline checkout, reduce friction, cut cart abandonment and recover lost sales with the right tools.',
            'focus_keyword' => 'WooCommerce checkout optimisation',
            'secondary_keywords' => ['cart abandonment', 'WooCommerce conversion', 'checkout UX', 'abandoned cart recovery'],
        ],
        'local-seo-for-small-businesses-a-practical-playbook' => [
            'title' => 'Local SEO for Small Businesses: A Practical Playbook',
            'meta_description' => 'A practical local SEO playbook for small businesses: Google Business Profile, local citations, a review strategy and location page optimisation.',
            'focus_keyword' => 'local SEO',
            'secondary_keywords' => ['Google Business Profile', 'local citations', 'review strategy', 'location pages'],
        ],
    ];

    private const CASE_STUDIES = [
        'ask-media-scaling-to-millions' => [
            'title' => 'Ask Media Case Study: Enterprise WordPress at Scale',
            'meta_description' => 'How Softorio built an enterprise WordPress platform for Ask Media Group that handles millions of monthly page views with zero downtime and fast load times.',
            'focus_keyword' => 'enterprise WordPress',
            'secondary_keywords' => ['high-traffic WordPress', 'WordPress VIP', 'Ask Media Group'],
        ],
        'tropicana-online-store' => [
            'title' => 'Tropicana Case Study: WooCommerce Store Rebuild',
            'meta_description' => 'How Softorio delivered a WooCommerce store rebuild for Tropicana: a custom checkout, multi-currency support and email automation for online sales.',
            'focus_keyword' => 'WooCommerce store rebuild',
            'secondary_keywords' => ['custom WooCommerce checkout', 'multi-currency store', 'eCommerce case study'],
        ],
        'aap-publisher-platform' => [
            'title' => 'AAP Case Study: Multi-Author Publishing Platform',
            'meta_description' => 'How Softorio built a multi-author publishing platform for the Association of American Publishers with editorial workflows and member access control.',
            'focus_keyword' => 'publishing platform',
            'secondary_keywords' => ['multi-author WordPress', 'editorial workflow', 'member access control'],
        ],
        'storyful-cloud-migration' => [
            'title' => 'Storyful Cloud Migration Case Study: Shared Hosting to VPS',
            'meta_description' => 'How Softorio handled Storyful\'s cloud migration from shared hosting to a managed VPS with a CDN, mail server and automated backups for zero-downtime publishing.',
            'focus_keyword' => 'cloud migration',
            'secondary_keywords' => ['shared hosting to VPS', 'server migration', 'managed VPS'],
        ],
        'markethive-automation' => [
            'title' => 'MarketHive Case Study: Sales Automation Saving 15 Hours',
            'meta_description' => 'How Softorio built end-to-end sales automation for MarketHive: lead capture, CRM pipeline, email sequences and weekly reporting, saving 15 hours a week.',
            'focus_keyword' => 'sales automation',
            'secondary_keywords' => ['CRM pipeline automation', 'email sequences', 'lead capture'],
        ],
        'hkp-group-seo-campaign' => [
            'title' => 'HKP Group SEO Campaign: 18 Keywords to Page One',
            'meta_description' => 'How an SEO campaign of technical audit, on-page optimisation and six months of content took 18 of HKP Group\'s keywords from page four to page one.',
            'focus_keyword' => 'SEO campaign',
            'secondary_keywords' => ['technical SEO audit', 'content campaign', 'keyword rankings'],
        ],
        'dc-innovation-marketplace' => [
            'title' => 'DC Innovation Case Study: B2B Multi-Vendor Marketplace',
            'meta_description' => 'How Softorio built a B2B multi-vendor marketplace for DC Innovation with custom vendor dashboards, bulk ordering and invoice automation for UK distribution.',
            'focus_keyword' => 'multi-vendor marketplace',
            'secondary_keywords' => ['B2B marketplace', 'vendor dashboard', 'invoice automation'],
        ],
    ];

    public function run(): void
    {
        $this->call(PageSeoSeeder::class);

        foreach (self::STATIC_PAGES as $routeName => $fields) {
            $this->fillEmpty(PageSeo::forRoute($routeName), $fields);
        }

        foreach (Article::with('seo')->get() as $article) {
            $this->fillEmpty($article->ensureSeo(), self::ARTICLES[$article->slug] ?? $this->draftFor($article));
        }

        foreach (CaseStudy::with('seo')->get() as $caseStudy) {
            $this->fillEmpty($caseStudy->ensureSeo(), self::CASE_STUDIES[$caseStudy->slug] ?? $this->draftFor($caseStudy));
        }

        // Portfolio items have no written description yet, so only a draft
        // title/description is set and no focus keyword is guessed.
        foreach (PortfolioItem::with('seo')->get() as $item) {
            $this->fillEmpty($item->ensureSeo(), $this->draftFor($item));
        }
    }

    /**
     * Title and description drafted from the record's own content.
     */
    private function draftFor($record): array
    {
        return [
            'title' => mb_strlen($record->seoDefaultTitle()) <= 60 ? $record->seoDefaultTitle() : $record->title,
            'meta_description' => Seo::excerpt($record->seoDefaultDescription(), 158),
        ];
    }

    private function fillEmpty(PageSeo $seo, array $fields): void
    {
        foreach ($fields as $field => $value) {
            if (blank($seo->{$field}) && filled($value)) {
                $seo->{$field} = $value;
            }
        }

        $seo->save();
    }
}
