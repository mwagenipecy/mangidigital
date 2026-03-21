<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceProviderController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $providers = $organization->serviceProviders()->with('productCategory')->orderBy('type')->orderBy('name')->paginate(20);

        return view('service-providers.index', ['providers' => $providers]);
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $categories = $organization->productCategories()->orderBy('name')->get();

        return view('service-providers.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:international_transport,local_transport,clearance_forwarding'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($validated['product_category_id']) && ! $organization->productCategories()->where('id', $validated['product_category_id'])->exists()) {
            return back()->withErrors(['product_category_id' => __('Invalid category.')]);
        }

        $organization->serviceProviders()->create($validated);

        return redirect()->route('service-providers.index')->with('success', __('Service provider added.'));
    }

    public function edit(ServiceProvider $serviceProvider): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $serviceProvider->organization_id !== $organization->id) {
            abort(404);
        }

        $categories = $organization->productCategories()->orderBy('name')->get();

        return view('service-providers.edit', [
            'provider' => $serviceProvider,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, ServiceProvider $serviceProvider): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $serviceProvider->organization_id !== $organization->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:international_transport,local_transport,clearance_forwarding'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($validated['product_category_id']) && ! $organization->productCategories()->where('id', $validated['product_category_id'])->exists()) {
            return back()->withErrors(['product_category_id' => __('Invalid category.')]);
        }

        $serviceProvider->update($validated);

        return redirect()->route('service-providers.index')->with('success', __('Service provider updated.'));
    }

    public function destroy(ServiceProvider $serviceProvider): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $serviceProvider->organization_id !== $organization->id) {
            abort(404);
        }
        if ($serviceProvider->stockOrders()->exists()) {
            return back()->with('error', __('Cannot delete: this provider is used by stock orders.'));
        }
        $serviceProvider->delete();

        return redirect()->route('service-providers.index')->with('success', __('Service provider removed.'));
    }
}
