<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to manage stores.'));
        }
        $stores = $organization->stores()->latest()->paginate(10);

        return view('stores.index', ['stores' => $stores]);
    }

    public function create(): View|RedirectResponse
    {
        if (! auth()->user()->organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to register a store.'));
        }

        return view('stores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to register a store.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $organization->stores()->create($validated);

        return redirect()->route('stores.index')->with('success', __('Store registered successfully.'));
    }
}
