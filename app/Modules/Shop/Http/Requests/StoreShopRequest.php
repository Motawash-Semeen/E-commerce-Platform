<?php

namespace App\Modules\Shop\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShopRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'owner_id' => [
                'required',
                'integer',
                // Must be a shop owner account...
                Rule::exists('users', 'id')->where('role', User::ROLE_SHOP_OWNER),
                // ...that doesn't already have a shop (ignoring this shop on update).
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
        return [
            'owner_id' => 'owner',
        ];
    }
}
