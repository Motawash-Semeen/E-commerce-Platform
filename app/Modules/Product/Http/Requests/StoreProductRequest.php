<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Used by both the admin and shop owners to add a catalog product.
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Access is already limited by the route middleware (role:admin or role:shop_owner).
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => [
                'required',
                'integer',
                // The subcategory must belong to the selected category.
                Rule::exists('subcategories', 'id')
                    ->where('category_id', $this->integer('category_id')),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'old_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'new_price' => [
                'required', 'numeric', 'min:0', 'max:99999999',
                // Only compare when an old price was given.
                Rule::when($this->filled('old_price'), ['lte:old_price']),
            ],
            // Owner form only: "Also add to my shop". Ignored for the admin.
            'add_to_shop' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'subcategory_id.exists' => 'The selected subcategory does not belong to the selected category.',
            'new_price.lte' => 'The price must be less than or equal to the old price.',
            'image.max' => 'The image may not be larger than 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'subcategory_id' => 'subcategory',
            'new_price' => 'price',
        ];
    }
}
