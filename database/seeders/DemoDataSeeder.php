<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demo data: 2 shop owners with a shop each, 3 categories x 2 subcategories,
 * 10 catalog products (some by the admin, some by owners) and listings at
 * different prices. "Classic Cotton Crew Tee" and the headphones are in BOTH
 * shops; "Rattan Pendant Light" is in NO shop, so customers never see it.
 *
 * Safe to run more than once: records are matched by email / name and reused.
 */
class DemoDataSeeder extends Seeder
{
    private const OWNERS = [
        'owner1' => ['name' => 'Nadia Islam', 'email' => 'owner1@example.com', 'shop' => 'Urban Bazaar',
            'description' => "Everyday fashion and smart home finds.\nFree gift wrapping on every order."],
        'owner2' => ['name' => 'Tanvir Ahmed', 'email' => 'owner2@example.com', 'shop' => 'Corner Store',
            'description' => 'Phones, audio and kitchen essentials at fair prices.'],
    ];

    /**
     * Category => [Subcategory => [[name, old price, suggested price, created by, description], ...]]
     * "created by" is 'admin' or an OWNERS key.
     */
    private const CATALOG = [
        'Fashion' => [
            'T-Shirts' => [
                ['Classic Cotton Crew Tee', 1200, 950, 'admin', "Soft 100% cotton tee with a relaxed fit.\nPre-shrunk and machine washable."],
                ['Striped Pocket Tee', 1400, 1100, 'owner1', "Breton stripes with a chest pocket.\nMid-weight jersey that keeps its shape."],
            ],
            'Jeans' => [
                ['Slim Fit Stretch Jeans', 3500, 2800, 'admin', "Dark indigo denim with 2% stretch.\nFive-pocket styling and a slim leg."],
            ],
        ],
        'Electronics' => [
            'Smartphones' => [
                ['Nova X 128GB Smartphone', 42000, 38500, 'admin', "6.5-inch AMOLED display, 50 MP camera.\nAll-day 5000 mAh battery with fast charging."],
                ['Lite A5 64GB Smartphone', 18000, 15999, 'owner2', "Compact 6.1-inch screen and dual cameras.\nGreat value for everyday use."],
            ],
            'Headphones' => [
                ['Wireless Noise-Cancelling Headphones', 12000, 9500, 'admin', "Active noise cancelling with 30-hour battery.\nFolds flat for travel."],
                ['True Wireless Earbuds', 4500, 3600, 'owner2', "Pocket-size charging case with 24 hours total playback.\nSplash resistant (IPX4)."],
            ],
        ],
        'Home & Kitchen' => [
            'Cookware' => [
                ['Non-Stick Frying Pan 28cm', 2200, 1750, 'admin', "Three-layer non-stick coating.\nWorks on gas, electric and induction."],
            ],
            'Lighting' => [
                ['LED Desk Lamp', 2800, 2200, 'owner1', "Five brightness levels and three colour temperatures.\nUSB charging port in the base."],
                ['Rattan Pendant Light', 4200, 3900, 'admin', "Hand-woven rattan shade for a warm, natural look.\nFits standard E27 bulbs."],
            ],
        ],
    ];

    /**
     * Shop => [product name => [price, old price, active, own description or null]]
     */
    private const LISTINGS = [
        'owner1' => [
            'Classic Cotton Crew Tee' => [899, 1200, true, "Our best-selling tee, now in five colours.\nBuy two and get free delivery."],
            'Striped Pocket Tee' => [1050, 1400, true, null],
            'Slim Fit Stretch Jeans' => [2750, 3500, true, null],
            'Wireless Noise-Cancelling Headphones' => [9200, 12000, true, null],
            'LED Desk Lamp' => [2100, null, true, null],
        ],
        'owner2' => [
            'Classic Cotton Crew Tee' => [990, null, true, null],
            'Nova X 128GB Smartphone' => [37999, 42000, true, null],
            'Lite A5 64GB Smartphone' => [15499, 18000, true, null],
            'Wireless Noise-Cancelling Headphones' => [9800, 12000, true, "Includes a hard travel case and a 1-year shop warranty."],
            'True Wireless Earbuds' => [3500, 4500, true, null],
            'Non-Stick Frying Pan 28cm' => [1700, 2200, false, null],  // hidden: shows an inactive listing
        ],
    ];

    public function run(): void
    {
        $users = ['admin' => User::where('email', config('admin.email'))->firstOrFail()];
        $shops = [];

        foreach (self::OWNERS as $key => $data) {
            [$users[$key], $shops[$key]] = $this->ownerWithShop($data);
        }

        $products = $this->catalog($users);

        foreach (self::LISTINGS as $key => $listings) {
            foreach ($listings as $productName => [$price, $oldPrice, $active, $description]) {
                $listing = $shops[$key]->listings()->firstOrNew(['product_id' => $products[$productName]->id]);
                $listing->fill(['price' => $price, 'old_price' => $oldPrice, 'description' => $description]);
                $listing->is_active = $active;
                $listing->save();
            }
        }
    }

    /**
     * @return array{0: User, 1: Shop}
     */
    private function ownerWithShop(array $data): array
    {
        $owner = User::firstOrNew(['email' => $data['email']]);
        $owner->name = $data['name'];
        $owner->password = Hash::make('password');
        $owner->role = User::ROLE_SHOP_OWNER;   // role isn't fillable: set explicitly
        $owner->is_active = true;
        $owner->save();

        $shop = Shop::firstOrNew(['owner_id' => $owner->id]);
        $shop->owner_id = $owner->id;
        $shop->fill(['name' => $data['shop'], 'description' => $data['description']]);
        $shop->is_active = true;
        $shop->save();

        return [$owner, $shop];
    }

    /**
     * @return array<string, Product> keyed by product name
     */
    private function catalog(array $users): array
    {
        $products = [];

        foreach (self::CATALOG as $categoryName => $subcategories) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($subcategories as $subcategoryName => $items) {
                $subcategory = $category->subcategories()->firstOrCreate(['name' => $subcategoryName]);

                // Demo artwork per subcategory (e.g. images/headphones.jpg), else the grey placeholder.
                $image = database_path('seeders/images/'.Str::slug($subcategoryName).'.jpg');
                if (! is_file($image)) {
                    $image = database_path('seeders/images/placeholder.jpg');
                }

                foreach ($items as [$name, $oldPrice, $newPrice, $createdBy, $description]) {
                    $product = Product::firstOrNew(['subcategory_id' => $subcategory->id, 'name' => $name]);

                    if (! $product->exists) {
                        $product->fill([
                            'category_id' => $category->id,
                            'description' => $description,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                        ]);
                        // Each product gets its own copy of the image (random name), so deleting
                        // one never affects another. Copied into storage/app/public/products.
                        $product->image = Storage::disk('public')->putFile('products', new File($image));
                        $product->created_by = $users[$createdBy]->id;
                        $product->save();
                    }

                    $products[$name] = $product;
                }
            }
        }

        return $products;
    }
}
