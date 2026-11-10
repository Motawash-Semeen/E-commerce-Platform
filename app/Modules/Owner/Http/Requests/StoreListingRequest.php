<?php

namespace App\Modules\Owner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * "Add to my shop": the owner's own price, details and thumbnail for a catalog product.
 */
class StoreListingRequest extends FormRequest
{
    /**
     * Access is already limited by 'role:shop_owner' + 'has.shop' on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The product comes from the URL ({product}); copy its id in so the
     * duplicate check below can run as a normal validation rule.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['product_id' => $this->route('product')->id]);
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                // Listed at most once per shop: THIS owner's shop, never one from the request.
                Rule::unique('shop_product', 'product_id')->where('shop_id', $this->user()->shop->id),
            ],
            ...static::listingRules(),
        ];
    }

    /**
     * Shared with UpdateListingRequest.
     */
    public static function listingRules(): array
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
            'product_id.unique' => 'This product is already in your shop.',
            'old_price.gte' => 'The old price must be greater than or equal to the price.',
            'thumbnail.max' => 'The thumbnail may not be larger than 2 MB.',
        ];
    }
}
