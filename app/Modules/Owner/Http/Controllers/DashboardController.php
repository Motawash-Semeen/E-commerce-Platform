<?php

namespace App\Modules\Owner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // The owner's shop always comes from the logged-in user, never from the request.
        $shop = $user->shop()->withCount([
            'listings',
            'listings as active_listings_count' => fn ($query) => $query->where('is_active', true),
        ])->first();

        return Inertia::render('Owner/Dashboard', [
            'shop' => $shop?->only(['name', 'slug', 'is_active', 'listings_count', 'active_listings_count']),
            'myProductsCount' => Product::where('created_by', $user->id)->count(),
        ]);
    }
}
