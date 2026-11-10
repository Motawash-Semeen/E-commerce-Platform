<?php

namespace App\Modules\Shop\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create and update. Access is limited by 'role:admin' on the route group.
 */
class ShopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'owner_id' => [
                'required', 'integer',
                // A shop owner account that doesn't already have a shop (ignoring this shop on update).
                Rule::exists('users', 'id')->where('role', User::ROLE_SHOP_OWNER),
                Rule::unique('shops', 'owner_id')->ignore($this->route('shop')),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_id.exists' => 'Choose a shop owner account.',
            'owner_id.unique' => 'This shop owner already has a shop.',
            'logo.max' => 'The logo may not be larger than 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return ['owner_id' => 'owner'];
    }
}
