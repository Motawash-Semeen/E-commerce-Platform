<?php

namespace App\Modules\ShopOwner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\ShopOwner\Http\Requests\StoreShopOwnerRequest;
use App\Modules\ShopOwner\Http\Requests\UpdateShopOwnerRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * {shop_owner} only ever resolves to a shop owner (see the route binding in
 * AppServiceProvider), so admin accounts can't be edited or deleted here.
 */
class ShopOwnerController extends Controller
{
    public function index(): View
    {
        $owners = User::shopOwners()
            ->with('shop')          // shop name per row without N+1
            ->orderBy('name')
            ->orderBy('id')         // tie-breaker for a stable page order
            ->paginate(10);

        return view('ShopOwner::index', compact('owners'));
    }

    public function create(): View
    {
        return view('ShopOwner::create', ['owner' => new User]);
    }

    public function store(StoreShopOwnerRequest $request): RedirectResponse
    {
        $owner = new User($request->safe()->only(['name', 'email']));
        $owner->password = Hash::make($request->validated('password'));
        $owner->role = User::ROLE_SHOP_OWNER;                 // always a shop owner, set explicitly
        $owner->is_active = $request->boolean('is_active');
        $owner->save();

        return redirect()->route('admin.shop-owners.index')
            ->with('success', "Shop owner \"{$owner->name}\" created.");
    }

    public function edit(User $shopOwner): View
    {
        return view('ShopOwner::edit', ['owner' => $shopOwner]);
    }

    public function update(UpdateShopOwnerRequest $request, User $shopOwner): RedirectResponse
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
