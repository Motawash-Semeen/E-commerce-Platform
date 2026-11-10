<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use HasFactory, HasSlug;

    /**
     * owner_id is left out: the admin's controller sets it explicitly,
     * so it can never be slipped in through mass assignment.
     */
    protected $fillable = ['name', 'description', 'logo', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Full public URL of the logo ($shop->logo_url), or null.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->logo ? Storage::disk('public')->url($this->logo) : null);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The shop's listings (rows of shop_product).
     */
    public function listings(): HasMany
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'shop_product')
            ->using(ShopProduct::class)
            ->withPivot(['id', 'price', 'old_price', 'description', 'thumbnail', 'is_active'])
            ->withTimestamps();
    }
}
