<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to manage product categories.'));
        }
        $categories = $organization->productCategories()->withCount('products')->latest()->paginate(15);

        return view('product-categories.index', ['categories' => $categories]);
    }

    public function create(): View|RedirectResponse
    {
        if (! auth()->user()->organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to add product categories.'));
        }

        return view('product-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $organization->productCategories()->create($validated);

        return redirect()->route('product-categories.index')->with('success', __('Product category added.'));
    }
}
