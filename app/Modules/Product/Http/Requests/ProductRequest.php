<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Catalog product create and update, shared by the admin and shop owners.
 */
class ProductRequest extends FormRequest
{
    /**
     * Creating: the route's role middleware is enough. Updating: ProductPolicy@update
     * (the admin, or the shop owner who created the product).
     */
    public function authorize(): bool
    {
        $product = $this->route('product');

        return ! $product || $this->user()->can('update', $product);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => [
                'required', 'integer',
                // The subcategory must belong to the selected category.
                Rule::exists('subcategories', 'id')->where('category_id', $this->integer('category_id')),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            // Required for a new product; on update an empty field keeps the current image.
            'image' => [$this->route('product') ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'old_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'new_price' => [
                'required', 'numeric', 'min:0', 'max:99999999',
                Rule::when($this->filled('old_price'), ['lte:old_price']),
            ],
            'add_to_shop' => ['sometimes', 'boolean'],   // owner create form only
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
        return ['category_id' => 'category', 'subcategory_id' => 'subcategory', 'new_price' => 'price'];
    }
}
