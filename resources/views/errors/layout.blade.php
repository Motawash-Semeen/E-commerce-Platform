{{-- Shared layout for error pages (Laravel picks errors/{code}.blade.php automatically).
     Deliberately standalone: no auth()->user(), session or database calls, so it still
     renders when the error itself came from the database or the session. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>@yield('code') · @yield('title') | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet"
          integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+"
          crossorigin="anonymous">
    <link href="{{ asset('css/shop.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="navbar-shop py-3">
    <div class="container">
        <a class="navbar-brand d-inline-flex align-items-center gap-2 fw-bold text-decoration-none text-body" href="{{ url('/') }}">
            <span class="brand-mark"><i class="bi bi-bag-heart-fill"></i></span>
            {{ config('app.name') }}
        </a>
    </div>
</header>

<main class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="error-page text-center">
        <div class="error-icon mb-3"><i class="bi @yield('icon', 'bi-exclamation-circle')"></i></div>
        <div class="error-code">@yield('code')</div>
        <h1 class="h3 mb-2">@yield('title')</h1>
        <p class="text-secondary mb-4">@yield('message')</p>

        <div class="d-flex flex-wrap justify-content-center gap-2">
            @section('actions')
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="bi bi-shop me-1"></i> Back to shop
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i> Go back
                </a>
            @show
        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="container py-4 text-center">&copy; {{ date('Y') }} {{ config('app.name') }}</div>
</footer>

</body>
</html>
