@extends('layouts.app-shop')

@section('title', 'Products')

@section('content')
    <div class="page-header">
        <div>
            <h1>Products</h1>
            <p>Everything listed in the shop, newest first.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add product
        </a>
    </div>

    <div class="surface overflow-hidden">
        @if ($products->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-box-seam"></i></div>
                <h2 class="h5">No products yet</h2>
                <p class="text-secondary">Add your first product with a photo and prices.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add product
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category › Subcategory</th>
                            <th class="text-end">Old price</th>
                            <th class="text-end">New price</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="cell-product">
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="thumb">
                                        @else
                                            <span class="thumb thumb-empty"><i class="bi bi-image"></i></span>
                                        @endif
                                        <div>
                                            <a href="{{ route('products.show', $product) }}" class="cell-title text-decoration-none text-wrap" target="_blank" rel="noopener" title="View in shop">
                                                {{ $product->name }} <i class="bi bi-box-arrow-up-right small text-secondary"></i>
                                            </a>
                                            <div><span class="slug">{{ $product->slug }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $product->category->name }}</div>
                                    <div class="cell-sub">{{ $product->subcategory->name }}</div>
                                </td>
                                <td class="text-end">
                                    @if ($product->old_price !== null)
                                        <del class="text-secondary">{{ number_format($product->old_price, 2) }}</del>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold">{{ number_format($product->new_price, 2) }}</div>
                                    @if ($product->discount_percent)
                                        <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">-{{ $product->discount_percent }}%</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @include('admin.partials.row-actions', [
                                        'editUrl' => route('admin.products.edit', $product),
                                        'deleteUrl' => route('admin.products.destroy', $product),
                                        'confirm' => 'Delete this product and its image? This cannot be undone.',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('admin.partials.pagination-footer', ['paginator' => $products, 'label' => 'products'])
        @endif
    </div>
@endsection
