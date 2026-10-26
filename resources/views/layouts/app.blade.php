<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Softorio — Your Vision, Our Innovation')</title>
  @hasSection('description')
  <meta name="description" content="@yield('description')" />
  @endif
  <link rel="canonical" href="{{ url()->current() }}" />

  {{-- Open Graph --}}
  <meta property="og:site_name" content="Softorio" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta property="og:title" content="@yield('title', 'Softorio — Your Vision, Our Innovation')" />
  @hasSection('description')
  <meta property="og:description" content="@yield('description')" />
  @endif
  @hasSection('image')
  <meta property="og:image" content="@yield('image')" />
  @endif

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="@yield('title', 'Softorio — Your Vision, Our Innovation')" />
  @hasSection('description')
  <meta name="twitter:description" content="@yield('description')" />
  @endif
  @hasSection('image')
  <meta name="twitter:image" content="@yield('image')" />
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
