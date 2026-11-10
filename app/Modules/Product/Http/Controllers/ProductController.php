<?php

namespace App\Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'subcategory'])  // no N+1 in the table
            ->latest()
            ->latest('id')  // tie-breaker: rows saved in the same second keep a stable page order
            ->paginate(10);

        return view('Product::index', compact('products'));
    }

    public function create(): View
    {
        return view('Product::create', $this->formData(new Product));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // store() gives the file a random name; the client's original name is never used.
        $data['image'] = $request->file('image')->store('products', 'public');

        $product = Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" created.");
    }

    public function edit(Product $product): View
    {
        return view('Product::edit', $this->formData($product));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        // An empty file input would otherwise overwrite the current image with null.
        $data = $request->safe()->except('image');
        $oldImage = null;

        if ($request->hasFile('image')) {
            $oldImage = $product->image;
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // Remove the old file only after the new one is saved and the row points to it.
        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" deleted.");
    }

    /**
     * Data for the shared form. The subcategory list is pre-filled for the
     * selected category (old input first, then the product's own value),
     * so the right option is selected without waiting for JavaScript.
     */
    private function formData(Product $product): array
    {
        $categoryId = (int) old('category_id', $product->category_id);

        return [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'subcategories' => Subcategory::where('category_id', $categoryId)
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }
}
