<?php

namespace App\Models;

use App\Modules\Product\Models\Product;
use Database\Factories\ShopProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * One shop's listing of a catalog product (table shop_product), with the
 * shop's own price, description and thumbnail.
 */
class ShopProduct extends Model
{
    use HasFactory;

    protected $table = 'shop_product';

    /**
     * shop_id is left out: it always comes from the owner's own shop.
     */
    protected $fillable = ['product_id', 'price', 'old_price', 'description', 'thumbnail', 'is_active'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): ShopProductFactory
    {
        return ShopProductFactory::new();
    }

    /**
     * What customers may see: the listing is active AND its shop is active.
     * Every public page goes through this scope.
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('shop_product.is_active', true)
            ->whereHas('shop', fn (Builder $shop) => $shop->where('is_active', true));
    }

    /**
     * The shop's own thumbnail URL ($listing->thumbnail_url), or null.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => $this->thumbnail ? Storage::disk('public')->url($this->thumbnail) : null);
    }

    /**
     * The shop's description, or the catalog's when the shop didn't write one.
     */
    protected function displayDescription(): Attribute
    {
        return Attribute::get(fn () => $this->description ?: $this->product->description);
    }

    /**
     * The shop's thumbnail, or the catalog image when the shop didn't upload one.
     */
    protected function displayImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->thumbnail_url ?? $this->product->image_url);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
