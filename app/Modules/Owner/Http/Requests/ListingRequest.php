<?php

namespace App\Modules\Owner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A shop's own price, details and thumbnail for a catalog product (add and edit).
 */
class ListingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'old_price' => ['nullable', 'numeric', 'gte:price', 'max:99999999'],
            'description' => ['nullable', 'string', 'max:5000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'old_price.gte' => 'The old price must be greater than or equal to the price.',
            'thumbnail.max' => 'The thumbnail may not be larger than 2 MB.',
        ];
    }
}
