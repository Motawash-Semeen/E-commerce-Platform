<?php

namespace App\Modules\ShopOwner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShopOwnerRequest extends FormRequest
{
    /**
     * Access is already limited by the 'auth' + 'role:admin' middleware on the admin group.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                // Keeping the same email on save must not fail as "already taken".
                Rule::unique('users', 'email')->ignore($this->route('shop_owner')),
            ],
            // Blank = keep the current password.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active' => 'active status',
        ];
    }
}
