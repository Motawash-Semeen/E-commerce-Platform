{{-- Shared by create and edit. Expects $product, $categories and $subcategories
     (the subcategories of the currently selected category). --}}
@php
    $selectedCategory = (string) old('category_id', $product->category_id);
    $selectedSubcategory = (string) old('subcategory_id', $product->subcategory_id);
@endphp

<div class="row g-4 g-lg-5">
    {{-- Left column: what the product is --}}
    <div class="col-lg-7">
        <h2 class="form-section-title">Product details</h2>
        <p class="form-section-help">Where it appears in the shop and how it's described.</p>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                <select id="category_id"
                        name="category_id"
                        class="form-select @error('category_id') is-invalid @enderror"
                        required>
                    <option value="">-- Select a category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                                data-url="{{ route('admin.categories.subcategories', $category) }}"
                                @selected($selectedCategory === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="subcategory_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
                <select id="subcategory_id"
                        name="subcategory_id"
                        class="form-select @error('subcategory_id') is-invalid @enderror"
                        required
                        @disabled($subcategories->isEmpty())>
                    @if ($selectedCategory === '')
                        <option value="">-- Select a category first --</option>
                    @elseif ($subcategories->isEmpty())
                        <option value="">No subcategories in this category</option>
                    @else
                        <option value="">-- Select a subcategory --</option>
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" @selected($selectedSubcategory === (string) $subcategory->id)>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('subcategory_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name', $product->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   maxlength="150"
                   placeholder="e.g. Wireless Noise-Cancelling Headphones"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea id="description"
                      name="description"
                      rows="7"
                      class="form-control @error('description') is-invalid @enderror"
                      maxlength="5000"
                      placeholder="Key features, materials, sizes... Line breaks are kept in the shop."
                      required>{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Right column: price and photo --}}
    <div class="col-lg-5">
        <h2 class="form-section-title">Pricing</h2>
        <p class="form-section-help">The old price is shown struck through when it's higher.</p>

        <div class="row">
            <div class="col-6 mb-3">
                <label for="old_price" class="form-label">Old price <span class="text-danger">*</span></label>
                <input type="number"
                       id="old_price"
                       name="old_price"
                       value="{{ old('old_price', $product->old_price) }}"
                       class="form-control @error('old_price') is-invalid @enderror"
                       min="0"
                       max="99999999"
                       step="0.01"
                       placeholder="0.00"
                       required>
                @error('old_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-6 mb-3">
                <label for="new_price" class="form-label">New price <span class="text-danger">*</span></label>
                <input type="number"
                       id="new_price"
                       name="new_price"
                       value="{{ old('new_price', $product->new_price) }}"
                       class="form-control @error('new_price') is-invalid @enderror"
                       min="0"
                       max="99999999"
                       step="0.01"
                       placeholder="0.00"
                       required>
                @error('new_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-text mb-4"><i class="bi bi-info-circle me-1"></i>New price must be less than or equal to the old price.</div>

        <h2 class="form-section-title">Image @unless ($product->exists)<span class="text-danger">*</span>@endunless</h2>
        <p class="form-section-help">JPG, PNG or WEBP, max 2 MB. A square photo looks best.</p>

        <div class="d-flex align-items-start gap-3 mb-3">
            {{-- Shows the current image, then a live preview of a newly chosen file. --}}
            <img id="image-preview"
                 @if ($product->image_url) src="{{ $product->image_url }}" @endif
                 alt="{{ $product->exists ? $product->name : 'Image preview' }}"
                 class="image-preview {{ $product->image_url ? '' : 'd-none' }}">
            <div id="image-placeholder" class="image-preview thumb-empty fs-2 {{ $product->image_url ? 'd-none' : '' }}">
                <i class="bi bi-image"></i>
            </div>
            <div class="small text-secondary pt-1">
                @if ($product->image_url)
                    Current image. Choose a new file only if you want to replace it.
                @else
                    No image selected yet.
                @endif
            </div>
        </div>

        <input type="file"
               id="image"
               name="image"
               class="form-control @error('image') is-invalid @enderror"
               accept=".jpg,.jpeg,.png,.webp"
               @required(! $product->exists)>
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    // Reload the subcategory list whenever the category changes.
    (function () {
        const categorySelect = document.getElementById('category_id');
        const subcategorySelect = document.getElementById('subcategory_id');
        let latestRequest = 0; // ignore slow responses from earlier selections

        function resetSubcategories(placeholder) {
            subcategorySelect.replaceChildren(new Option(placeholder, ''));
            subcategorySelect.disabled = true;
        }

        categorySelect.addEventListener('change', async function () {
            const url = categorySelect.selectedOptions[0]?.dataset.url;

            if (!url) {
                resetSubcategories('-- Select a category first --');
                return;
            }

            const requestId = ++latestRequest;
            resetSubcategories('Loading...');

            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                const subcategories = await response.json();

                if (requestId !== latestRequest) {
                    return;
                }

                if (subcategories.length === 0) {
                    resetSubcategories('No subcategories in this category');
                    return;
                }

                resetSubcategories('-- Select a subcategory --');
                // new Option() sets text, not HTML, so names are never parsed as markup.
                subcategories.forEach(function (sub) {
                    subcategorySelect.add(new Option(sub.name, sub.id));
                });
                subcategorySelect.disabled = false;
            } catch (error) {
                if (requestId === latestRequest) {
                    resetSubcategories('Could not load subcategories. Please try again.');
                }
            }
        });
    })();

    // Preview the chosen image before uploading (the file never leaves the browser here).
    (function () {
        const input = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        let objectUrl = null;

        input.addEventListener('change', function () {
            const file = input.files[0];
            if (!file || !file.type.startsWith('image/')) {
                return;
            }
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }
            objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        });
    })();
</script>
@endpush
