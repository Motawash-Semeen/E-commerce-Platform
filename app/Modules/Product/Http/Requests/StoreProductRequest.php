<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Access is already limited by the 'auth' middleware on the admin group.
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
            'old_price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'new_price' => ['required', 'numeric', 'min:0', 'lte:old_price'],
        ];
    }

    public function messages(): array
    {
        return [
            'subcategory_id.exists' => 'The selected subcategory does not belong to the selected category.',
            'new_price.lte' => 'The new price must be less than or equal to the old price.',
            'image.max' => 'The image may not be larger than 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'subcategory_id' => 'subcategory',
        ];
    }
}
