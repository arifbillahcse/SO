<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @php
    $sectionValue = fn (string $name) => $__env->hasSection($name) ? $__env->yieldContent($name) : null;
    $seoHead = \App\Support\SeoHead::build($pageSeo, [
        'title' => $sectionValue('title'),
        'description' => $sectionValue('description'),
        'image' => $sectionValue('image'),
        'og_type' => $sectionValue('og_type'),
        'breadcrumbs' => $sectionValue('breadcrumbs'),
        'page_schema' => $sectionValue('page_schema'),
    ], $orgSettings, request()->path());
  @endphp
  <title>{{ $seoHead['title'] }}</title>
  @if($seoHead['description'])
  <meta name="description" content="{{ $seoHead['description'] }}" />
  @endif
  <meta name="robots" content="{{ $seoHead['robots'] }}" />
  <link rel="canonical" href="{{ $seoHead['canonical'] }}" />

  @foreach($seoHead['og'] as $property => $content)
  <meta property="{{ $property }}" content="{{ $content }}" />
  @endforeach
  @foreach($seoHead['twitter'] as $name => $content)
  <meta name="{{ $name }}" content="{{ $content }}" />
  @endforeach

  @foreach($seoHead['jsonld'] as $schema)
  <script type="application/ld+json">{!! \App\Support\SeoHead::encode($schema) !!}</script>
  @endforeach

  @if($orgSettings->google_analytics_id)
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $orgSettings->google_analytics_id }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $orgSettings->google_analytics_id }}');
  </script>
  @endif

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
