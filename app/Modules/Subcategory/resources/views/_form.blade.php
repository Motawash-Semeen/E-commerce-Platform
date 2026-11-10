{{-- Shared by create and edit. Expects $subcategory and $categories (id => name). --}}
<div class="mb-3">
    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
    <select id="category_id"
            name="category_id"
            class="form-select @error('category_id') is-invalid @enderror"
            required>
        <option value="">-- Select a category --</option>
        @foreach ($categories as $id => $name)
            <option value="{{ $id }}" @selected((string) old('category_id', $subcategory->category_id) === (string) $id)>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($categories->isEmpty())
        <div class="form-text text-danger">
            There are no categories yet. <a href="{{ route('admin.categories.create') }}">Create one first</a>.
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text"
           id="name"
           name="name"
           value="{{ old('name', $subcategory->name) }}"
           class="form-control @error('name') is-invalid @enderror"
           maxlength="100"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">The URL slug is generated from the name automatically.</div>
</div>
