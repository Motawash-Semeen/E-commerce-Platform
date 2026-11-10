<?php

namespace Database\Seeders;

use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Category => [Subcategory => [[name, old price, new price, description], ...]]
     */
    private const DATA = [
        'Men\'s Fashion' => [
            'T-Shirts' => [
                ['Classic Cotton Crew Tee', 1200, 950, "Soft 100% cotton tee with a relaxed fit.\nPre-shrunk and machine washable."],
                ['Striped Pocket Tee', 1400, 1100, "Breton stripes with a chest pocket.\nMid-weight jersey that keeps its shape."],
                ['Performance Sports Tee', 1600, 1600, "Quick-dry fabric that pulls sweat away.\nFlat seams to stop chafing."],
            ],
            'Jeans' => [
                ['Slim Fit Stretch Jeans', 3500, 2800, "Dark indigo denim with 2% stretch.\nFive-pocket styling and a slim leg."],
                ['Relaxed Straight Jeans', 3200, 2900, "Classic straight leg with room through the thigh.\nStone-washed for a lived-in look."],
            ],
        ],
        'Electronics' => [
            'Smartphones' => [
                ['Nova X 128GB Smartphone', 42000, 38500, "6.5-inch AMOLED display, 50 MP camera.\nAll-day 5000 mAh battery with fast charging."],
                ['Lite A5 64GB Smartphone', 18000, 15999, "Compact 6.1-inch screen and dual cameras.\nGreat value for everyday use."],
            ],
            'Headphones' => [
                ['Wireless Noise-Cancelling Headphones', 12000, 9500, "Active noise cancelling with 30-hour battery.\nFolds flat for travel."],
                ['True Wireless Earbuds', 4500, 3600, "Pocket-size charging case with 24 hours total playback.\nSplash resistant (IPX4)."],
                ['Wired Studio Headphones', 6000, 5400, "Closed-back design for accurate monitoring.\nDetachable 3 m cable."],
            ],
        ],
        'Home & Kitchen' => [
            'Cookware' => [
                ['Non-Stick Frying Pan 28cm', 2200, 1750, "Three-layer non-stick coating.\nWorks on gas, electric and induction."],
                ['Stainless Steel Saucepan Set', 5500, 4600, "Three saucepans with glass lids.\nDishwasher safe."],
            ],
            'Lighting' => [
                ['LED Desk Lamp', 2800, 2200, "Five brightness levels and three colour temperatures.\nUSB charging port in the base."],
                ['Rattan Pendant Light', 4200, 3900, "Hand-woven rattan shade for a warm, natural look.\nFits standard E27 bulbs."],
            ],
        ],
    ];

    /**
     * Safe to run more than once: existing records (matched by name) are reused,
     * so no duplicates are created and no extra image files are copied.
     */
    public function run(): void
    {
        foreach (self::DATA as $categoryName => $subcategories) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($subcategories as $subcategoryName => $products) {
                $subcategory = $category->subcategories()->firstOrCreate(['name' => $subcategoryName]);

                // Demo artwork per subcategory (e.g. images/headphones.jpg), else the grey placeholder.
                $placeholder = database_path('seeders/images/'.Str::slug($subcategoryName).'.jpg');
                if (! is_file($placeholder)) {
                    $placeholder = database_path('seeders/images/placeholder.jpg');
                }

                foreach ($products as [$name, $oldPrice, $newPrice, $description]) {
                    Product::firstOrCreate(
                        ['subcategory_id' => $subcategory->id, 'name' => $name],
                        // Only runs when the product is new. Each product gets its own copy
                        // of the image (random name), so deleting one never affects another.
                        fn () => [
                            'category_id' => $category->id,
                            'description' => $description,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                            'image' => Storage::disk('public')->putFile('products', new File($placeholder)),
                        ]
                    );
                }
            }
        }
    }
}
