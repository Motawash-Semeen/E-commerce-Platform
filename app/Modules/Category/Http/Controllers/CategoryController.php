<?php

namespace App\Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Http\Requests\CategoryRequest;
use App\Modules\Category\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();

        $categories = Category::withCount(['subcategories', 'products'])
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Category $category) => $category->only(['id', 'name', 'slug', 'subcategories_count', 'products_count']));

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" created.");
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category->only(['id', 'name', 'slug']),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
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
        } catch (QueryException) {
            // A child row was added between the check and the delete (restrictOnDelete blocked it).
            return back()->with('error', "Cannot delete \"{$category->name}\" because other records still use it.");
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" deleted.");
    }
}
