<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Softorio — Your Vision, Our Innovation')</title>
  @hasSection('description')
  <meta name="description" content="@yield('description')" />
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
