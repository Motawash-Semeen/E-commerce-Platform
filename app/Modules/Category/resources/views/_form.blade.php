{{-- Shared by create and edit. Expects $category (a new or existing Category). --}}
<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text"
           id="name"
           name="name"
           value="{{ old('name', $category->name) }}"
           class="form-control @error('name') is-invalid @enderror"
           maxlength="100"
           required
           autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">The URL slug is generated from the name automatically.</div>
</div>
