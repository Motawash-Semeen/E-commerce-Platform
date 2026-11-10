<?php

namespace App\Modules\ShopOwner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\ShopOwner\Http\Requests\ShopOwnerRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

/**
 * {shop_owner} only ever resolves to a shop owner (see the route binding in
 * AppServiceProvider), so admin accounts can't be edited or deleted here.
 */
class ShopOwnerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();

        $owners = User::shopOwners()
            ->with('shop:id,owner_id,name')   // shop name per row without N+1
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('name')
            ->orderBy('id')                   // tie-breaker for a stable page order
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $owner) => [
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'is_active' => $owner->is_active,
                'shop' => $owner->shop?->name,
            ]);

        return Inertia::render('Admin/ShopOwners/Index', [
            'owners' => $owners,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ShopOwners/Create');
    }

    public function store(ShopOwnerRequest $request): RedirectResponse
    {
        $owner = new User($request->safe()->only(['name', 'email']));
        $owner->password = Hash::make($request->validated('password'));
        $owner->role = User::ROLE_SHOP_OWNER;                 // always a shop owner, set explicitly
        $owner->is_active = $request->boolean('is_active');
        $owner->save();

        return redirect()->route('admin.shop-owners.index')
            ->with('success', "Shop owner \"{$owner->name}\" created.");
    }

    public function edit(User $shopOwner): Response
    {
        return Inertia::render('Admin/ShopOwners/Edit', [
            'owner' => $shopOwner->only(['id', 'name', 'email', 'is_active']),
        ]);
    }

    public function update(ShopOwnerRequest $request, User $shopOwner): RedirectResponse
    {
        $shopOwner->fill($request->safe()->only(['name', 'email']));
        $shopOwner->is_active = $request->boolean('is_active');

        // A blank password field keeps the current password.
        if ($request->filled('password')) {
            $shopOwner->password = Hash::make($request->validated('password'));
        }

        $shopOwner->save();

        return redirect()->route('admin.shop-owners.index')
            ->with('success', "Shop owner \"{$shopOwner->name}\" updated.");
    }

    public function destroy(User $shopOwner): RedirectResponse
    {
        $shop = $shopOwner->shop()->first(['id', 'name']);

        if ($shop) {
            return back()->with('error', "Cannot delete \"{$shopOwner->name}\": they still own the shop \"{$shop->name}\". Remove or reassign the shop first.");
        }

        try {
            $shopOwner->delete();
        } catch (QueryException $e) {
            // Fallback: a shop was created for this owner between the check and the delete.
            return back()->with('error', "Cannot delete \"{$shopOwner->name}\" because a shop still belongs to them.");
        }

        return redirect()->route('admin.shop-owners.index')
            ->with('success', "Shop owner \"{$shopOwner->name}\" deleted.");
    }
}
