<?php

namespace App\Modules\Product\Models;

use App\Models\Concerns\HasSlug;
use App\Modules\Category\Models\Category;
use App\Modules\Subcategory\Models\Subcategory;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, HasSlug;

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
     * Public URL of the image ($product->image_url), or null if there is none.
     * asset() uses the current host, so it works with any APP_URL.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? asset('storage/'.$this->image) : null);
    }

    /**
     * Whole-number discount ($product->discount_percent), or null when the
     * product isn't reduced (no old price, or old price not above new price).
     */
    protected function discountPercent(): Attribute
    {
        return Attribute::get(function () {
            if ($this->old_price === null || (float) $this->old_price <= (float) $this->new_price) {
                return null;
            }

            return (int) round((1 - $this->new_price / $this->old_price) * 100);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
