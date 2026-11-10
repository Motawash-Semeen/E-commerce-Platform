<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Render ->links() with Bootstrap 5 markup instead of the default Tailwind.
        Paginator::useBootstrapFive();

        // Outside production, throw on lazy loading inside lists, so any N+1 query
        // shows up immediately in development and tests instead of slowing pages.
        Model::preventLazyLoading(! $this->app->isProduction());

        // Log viewer: admins only. Backs up the 'auth' + 'role:admin' middleware
        // in config/log-viewer.php, and is required for access in production.
        LogViewer::auth(fn ($request) => (bool) $request->user()?->isAdmin());

        // /admin/shop-owners/{shop_owner}: only shop owner accounts resolve, so an
        // admin's id gives 404 and can never be edited or deleted there.
        // (Here rather than in the route file, so it survives `route:cache`.)
        Route::bind('shop_owner', fn (string $value) => User::shopOwners()->findOrFail($value));
    }
}
