<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @php
    $defaultTitle = 'Softorio — Website Development, Cloud & SEO Agency';
    $seoTitle = $pageSeo?->title ?: $__env->yieldContent('title', $defaultTitle);
    $seoDescription = $pageSeo?->meta_description ?: ($__env->yieldContent('description') ?: null);
    $seoImage = $pageSeo?->og_image
        ? asset('storage/' . $pageSeo->og_image)
        : ($__env->yieldContent('image') ?: asset('images/logo-icon.png'));
    $canonicalUrl = $pageSeo?->canonical_url ?: url()->current();
    $robotsParts = [
        ($pageSeo?->robots_index ?? true) ? 'index' : 'noindex',
        ($pageSeo?->robots_follow ?? true) ? 'follow' : 'nofollow',
    ];
    if (! empty($pageSeo?->robots_advanced)) {
        $robotsParts = array_merge($robotsParts, array_map('trim', explode(',', $pageSeo->robots_advanced)));
    }

    $currentRouteName = request()->route()?->getName();
    $breadcrumbSchema = null;
    if ($currentRouteName && $currentRouteName !== 'home') {
        $breadcrumbSchema = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => \Illuminate\Support\Str::headline($currentRouteName), 'item' => $canonicalUrl],
            ],
        ]);
    }

    $organizationSchema = json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $orgSettings->name,
        'url' => url('/'),
        'logo' => $orgSettings->logo ? asset('storage/' . $orgSettings->logo) : null,
        'description' => $orgSettings->description,
        'telephone' => $orgSettings->telephone,
        'email' => $orgSettings->email,
        'sameAs' => array_values(array_filter([
            $orgSettings->facebook_url,
            $orgSettings->twitter_url,
            $orgSettings->linkedin_url,
            $orgSettings->youtube_url,
        ])),
    ]));

    $pageSchemaType = $pageSeo?->schema_type ?: 'WebPage';
    $pageSchema = json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => $pageSchemaType,
        'name' => $seoTitle,
        'description' => $seoDescription,
        'url' => $canonicalUrl,
        'provider' => $pageSchemaType === 'Service' ? ['@type' => 'Organization', 'name' => $orgSettings->name, 'url' => url('/')] : null,
    ]));
  @endphp
  <title>{{ $seoTitle }}</title>
  <meta name="robots" content="{{ implode(', ', $robotsParts) }}" />
  @if($seoDescription)
  <meta name="description" content="{{ $seoDescription }}" />
  @endif
  <link rel="canonical" href="{{ $canonicalUrl }}" />

  {{-- Open Graph --}}
  <meta property="og:site_name" content="Softorio" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ $canonicalUrl }}" />
  <meta property="og:title" content="{{ $pageSeo?->og_title ?: $seoTitle }}" />
  @if($seoDescription)
  <meta property="og:description" content="{{ $pageSeo?->og_description ?: $seoDescription }}" />
  @endif
  @if($seoImage)
  <meta property="og:image" content="{{ $seoImage }}" />
  @endif

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="{{ $pageSeo?->twitter_card_type ?: 'summary_large_image' }}" />
  <meta name="twitter:title" content="{{ $pageSeo?->twitter_title ?: $seoTitle }}" />
  @if($seoDescription)
  <meta name="twitter:description" content="{{ $pageSeo?->twitter_description ?: $seoDescription }}" />
  @endif
  @if($seoImage)
  <meta name="twitter:image" content="{{ $pageSeo?->twitter_image ? asset('storage/' . $pageSeo->twitter_image) : $seoImage }}" />
  @endif

  <script type="application/ld+json">{!! $organizationSchema !!}</script>
  <script type="application/ld+json">{!! $pageSchema !!}</script>
  @if($breadcrumbSchema)
  <script type="application/ld+json">{!! $breadcrumbSchema !!}</script>
  @endif

  @if($orgSettings->google_analytics_id)
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $orgSettings->google_analytics_id }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $orgSettings->google_analytics_id }}');
  </script>
  @endif

  @yield('meta')
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/nav.js'])
  @stack('head')
</head>
<body>

  @include('partials.nav')

  @yield('content')

  @include('partials.footer')

  @stack('scripts')
</body>
</html>
