<?php

namespace App\Modules\Shop\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Modules\Shop\Http\Requests\ShopRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin: shops CRUD. Each shop belongs to exactly one shop owner.
 */
class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();

        $shops = Shop::with('owner:id,name,email')
            ->withCount('listings')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Shop $shop) => [
                'id' => $shop->id,
                'name' => $shop->name,
                'slug' => $shop->slug,
                'logo_url' => $shop->logo_url,
                'is_active' => $shop->is_active,
                'owner' => $shop->owner->only(['name', 'email']),
                'listings_count' => $shop->listings_count,
            ]);

        return Inertia::render('Admin/Shops/Index', [
            'shops' => $shops,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Shops/Create', [
            'owners' => $this->ownerOptions(),
        ]);
    }

    public function store(ShopRequest $request): RedirectResponse
    {
        $shop = new Shop($request->safe()->only(['name', 'description']));
        $shop->owner_id = $request->validated('owner_id');   // validated: a shop owner without a shop
        $shop->is_active = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            // store() gives the file a random name; the client's file name is never used.
            $shop->logo = $request->file('logo')->store('shops', 'public');
        }

        $shop->save();

        return redirect()->route('admin.shops.index')
            ->with('success', "Shop \"{$shop->name}\" created.");
    }

    public function edit(Shop $shop): Response
    {
        return Inertia::render('Admin/Shops/Edit', [
            'shop' => [
                ...$shop->only(['id', 'name', 'slug', 'description', 'owner_id', 'is_active']),
                'logo_url' => $shop->logo_url,
            ],
            'owners' => $this->ownerOptions($shop),
        ]);
    }

    public function update(ShopRequest $request, Shop $shop): RedirectResponse
    {
        $shop->fill($request->safe()->only(['name', 'description']));
        $shop->owner_id = $request->validated('owner_id');
        $shop->is_active = $request->boolean('is_active');

        $oldLogo = null;
        if ($request->hasFile('logo')) {
            $oldLogo = $shop->logo;
            $shop->logo = $request->file('logo')->store('shops', 'public');
        }

        $shop->save();

        // Remove the old file only after the row points to the new one.
        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return redirect()->route('admin.shops.index')
            ->with('success', "Shop \"{$shop->name}\" updated.");
    }

    public function destroy(Shop $shop): RedirectResponse
    {
        // Collect the files first: the listings are removed by the cascade.
        $files = $shop->listings()->whereNotNull('thumbnail')->pluck('thumbnail')
            ->push($shop->logo)
            ->filter()
            ->all();

        $shop->delete();

        Storage::disk('public')->delete($files);

        return redirect()->route('admin.shops.index')
            ->with('success', "Shop \"{$shop->name}\" deleted.");
    }

    /**
     * Shop owners who don't have a shop yet, plus the current owner when editing.
     */
    private function ownerOptions(?Shop $shop = null): Collection
    {
        return User::shopOwners()
            ->where(fn (Builder $query) => $query
                ->whereDoesntHave('shop')
                ->when($shop, fn ($q) => $q->orWhere('id', $shop->owner_id)))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
