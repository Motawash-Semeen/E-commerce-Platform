<?php

namespace App\Modules\Subcategory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Subcategory\Http\Requests\SubcategoryRequest;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SubcategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();
        $categoryId = $request->integer('category') ?: null;

        $subcategories = Subcategory::with('category:id,name')   // one query for all parents (no N+1)
            ->withCount('products')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->orderBy('id')  // tie-breaker: the same name can exist in two categories
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Subcategory $subcategory) => [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'category' => $subcategory->category->name,
                'products_count' => $subcategory->products_count,
            ]);

        return Inertia::render('Admin/Subcategories/Index', [
            'subcategories' => $subcategories,
            'categories' => $this->categoryOptions(),
            'filters' => ['search' => $search, 'category' => $categoryId],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Subcategories/Create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(SubcategoryRequest $request): RedirectResponse
    {
        $subcategory = Subcategory::create($request->validated());

        return redirect()->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" created.");
    }

    public function edit(Subcategory $subcategory): Response
    {
        return Inertia::render('Admin/Subcategories/Edit', [
            'subcategory' => $subcategory->only(['id', 'name', 'slug', 'category_id']),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(SubcategoryRequest $request, Subcategory $subcategory): RedirectResponse
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
        } catch (QueryException) {
            // A product was added between the check and the delete.
            return back()->with('error', "Cannot delete \"{$subcategory->name}\" because products still use it.");
        }

        return redirect()->route('admin.subcategories.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" deleted.");
    }

    /**
     * [{id, name}, ...] for the category <select>.
     */
    private function categoryOptions(): Collection
    {
        return Category::orderBy('name')->get(['id', 'name']);
    }
}
