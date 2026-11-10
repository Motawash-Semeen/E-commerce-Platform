{{-- Shared layout for error pages (Laravel picks errors/{code}.blade.php automatically).
     Deliberately standalone: no Vite build, auth()->user(), session or database calls,
     so it still renders when the error itself came from one of those. The inline
     styles copy the app's Tailwind look (gray background, white card, indigo accent). --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>@yield('code') · @yield('title') | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #f9fafb; color: #111827;
               font-family: Figtree, ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        a { color: inherit; text-decoration: none; }
        .nav { background: rgba(255,255,255,.9); border-bottom: 1px solid #e5e7eb; }
        .wrap { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }
        .brand { display: inline-flex; align-items: center; gap: .5rem; height: 4rem; font-weight: 700; font-size: 1.125rem; }
        .mark { display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: .75rem;
                color: #fff; background: linear-gradient(135deg, #6366f1, #a855f7); box-shadow: 0 4px 6px -1px rgba(99,102,241,.3); }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 1.5rem; }
        .card { width: 100%; max-width: 28rem; text-align: center; background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem;
                box-shadow: 0 1px 2px rgba(0,0,0,.05); padding: 2.5rem 2rem; }
        .code { font-size: 4.5rem; line-height: 1; font-weight: 800; letter-spacing: -.025em;
                background: linear-gradient(135deg, #4f46e5, #a855f7); -webkit-background-clip: text; background-clip: text; color: transparent; }
        h1 { margin: 1rem 0 .5rem; font-size: 1.5rem; font-weight: 700; }
        p { margin: 0 0 1.75rem; color: #6b7280; line-height: 1.6; }
        .actions { display: flex; flex-wrap: wrap; justify-content: center; gap: .5rem; }
        .btn { display: inline-flex; align-items: center; padding: .5rem 1rem; border-radius: .5rem; font-size: .875rem; font-weight: 600; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #6366f1; }
        .btn-secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .btn-secondary:hover { background: #f9fafb; }
        footer { padding: 1.5rem; text-align: center; font-size: .875rem; color: #9ca3af; }
    </style>
</head>
<body>

<header class="nav">
    <div class="wrap">
        <a class="brand" href="{{ url('/') }}">
            <span class="mark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/></svg>
            </span>
            {{ config('app.name') }}
        </a>
    </div>
</header>

<main>
    <div class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>

        <div class="actions">
            @section('actions')
                <a href="{{ url('/') }}" class="btn btn-primary">Back to shops</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Go back</a>
            @show
        </div>
    </div>
</main>

<footer>&copy; {{ date('Y') }} {{ config('app.name') }}</footer>

</body>
</html>
