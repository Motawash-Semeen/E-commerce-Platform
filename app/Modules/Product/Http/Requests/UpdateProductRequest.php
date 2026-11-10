<?php

namespace App\Modules\Product\Http\Requests;

class UpdateProductRequest extends StoreProductRequest
{
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
