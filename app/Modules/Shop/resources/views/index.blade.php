@extends('layouts.app-shop')

@section('title', 'Shop | '.config('app.name'))
@section('meta_description', 'Browse all products by category and subcategory at '.config('app.name').'.')

@section('content')
    {{-- Hero --}}
    <section class="hero mb-5">
        <i class="bi bi-bag-heart hero-art" aria-hidden="true"></i>
        <div class="position-relative">
            <span class="hero-eyebrow mb-3"><i class="bi bi-stars"></i> Welcome to {{ config('app.name') }}</span>
            <h1 class="mb-3">Find something<br>you'll love.</h1>
            <p class="lead mb-4">Browse every collection by category and spot the best deals at a glance.</p>

            <div class="d-flex flex-wrap align-items-center gap-4">
                <a href="#products" class="btn btn-light btn-lg text-primary">
                    Start shopping <i class="bi bi-arrow-down-short"></i>
                </a>
                <div class="d-flex flex-wrap gap-3 gap-sm-4">
                    <div class="hero-stat"><strong>{{ $stats['products'] }}</strong><span>Products</span></div>
                    <div class="hero-stat"><strong>{{ $stats['categories'] }}</strong><span>Categories</span></div>
                    <div class="hero-stat"><strong>{{ $stats['collections'] }}</strong><span>Collections</span></div>
                </div>
            </div>
        </div>
    </section>

    @if ($subcategories->isEmpty())
        <div class="surface empty-state">
            <div class="empty-icon"><i class="bi bi-bag"></i></div>
            <h2 class="h5">No products yet</h2>
            <p class="text-secondary mb-0">We're stocking the shelves. Please check back soon.</p>
        </div>
    @else
        {{-- One tile per collection; each jumps to its section below. --}}
        <section id="products" class="shop-section mb-5">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                <div>
                    <div class="section-eyebrow">Shop by collection</div>
                    <h2 class="section-title">Browse collections</h2>
                </div>
                <span class="text-secondary small">
                    {{ $stats['collections'] }} collections · {{ $stats['categories'] }} categories
                </span>
            </div>

            <div class="row row-cols-2 row-cols-md-3 g-3 g-lg-4">
                @foreach ($subcategories as $subcategory)
                    @php
                        $cover = $subcategory->products->firstWhere('image', '!=', null);
                        $count = $subcategory->products->count();
                    @endphp
                    <div class="col">
                        <a href="#{{ $subcategory->slug }}" class="collection-tile">
                            @if ($cover)
                                <img src="{{ $cover->image_url }}" alt="" loading="lazy">
                            @endif
                            <div class="collection-tile-body">
                                <span class="collection-tile-category">{{ $subcategory->category->name }}</span>
                                <span class="collection-tile-name">{{ $subcategory->name }}</span>
                                <span class="collection-tile-meta">
                                    {{ $count }} {{ \Illuminate\Support\Str::plural('item', $count) }}
                                    · from {{ number_format((float) $subcategory->products->min('new_price'), 2) }}
                                </span>
                            </div>
                            <span class="collection-tile-arrow" aria-hidden="true"><i class="bi bi-arrow-right"></i></span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        @foreach ($subcategories as $subcategory)
            <section id="{{ $subcategory->slug }}" class="shop-section mb-5">
                <div class="d-flex align-items-end justify-content-between mb-3">
                    <div>
                        <div class="section-eyebrow">{{ $subcategory->category->name }}</div>
                        <h2 class="section-title">{{ $subcategory->category->name }} › {{ $subcategory->name }}</h2>
                    </div>
                    <span class="text-secondary small d-none d-sm-inline">
                        {{ $subcategory->products->count() }} {{ \Illuminate\Support\Str::plural('item', $subcategory->products->count()) }}
                    </span>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    @foreach ($subcategory->products as $product)
                        <div class="col">
                            @include('Shop::_product-card')
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
@endsection
