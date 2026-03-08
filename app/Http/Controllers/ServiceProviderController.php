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
        $providers = $organization->serviceProviders()->orderBy('type')->orderBy('name')->paginate(20);

        return view('service-providers.index', ['providers' => $providers]);
    }

    public function create(): View|RedirectResponse
    {
        if (! auth()->user()->organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        return view('service-providers.create');
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
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $organization->serviceProviders()->create($validated);

        return redirect()->route('service-providers.index')->with('success', __('Service provider added.'));
    }

    public function edit(ServiceProvider $serviceProvider): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $serviceProvider->organization_id !== $organization->id) {
            abort(404);
        }

        return view('service-providers.edit', ['provider' => $serviceProvider]);
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
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

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
