<?php

namespace App\Modules\Owner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    /**
     * ShopProductPolicy@update: only the owner of the listing's shop (403 otherwise).
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('listing'));
    }

    public function rules(): array
    {
        return StoreListingRequest::listingRules();
    }

    public function messages(): array
    {
        return [
            'old_price.gte' => 'The old price must be greater than or equal to the price.',
            'thumbnail.max' => 'The thumbnail may not be larger than 2 MB.',
        ];
    }
}
