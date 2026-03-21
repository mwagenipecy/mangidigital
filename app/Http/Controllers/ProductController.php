<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to manage products.'));
        }
        $products = $organization->products()->with('productCategory')->latest()->paginate(15);

        return view('products.index', ['products' => $products]);
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to add products.'));
        }
        $categories = $organization->productCategories()->orderBy('name')->get();

        return view('products.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        if ((int) $validated['product_category_id'] && ! $organization->productCategories()->where('id', $validated['product_category_id'])->exists()) {
            return back()->withErrors(['product_category_id' => __('Invalid category.')]);
        }

        $validated['price'] = 0;

        $organization->products()->create($validated);

        return redirect()->route('products.index')->with('success', __('Product added.'));
    }

    public function edit(Product $product): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $product->organization_id !== $organization->id) {
            return redirect()->route('products.index')->with('error', __('Product not found.'));
        }
        $categories = $organization->productCategories()->orderBy('name')->get();

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $product->organization_id !== $organization->id) {
            return redirect()->route('products.index')->with('error', __('Product not found.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        if (! $organization->productCategories()->where('id', $validated['product_category_id'])->exists()) {
            return back()->withErrors(['product_category_id' => __('Invalid category.')]);
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', __('Product updated.'));
    }
}
