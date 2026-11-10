<?php

namespace App\Modules\Subcategory\Models;

use App\Models\Concerns\HasSlug;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use Database\Factories\SubcategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = ['category_id', 'name'];

    /**
     * Laravel guesses factories only for App\Models\*, so point to it explicitly.
     */
    protected static function newFactory(): SubcategoryFactory
    {
        return SubcategoryFactory::new();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
