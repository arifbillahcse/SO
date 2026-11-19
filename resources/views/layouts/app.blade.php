<!DOCTYPE html>
<html lang="en" data-theme="{{ $orgSettings->defaultTheme() === 'dark' ? 'dark' : 'light' }}" data-default-theme="{{ $orgSettings->defaultTheme() }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @if($orgSettings->recaptcha_enabled)
  <meta name="recaptcha-site-key" content="{{ $orgSettings->recaptcha_site_key }}" />
  @endif
  <link rel="icon" href="{{ $orgSettings->faviconUrl() }}" />
  @include('partials.site-tracking-head')
  <script>
    (() => {
      const root = document.documentElement;
      let preference = root.dataset.defaultTheme;
      try {
        const saved = localStorage.getItem('softorio-theme');
        if (saved === 'light' || saved === 'dark') preference = saved;
      } catch (error) {}
      root.dataset.themePreference = preference;
      root.dataset.theme = preference === 'auto'
        ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
        : preference;
    })();
  </script>
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

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/nav.js'])
  @stack('head')
</head>
<body>
  @include('partials.site-tracking-body-start')

  <a href="#main-content" class="skip-link">Skip to main content</a>
  @include('partials.nav')

  <div id="main-content" tabindex="-1">@yield('content')</div>

  @include('partials.footer')
  @include('partials.whatsapp-chat')

  @stack('scripts')
  @include('partials.site-tracking-body-end')
</body>
</html>
