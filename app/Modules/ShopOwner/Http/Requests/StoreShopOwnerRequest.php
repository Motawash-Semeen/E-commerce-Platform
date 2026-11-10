<?php

namespace App\Modules\ShopOwner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopOwnerRequest extends FormRequest
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
