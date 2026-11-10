<?php

namespace App\Policies;

use App\Models\ShopProduct;
use App\Models\User;

class ShopProductPolicy
{
    /**
     * A listing belongs to exactly one shop; only that shop's owner may change it.
     */
    public function update(User $user, ShopProduct $listing): bool
    {
        return $this->ownsListing($user, $listing);
    }

    public function delete(User $user, ShopProduct $listing): bool
    {
        return $this->ownsListing($user, $listing);
    }

    private function ownsListing(User $user, ShopProduct $listing): bool
    {
        $shopId = $user->shop?->id;

        return $shopId !== null && (int) $listing->shop_id === $shopId;
    }
}
