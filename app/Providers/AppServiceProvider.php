<?php

namespace App\Providers;

use App\Models\ShopProduct;
use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Policies\ProductPolicy;
use App\Policies\ShopProductPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Outside production, throw on lazy loading, so any N+1 query shows up
        // immediately in development and tests instead of slowing pages.
        Model::preventLazyLoading(! $this->app->isProduction());

        // Product lives in a module, so Laravel can't auto-discover its policy.
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ShopProduct::class, ShopProductPolicy::class);

        // Log viewer: admins only. Backs up the 'auth' + 'role:admin' middleware
        // in config/log-viewer.php, and is required for access in production.
        LogViewer::auth(fn ($request) => (bool) $request->user()?->isAdmin());

        // /admin/shop-owners/{shop_owner}: only shop owner accounts resolve, so an
        // admin's id gives 404 and can never be edited or deleted there.
        Route::bind('shop_owner', fn (string $value) => User::shopOwners()->findOrFail($value));
    }
}
