@extends('layouts.app-shop')

@section('title', 'Categories')

@section('content')
    <div class="page-header">
        <div>
            <h1>Categories</h1>
            <p>Top-level groups for your products, like "Electronics" or "Home &amp; Kitchen".</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add category
        </a>
    </div>

    <div class="surface overflow-hidden">
        @if ($categories->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-tags"></i></div>
                <h2 class="h5">No categories yet</h2>
                <p class="text-secondary">Create your first category to start organising products.</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add category
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th class="text-center">Subcategories</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="text-secondary">{{ $categories->firstItem() + $loop->index }}</td>
                                <td class="cell-title">{{ $category->name }}</td>
                                <td><span class="slug">{{ $category->slug }}</span></td>
                                <td class="text-center">
                                    <span class="count-badge {{ $category->subcategories_count ? '' : 'is-zero' }}">{{ $category->subcategories_count }}</span>
                                </td>
                                <td class="text-end">
                                    @include('admin.partials.row-actions', [
                                        'editUrl' => route('admin.categories.edit', $category),
                                        'deleteUrl' => route('admin.categories.destroy', $category),
                                        'confirm' => 'Delete this category? This cannot be undone.',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('admin.partials.pagination-footer', ['paginator' => $categories, 'label' => 'categories'])
        @endif
    </div>
@endsection
