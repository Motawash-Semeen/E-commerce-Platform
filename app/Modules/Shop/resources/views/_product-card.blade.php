{{-- One product card. Expects $product (with category/subcategory available on the parent). --}}
<article class="product-card">
    <div class="product-media">
        @if ($product->discount_percent)
            <span class="badge-discount">-{{ $product->discount_percent }}%</span>
        @endif

        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary fs-1">
                <i class="bi bi-image"></i>
            </div>
        @endif
    </div>

    <div class="product-body">
        <div class="product-meta">{{ $subcategory->name }}</div>
        <h3 class="product-name">{{ $product->name }}</h3>
        <p class="product-excerpt">{{ \Illuminate\Support\Str::limit($product->description, 110) }}</p>

        <div class="d-flex align-items-end justify-content-between gap-2 mt-auto">
            <div class="price">
                <span class="price-new">{{ number_format($product->new_price, 2) }}</span>
                @if ($product->discount_percent)
                    <span class="price-old">{{ number_format($product->old_price, 2) }}</span>
                @endif
            </div>
            <a href="{{ route('products.show', $product) }}" class="btn btn-light-primary btn-sm stretched-link text-nowrap">
                View details <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>
    </div>
</article>
