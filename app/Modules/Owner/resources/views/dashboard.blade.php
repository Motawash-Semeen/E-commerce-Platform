@extends('layouts.app-shop')

@section('title', 'Shop owner dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1>Shop owner dashboard</h1>
            <p>Welcome, {{ auth()->user()->name }}.</p>
        </div>
    </div>

    <div class="surface empty-state">
        <div class="empty-icon"><i class="bi bi-shop-window"></i></div>
        <h2 class="h5">Your shop tools will appear here</h2>
        <p class="text-secondary mb-4">Once the admin links your account to a shop, you'll manage its products from this page.</p>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
            <i class="bi bi-person-gear me-1"></i> Edit your profile
        </a>
    </div>
@endsection
