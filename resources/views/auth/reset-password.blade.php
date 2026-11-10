@extends('layouts.app-shop')

@section('title', 'Reset password | '.config('app.name'))

@section('content')
    <div class="auth-wrap">
        <div class="surface auth-card">
            <h1 class="h4 mb-4">Choose a new password</h1>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New password</label>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary w-100">Reset password</button>
            </form>
        </div>
    </div>
@endsection
