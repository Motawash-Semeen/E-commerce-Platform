<?php

namespace App\Modules\Category\Models;

use App\Models\Concerns\HasSlug;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = ['name'];

    /**
     * Laravel guesses factories only for App\Models\*, so point to it explicitly.
     */
    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
