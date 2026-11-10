@extends('layouts.app-shop')

@section('title', 'Admin dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1>Admin dashboard</h1>
            <p>Welcome back, {{ auth()->user()->name }}. Manage the shared catalogue from here.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3 g-lg-4">
        <div class="col">
            <a href="{{ route('admin.categories.index') }}" class="surface stat-card">
                <span class="stat-icon"><i class="bi bi-tags"></i></span>
                <span class="stat-value">{{ $stats['categories'] }}</span>
                <span class="stat-label">Categories</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.subcategories.index') }}" class="surface stat-card">
                <span class="stat-icon"><i class="bi bi-diagram-3"></i></span>
                <span class="stat-value">{{ $stats['subcategories'] }}</span>
                <span class="stat-label">Subcategories</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.products.index') }}" class="surface stat-card">
                <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
                <span class="stat-value">{{ $stats['products'] }}</span>
                <span class="stat-label">Products</span>
            </a>
        </div>
    </div>

    <div class="surface p-4 mt-4">
        <h2 class="form-section-title">Quick actions</h2>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add product</a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg me-1"></i> Add category</a>
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg me-1"></i> Add subcategory</a>
            <a href="{{ route('admin.shop-owners.create') }}" class="btn btn-outline-primary"><i class="bi bi-person-plus me-1"></i> Add shop owner</a>
            <a href="{{ route('log-viewer.index') }}" class="btn btn-light"><i class="bi bi-journal-text me-1"></i> View logs</a>
        </div>
    </div>
@endsection
