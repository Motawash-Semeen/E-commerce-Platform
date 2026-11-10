<?php

namespace App\Modules\Owner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A shop's own price, details and thumbnail for a catalog product.
 * Adding: POST owner/catalog/{product}. Updating: PUT owner/listings/{listing}.
 */
class ListingRequest extends FormRequest
{
    /**
     * Updating: ShopProductPolicy (only the owner of the listing's shop, 403 otherwise).
     */
    public function authorize(): bool
    {
        $listing = $this->route('listing');

        return ! $listing || $this->user()->can('update', $listing);
    }

    /**
     * When adding, copy the product id from the URL so the duplicate check runs as a rule.
     */
    protected function prepareForValidation(): void
    {
        if ($product = $this->route('product')) {
            $this->merge(['product_id' => $product->id]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'old_price' => ['nullable', 'numeric', 'gte:price', 'max:99999999'],
            'description' => ['nullable', 'string', 'max:5000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ];

        if ($this->route('product')) {
            // Listed at most once per shop: THIS owner's shop, never one from the request.
            $rules['product_id'] = ['required', Rule::unique('shop_product', 'product_id')->where('shop_id', $this->user()->shop->id)];
        }

        return $rules;
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
