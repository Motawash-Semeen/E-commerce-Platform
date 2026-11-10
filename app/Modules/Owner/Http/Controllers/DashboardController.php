<?php

namespace App\Modules\Owner\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        // The owner's shop always comes from the logged-in user, never from the request.
        $shop = $request->user()->shop;

        return Inertia::render('Owner/Dashboard', [
            'shop' => $shop?->only(['name', 'slug', 'is_active']),
        ]);
    }
}
