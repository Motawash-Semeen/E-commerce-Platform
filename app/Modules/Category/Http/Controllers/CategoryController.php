<?php

namespace App\Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Http\Requests\StoreCategoryRequest;
use App\Modules\Category\Http\Requests\UpdateCategoryRequest;
use App\Modules\Category\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('subcategories')
            ->orderBy('name')
            ->paginate(10);

        return view('Category::index', compact('categories'));
    }

    public function create(): View
    {
        return view('Category::create', ['category' => new Category]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" created.");
    }

    public function edit(Category $category): View
    {
        return view('Category::edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->loadCount(['subcategories', 'products']);

        if ($category->subcategories_count > 0 || $category->products_count > 0) {
            return back()->with('error', sprintf(
                'Cannot delete "%s": it still has %d %s and %d %s. Delete or move them first.',
                $category->name,
                $category->subcategories_count,
                Str::plural('subcategory', $category->subcategories_count),
                $category->products_count,
                Str::plural('product', $category->products_count),
            ));
        }

        try {
            $category->delete();
        } catch (QueryException $e) {
            // Fallback: a child row was added between the check and the delete,
            // so the restrictOnDelete foreign key blocked it.
            return back()->with('error', "Cannot delete \"{$category->name}\" because other records still use it.");
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" deleted.");
    }
}
