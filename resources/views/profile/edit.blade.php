@extends('layouts.app-shop')

@section('title', 'Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="page-header">
                <div>
                    <h1>Profile</h1>
                    <p>Your admin account details.</p>
                </div>
            </div>

            {{-- Name and email --}}
            <form method="POST" action="{{ route('profile.update') }}" class="surface p-4 p-lg-5 mb-4">
                @csrf
                @method('PATCH')

                <h2 class="form-section-title">Account details</h2>
                <p class="form-section-help">Used to log in to the admin area.</p>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save details</button>
            </form>

            {{-- Password (errors come back in the "updatePassword" bag) --}}
            <form method="POST" action="{{ route('password.update') }}" class="surface p-4 p-lg-5">
                @csrf
                @method('PUT')

                <h2 class="form-section-title">Change password</h2>
                <p class="form-section-help">Use a long, unique password.</p>

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current password</label>
                    <input id="current_password" type="password" name="current_password"
                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                           autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New password</label>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary">Update password</button>
            </form>
        </div>
    </div>
@endsection
