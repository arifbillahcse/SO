<?php

return [

    // Canonical origin for canonical tags, og:url, JSON-LD and the sitemap,
    // independent of whichever host/scheme a request arrived on.
    'site_url' => rtrim(env('SEO_SITE_URL', 'https://softorio.com'), '/'),

    'site_name' => 'Softorio',

    'default_og_image' => 'images/og-default.png',

    // Dynamic detail pages with fewer words of unique body text than this are
    // served as noindex and left out of the sitemap (thin-content guard).
    'min_indexable_words' => 120,

    'page_types' => [
        'page' => 'Page',
        'service' => 'Service',
        'solution' => 'Solution',
        'listing' => 'Listing',
        'article' => 'Article',
        'case_study' => 'Case Study',
        'portfolio' => 'Portfolio',
    ],

    // Every public static route. A page_seos row is kept for each one.
    'pages' => [
        'home' => ['label' => 'Home', 'type' => 'page', 'view' => 'pages.home', 'schema' => 'WebPage'],
        'web-development' => ['label' => 'Web Development', 'type' => 'service', 'view' => 'pages.services.web-development', 'schema' => 'Service'],
        'seo' => ['label' => 'SEO Services', 'type' => 'service', 'view' => 'pages.services.seo', 'schema' => 'Service'],
        'manage-cloud' => ['label' => 'Managed Cloud Hosting', 'type' => 'service', 'view' => 'pages.services.manage-cloud', 'schema' => 'Service'],
        'automation' => ['label' => 'Business Automation', 'type' => 'service', 'view' => 'pages.services.automation', 'schema' => 'Service'],
        'ecommerce-solution' => ['label' => 'eCommerce Solution', 'type' => 'solution', 'view' => 'pages.solutions.ecommerce-solution', 'schema' => 'Service'],
        'cloud-solution' => ['label' => 'Cloud Solution', 'type' => 'solution', 'view' => 'pages.solutions.cloud-solution', 'schema' => 'Service'],
        'automation-solution' => ['label' => 'Automation Solution', 'type' => 'solution', 'view' => 'pages.solutions.automation-solution', 'schema' => 'Service'],
        'our-works' => ['label' => 'Our Works', 'type' => 'listing', 'view' => 'pages.our-works', 'schema' => 'CollectionPage'],
        'case-study' => ['label' => 'Case Studies', 'type' => 'listing', 'view' => 'pages.case-study', 'schema' => 'CollectionPage'],
        'our-resources' => ['label' => 'Our Resources', 'type' => 'listing', 'view' => 'pages.our-resources', 'schema' => 'CollectionPage'],
        'about-us' => ['label' => 'About Us', 'type' => 'page', 'view' => 'pages.about-us', 'schema' => 'AboutPage'],
        'affiliate-program' => ['label' => 'Affiliate Program', 'type' => 'page', 'view' => 'pages.affiliate-program', 'schema' => 'WebPage'],
        'our-founders' => ['label' => 'Our Founders', 'type' => 'page', 'view' => 'pages.our-founders', 'schema' => 'AboutPage', 'noindex' => true],
    ],

    'schema_types' => [
        'WebPage' => 'Web Page',
        'AboutPage' => 'About Page',
        'ContactPage' => 'Contact Page',
        'CollectionPage' => 'Collection / Listing Page',
        'Service' => 'Service',
        'Article' => 'Article',
        'CreativeWork' => 'Creative Work (case study / project)',
    ],

    'robots_directives' => [
        'noarchive' => 'noarchive — no cached copy',
        'nosnippet' => 'nosnippet — no text snippet',
        'noimageindex' => 'noimageindex — don\'t index images',
        'max-snippet:-1' => 'max-snippet:-1 — any snippet length',
        'max-image-preview:large' => 'max-image-preview:large — large image previews',
        'max-video-preview:-1' => 'max-video-preview:-1 — any video preview length',
    ],

    // Google Search Console is not connected yet; see App\Services\Seo\SearchPerformance.
    'search_console' => [
        'property' => env('GSC_PROPERTY', 'sc-domain:softorio.com'),
        'credentials_path' => env('GSC_CREDENTIALS_PATH'),
    ],
];
