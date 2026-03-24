<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $clientsQuery = $organization->clients();
        $clients = (clone $clientsQuery)->orderBy('name')->paginate(20);
        $clients->appends(request()->query());

        $totalClients = (clone $clientsQuery)->count();
        $clientsWithEmail = (clone $clientsQuery)->whereNotNull('email')->where('email', '!=', '')->count();
        $newThisMonth = (clone $clientsQuery)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return view('clients.index', [
            'clients' => $clients,
            'stats' => [
                'total_clients' => $totalClients,
                'clients_with_email' => $clientsWithEmail,
                'new_this_month' => $newThisMonth,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return $request->wantsJson()
                ? response()->json(['error' => __('You need an organization.')], 422)
                : redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $client = $organization->clients()->create($validated);

        if ($request->wantsJson()) {
            return response()->json(['id' => $client->id, 'name' => $client->name, 'phone' => $client->phone]);
        }

        return redirect()->route('clients.index')->with('success', __('Client added.'));
    }

    public function apiSearch(Request $request): JsonResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return response()->json([]);
        }
        $q = $request->input('q', '');
        $clients = $organization->clients()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('phone', 'like', '%' . $q . '%');
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'phone']);

        return response()->json($clients);
    }
}
