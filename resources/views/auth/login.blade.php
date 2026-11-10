@extends('layouts.app-shop')

@section('title', 'Admin login | '.config('app.name'))

@section('content')
    <div class="auth-wrap">
        <div class="surface auth-card">
            <div class="text-center mb-4">
                <span class="brand-mark mb-3" style="width: 3rem; height: 3rem; font-size: 1.35rem;">
                    <i class="bi bi-shield-lock-fill"></i>
                </span>
                <h1 class="h4 mb-1">Welcome back</h1>
                <p class="text-secondary mb-0">Sign in to manage the shop.</p>
            </div>

            {{-- e.g. "Your password has been reset." --}}
            @if (session('status'))
                <div class="alert alert-success flash py-2 small">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required
                               autofocus
                               autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small mb-2 text-decoration-none">Forgot password?</a>
                        @endif
                    </div>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input id="password"
                               type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required
                               autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                    <label for="remember_me" class="form-check-label">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    Log in <i class="bi bi-arrow-right-short"></i>
                </button>
            </form>
        </div>
    </div>
@endsection
