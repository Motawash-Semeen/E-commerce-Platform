<?php

namespace App\Modules\Product\Models;

use App\Models\Concerns\HasSlug;
use App\Models\ShopProduct;
use App\Models\User;
use App\Modules\Category\Models\Category;
use App\Modules\Subcategory\Models\Subcategory;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * A product in the shared catalog. Any shop can list it with its own price.
 */
class Product extends Model
{
    use HasFactory, HasSlug;

    /**
     * created_by is left out: the controller sets it to auth()->id().
     */
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'description',
        'image',
        'old_price',
        'new_price',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
        ];
    }

    /**
     * Laravel guesses factories only for App\Models\*, so point to it explicitly.
     */
    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * Full public URL of the catalog image ($product->image_url), or null.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? Storage::disk('public')->url($this->image) : null);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Every shop's listing of this product.
     */
    public function listings(): HasMany
    {
        return $this->hasMany(ShopProduct::class);
    }
}
