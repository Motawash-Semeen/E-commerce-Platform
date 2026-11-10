<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Owner pages that work on "my shop" (catalog, listings) need a shop.
 * Without one, show "You don't have a shop yet" instead of the page.
 * Use after 'role:shop_owner': ->middleware('has.shop').
 */
class EnsureOwnerHasShop
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->shop) {
            return Inertia::render('Owner/NoShop')->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}
