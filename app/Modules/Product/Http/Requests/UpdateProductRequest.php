<?php

namespace App\Modules\Product\Http\Requests;

class UpdateProductRequest extends StoreProductRequest
{
    /**
     * ProductPolicy@update: the admin, or the shop owner who created the product.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    /**
     * Same rules as store, except the image is optional (keep the current one).
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
