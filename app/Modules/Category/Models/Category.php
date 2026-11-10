<?php

namespace App\Modules\Category\Models;

use App\Models\Concerns\HasSlug;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    /**
     * Categories with their subcategories, for the product form's dependent
     * dropdown (filtered in Vue): [{id, name, subcategories: [{id, name}]}].
     */
    public static function withSubcategoryOptions(): Collection
    {
        return static::with(['subcategories' => fn ($query) => $query->orderBy('name')->select(['id', 'category_id', 'name'])])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'subcategories' => $category->subcategories->map->only(['id', 'name'])->values(),
            ]);
    }
}
