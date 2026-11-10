@extends('layouts.app-shop')

@section('title', 'Subcategories')

@section('content')
    <div class="page-header">
        <div>
            <h1>Subcategories</h1>
            <p>Collections inside a category, like "Headphones" under "Electronics".</p>
        </div>
        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add subcategory
        </a>
    </div>

    <div class="surface overflow-hidden">
        @if ($subcategories->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-diagram-3"></i></div>
                <h2 class="h5">No subcategories yet</h2>
                <p class="text-secondary">Add a subcategory under one of your categories.</p>
                <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add subcategory
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th class="text-center">Products</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subcategories as $subcategory)
                            <tr>
                                <td class="text-secondary">{{ $subcategories->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="cell-title">{{ $subcategory->name }}</div>
                                    <span class="slug">{{ $subcategory->slug }}</span>
                                </td>
                                <td><span class="tag"><i class="bi bi-tag"></i>{{ $subcategory->category->name }}</span></td>
                                <td class="text-center">
                                    <span class="count-badge {{ $subcategory->products_count ? '' : 'is-zero' }}">{{ $subcategory->products_count }}</span>
                                </td>
                                <td class="text-end">
                                    @include('admin.partials.row-actions', [
                                        'editUrl' => route('admin.subcategories.edit', $subcategory),
                                        'deleteUrl' => route('admin.subcategories.destroy', $subcategory),
                                        'confirm' => 'Delete this subcategory? This cannot be undone.',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('admin.partials.pagination-footer', ['paginator' => $subcategories, 'label' => 'subcategories'])
        @endif
    </div>
@endsection
