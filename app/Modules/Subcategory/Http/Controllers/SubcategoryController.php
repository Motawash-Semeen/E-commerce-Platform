<?php

namespace App\Modules\Subcategory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subcategory\Http\Requests\StoreSubcategoryRequest;
use App\Modules\Subcategory\Http\Requests\UpdateSubcategoryRequest;
use App\Modules\Category\Models\Category;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = Subcategory::with('category')   // one query for all parents (no N+1)
            ->withCount('products')
            ->orderBy('name')
            ->orderBy('id')  // tie-breaker: the same name can exist in two categories
            ->paginate(10);

        return view('Subcategory::index', compact('subcategories'));
    }

    public function create(): View
    {
        return view('Subcategory::create', [
            'subcategory' => new Subcategory,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(StoreSubcategoryRequest $request): RedirectResponse
    {
        $subcategory = Subcategory::create($request->validated());

        return redirect()->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" created.");
    }

    public function edit(Subcategory $subcategory): View
    {
        return view('Subcategory::edit', [
            'subcategory' => $subcategory,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): RedirectResponse
    {
        DB::transaction(function () use ($request, $subcategory) {
            $subcategory->update($request->validated());

            // Products store category_id too; keep them in step if the subcategory moved.
            if ($subcategory->wasChanged('category_id')) {
                $subcategory->products()->update(['category_id' => $subcategory->category_id]);
            }
        });

        return redirect()->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" updated.");
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $count = $subcategory->products()->count();

        if ($count > 0) {
            return back()->with('error', sprintf(
                'Cannot delete "%s": it still has %d %s. Delete or move them first.',
                $subcategory->name,
                $count,
                Str::plural('product', $count),
            ));
        }

        try {
            $subcategory->delete();
        } catch (QueryException $e) {
            // Fallback: a product was added between the check and the delete.
            return back()->with('error', "Cannot delete \"{$subcategory->name}\" because products still use it.");
        }

        return redirect()->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" deleted.");
    }

    /**
     * JSON for the product form's dependent dropdown: [{id, name}, ...].
     */
    public function forCategory(Category $category): JsonResponse
    {
        return response()->json(
            $category->subcategories()->orderBy('name')->get(['id', 'name'])
        );
    }

    /**
     * Category id => name pairs for the <select>.
     */
    private function categoryOptions(): Collection
    {
        return Category::orderBy('name')->pluck('name', 'id');
    }
}
