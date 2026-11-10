<?php

namespace App\Modules\Shop\Http\Requests;

/**
 * Same rules as store: the owner_id unique rule already ignores the shop
 * being edited ($this->route('shop')), and the logo stays optional.
 */
class UpdateShopRequest extends StoreShopRequest
{
}
