{{-- Shared by create and edit. Expects $owner (a new or existing shop owner User). --}}
<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text"
           id="name"
           name="name"
           value="{{ old('name', $owner->name) }}"
           class="form-control @error('name') is-invalid @enderror"
           maxlength="255"
           required
           autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
    <input type="email"
           id="email"
           name="email"
           value="{{ old('email', $owner->email) }}"
           class="form-control @error('email') is-invalid @enderror"
           maxlength="255"
           autocomplete="off"
           required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">They log in with this email.</div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">
            {{ $owner->exists ? 'New password' : 'Password' }}
            @unless ($owner->exists)<span class="text-danger">*</span>@endunless
        </label>
        <input type="password"
               id="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               autocomplete="new-password"
               @required(! $owner->exists)>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            {{ $owner->exists ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm password</label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-control"
               autocomplete="new-password"
               @required(! $owner->exists)>
    </div>
</div>

<div class="mb-3">
    {{-- The hidden 0 is sent when the switch is off (unchecked boxes send nothing). --}}
    <input type="hidden" name="is_active" value="0">
    <div class="form-check form-switch">
        <input type="checkbox"
               id="is_active"
               name="is_active"
               value="1"
               class="form-check-input @error('is_active') is-invalid @enderror"
               role="switch"
               @checked((bool) old('is_active', $owner->is_active))>
        <label for="is_active" class="form-check-label">Account is active</label>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-text">Disabled accounts can't log in.</div>
</div>
