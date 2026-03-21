<?php

namespace App\Http\Controllers;

use App\Models\CargoShipment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CargoTrackController extends Controller
{
    /**
     * Landing-friendly form: customer enters the tracking UUID from their email.
     */
    public function form(): View
    {
        return view('pages.track-cargo');
    }

    /**
     * Validate UUID and redirect to the secret tracking page.
     */
    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flow_token' => ['required', 'string', 'uuid', function (string $attribute, mixed $value, \Closure $fail) {
                $ok = Sale::query()
                    ->where('logistics_flow_token', $value)
                    ->where('delivery_requested', true)
                    ->exists()
                    || CargoShipment::query()
                        ->where('logistics_flow_token', $value)
                        ->exists();
                if (! $ok) {
                    $fail(__('We could not find an active delivery for this tracking code.'));
                }
            }],
        ], [
            'flow_token.uuid' => __('Please enter the full tracking code you received by email.'),
        ]);

        return redirect()->route('cargo.track', ['flow_token' => $validated['flow_token']]);
    }

    /**
     * Public read-only cargo status (secret URL for customers — no login).
     */
    public function show(string $flow_token): View
    {
        $sale = Sale::query()
            ->where('logistics_flow_token', $flow_token)
            ->where('delivery_requested', true)
            ->with(['organization', 'items.product', 'deliveryServiceProvider'])
            ->first();

        if ($sale) {
            return view('cargo.track', [
                'trackType' => 'sale',
                'sale' => $sale,
                'cargo' => null,
            ]);
        }

        $cargo = CargoShipment::query()
            ->where('logistics_flow_token', $flow_token)
            ->with(['organization', 'deliveryServiceProvider'])
            ->firstOrFail();

        return view('cargo.track', [
            'trackType' => 'cargo',
            'sale' => null,
            'cargo' => $cargo,
        ]);
    }
}
