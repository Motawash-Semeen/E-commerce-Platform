<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Product\Models\Product;

class ProductPolicy
{
    /**
     * The admin can edit any catalog product; a shop owner only the ones they created.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin()
            || ($user->isShopOwner() && (int) $product->created_by === $user->id);
    }

    /**
     * Only the admin deletes, and only products no shop is selling.
     * (The controller checks the listings first to show a friendly message.)
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin() && ! $product->listings()->exists();
    }
}
