@extends('layouts.app-shop')

@section('title', $product->name.' | '.config('app.name'))
{{-- First 155 characters of the description, on one line, for search results. --}}
@section('meta_description', \Illuminate\Support\Str::of($product->description)->squish()->limit(155))

@section('content')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Shop</a></li>
            <li class="breadcrumb-item">{{ $product->category->name }}</li>
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}#{{ $product->subcategory->slug }}">{{ $product->subcategory->name }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-6">
            <div class="gallery">
                @if ($product->discount_percent)
                    <span class="badge-discount">-{{ $product->discount_percent }}%</span>
                @endif

                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary display-1">
                        <i class="bi bi-image"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="tag"><i class="bi bi-tag"></i>{{ $product->category->name }}</span>
                <a href="{{ route('home') }}#{{ $product->subcategory->slug }}" class="tag">
                    <i class="bi bi-grid"></i>{{ $product->subcategory->name }}
                </a>
            </div>

            <h1 class="display-6 fw-bold mb-2">{{ $product->name }}</h1>
            <p class="text-secondary mb-4">{{ $product->category->name }} › {{ $product->subcategory->name }}</p>

            <div class="surface p-4 mb-4">
                <div class="price price-lg mb-2">
                    <span class="price-new">{{ number_format($product->new_price, 2) }}</span>
                    @if ($product->discount_percent)
                        <span class="price-old">{{ number_format($product->old_price, 2) }}</span>
                    @endif
                </div>
                @if ($product->discount_percent)
                    <span class="save-pill">
                        <i class="bi bi-lightning-charge-fill"></i>
                        You save {{ number_format($product->old_price - $product->new_price, 2) }} ({{ $product->discount_percent }}% off)
                    </span>
                @else
                    <span class="text-secondary small">Everyday fair price</span>
                @endif
            </div>

            <h2 class="h6 text-uppercase text-secondary fw-bold small mb-2" style="letter-spacing: .06em;">Description</h2>
            {{-- Escaped with {{ }}; "pre-line" keeps the admin's line breaks without any raw HTML output. --}}
            <p class="mb-4 lh-lg" style="white-space: pre-line;">{{ $product->description }}</p>

            <ul class="feature-list mb-4">
                <li><i class="bi bi-tag"></i> <span>Category: <strong class="text-body">{{ $product->category->name }}</strong></span></li>
                <li><i class="bi bi-grid"></i> <span>Collection: <strong class="text-body">{{ $product->subcategory->name }}</strong></span></li>
                <li><i class="bi bi-upc-scan"></i> <span>Product code: <span class="slug">{{ $product->slug }}</span></span></li>
            </ul>

            <a href="{{ route('home') }}#{{ $product->subcategory->slug }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to {{ $product->subcategory->name }}
            </a>
        </div>
    </div>
@endsection
