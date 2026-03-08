<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $baseQuery = $organization->sales()->where('delivery_requested', true);

        $summary = [
            'pending' => (clone $baseQuery)->where(function ($q) {
                $q->whereNull('delivery_status')->orWhere('delivery_status', Sale::DELIVERY_STATUS_PENDING);
            })->count(),
            'in_transit' => (clone $baseQuery)->where('delivery_status', Sale::DELIVERY_STATUS_IN_TRANSIT)->count(),
            'arrived' => (clone $baseQuery)->where('delivery_status', Sale::DELIVERY_STATUS_ARRIVED)->count(),
            'received' => (clone $baseQuery)->where('delivery_status', Sale::DELIVERY_STATUS_RECEIVED)->count(),
        ];
        $summary['total'] = $summary['pending'] + $summary['in_transit'] + $summary['arrived'] + $summary['received'];

        $query = $baseQuery->with(['client', 'deliveryServiceProvider', 'items.product'])
            ->latest('sale_date')
            ->latest('id');

        if ($request->filled('date')) {
            $query->whereDate('sale_date', $request->date);
        }
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('delivery_status')->orWhere('delivery_status', Sale::DELIVERY_STATUS_PENDING);
                });
            } else {
                $query->where('delivery_status', $request->status);
            }
        }

        $deliveries = $query->paginate(20);

        return view('logistics.index', [
            'deliveries' => $deliveries,
            'summary' => $summary,
        ]);
    }

    public function updateDeliveryStatus(Request $request, Sale $sale): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $sale->organization_id !== $organization->id) {
            abort(404);
        }
        if (! $sale->delivery_requested) {
            return back()->with('error', __('This sale has no delivery.'));
        }

        $validated = $request->validate([
            'delivery_status' => ['required', 'string', 'in:pending,in_transit,arrived,received'],
        ]);

        $current = $sale->delivery_status ?? Sale::DELIVERY_STATUS_PENDING;
        $next = $validated['delivery_status'];
        $allowedNext = self::allowedNextStatuses($current);
        if (! in_array($next, $allowedNext, true)) {
            return back()->with('error', __('You cannot change status from :current to :next. Only forward steps are allowed.', [
                'current' => $sale->delivery_status_label,
                'next' => $next,
            ]));
        }

        $sale->delivery_status = $validated['delivery_status'];
        if ($validated['delivery_status'] === Sale::DELIVERY_STATUS_IN_TRANSIT && ! $sale->delivery_dispatched_at) {
            $sale->delivery_dispatched_at = now();
        }
        if ($validated['delivery_status'] === Sale::DELIVERY_STATUS_ARRIVED) {
            $sale->delivery_arrived_at = $sale->delivery_arrived_at ?? now();
        }
        if ($validated['delivery_status'] === Sale::DELIVERY_STATUS_RECEIVED) {
            $sale->delivery_received_at = now();
        }
        $sale->save();

        return back()->with('success', __('Delivery status updated.'));
    }

    /** @return array<int, string> */
    private static function allowedNextStatuses(string $current): array
    {
        return match ($current) {
            Sale::DELIVERY_STATUS_PENDING => [Sale::DELIVERY_STATUS_IN_TRANSIT, Sale::DELIVERY_STATUS_ARRIVED, Sale::DELIVERY_STATUS_RECEIVED],
            Sale::DELIVERY_STATUS_IN_TRANSIT => [Sale::DELIVERY_STATUS_ARRIVED, Sale::DELIVERY_STATUS_RECEIVED],
            Sale::DELIVERY_STATUS_ARRIVED => [Sale::DELIVERY_STATUS_RECEIVED],
            Sale::DELIVERY_STATUS_RECEIVED => [],
            default => [Sale::DELIVERY_STATUS_IN_TRANSIT, Sale::DELIVERY_STATUS_ARRIVED, Sale::DELIVERY_STATUS_RECEIVED],
        };
    }
}
