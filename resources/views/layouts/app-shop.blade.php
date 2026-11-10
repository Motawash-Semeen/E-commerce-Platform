<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Browse products by category on '.config('app.name').'.')">

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

<nav class="navbar navbar-expand-xl navbar-shop sticky-top py-2">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <span class="brand-mark"><i class="bi bi-bag-heart-fill"></i></span>
            {{ config('app.name') }}
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-xl-auto gap-xl-1 mt-3 mt-xl-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home', 'products.show') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-shop"></i> Shop
                    </a>
                </li>

                @auth
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-tags"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}" href="{{ route('admin.subcategories.index') }}">
                                <i class="bi bi-diagram-3"></i> Subcategories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                <i class="bi bi-box-seam"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.shop-owners.*') ? 'active' : '' }}" href="{{ route('admin.shop-owners.index') }}">
                                <i class="bi bi-people"></i> Shop owners
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('log-viewer.index') }}">
                                <i class="bi bi-journal-text"></i> Logs
                            </a>
                        </li>
                    @elseif (auth()->user()->isShopOwner())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> My dashboard
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <ul class="navbar-nav align-items-xl-center mt-2 mt-xl-0">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="text-body">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text small text-secondary">
                                    Signed in as {{ auth()->user()->isAdmin() ? 'admin' : 'shop owner' }}
                                </span>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear"></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-light-primary btn-sm" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Log in
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5 flex-grow-1">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show flash" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show flash" role="alert">
            <i class="bi bi-exclamation-octagon-fill"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</main>

<footer class="site-footer mt-auto">
    <div class="container py-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="brand-mark"><i class="bi bi-bag-heart-fill"></i></span>
            <span class="brand-text">{{ config('app.name') }}</span>
        </div>
        <div class="d-flex gap-4">
            <a href="{{ route('home') }}">Shop</a>
            @auth
                <a href="{{ route('dashboard') }}">My dashboard</a>
            @else
                <a href="{{ route('login') }}">Log in</a>
            @endauth
        </div>
        <div>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

@stack('scripts')
</body>
</html>
