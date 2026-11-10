@extends('layouts.app-shop')

@section('title', 'Forgot password | '.config('app.name'))

@section('content')
    <div class="auth-wrap">
        <div class="surface auth-card">
            <h1 class="h4 mb-1">Forgot your password?</h1>
            <p class="text-secondary mb-4">Enter your email and we'll send you a link to choose a new one.</p>

            @if (session('status'))
                <div class="alert alert-success flash py-2 small">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Email reset link</button>
                <a href="{{ route('login') }}" class="btn btn-link w-100 mt-2">Back to login</a>
            </form>
        </div>
    </div>
@endsection
