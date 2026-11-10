<?php

namespace App\Modules\ShopOwner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create and update. Access is limited by 'role:admin' on the route group.
 */
class ShopOwnerRequest extends FormRequest
{
    public function rules(): array
    {
        $owner = $this->route('shop_owner');   // null when creating

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($owner),
            ],
            // Required on create; on update a blank password keeps the current one.
            'password' => [$owner ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['is_active' => 'active status'];
    }
}
