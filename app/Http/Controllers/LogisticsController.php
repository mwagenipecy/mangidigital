<?php

namespace App\Http\Controllers;

use App\Jobs\SendCargoDeliveryStatusEmailJob;
use App\Models\CargoShipment;
use App\Models\Sale;
use App\Models\ServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LogisticsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $baseSales = $organization->sales()->where('delivery_requested', true);
        $baseCargo = $organization->cargoShipments();

        $summary = [
            'pending' => $this->countStatusPending($baseSales) + $this->countStatusPendingCargo($baseCargo),
            'in_transit' => $this->countStatus($baseSales, Sale::DELIVERY_STATUS_IN_TRANSIT)
                + $this->countStatusCargo($baseCargo, CargoShipment::DELIVERY_STATUS_IN_TRANSIT),
            'arrived' => $this->countStatus($baseSales, Sale::DELIVERY_STATUS_ARRIVED)
                + $this->countStatusCargo($baseCargo, CargoShipment::DELIVERY_STATUS_ARRIVED),
            'received' => $this->countStatus($baseSales, Sale::DELIVERY_STATUS_RECEIVED)
                + $this->countStatusCargo($baseCargo, CargoShipment::DELIVERY_STATUS_RECEIVED),
        ];
        $summary['total'] = $summary['pending'] + $summary['in_transit'] + $summary['arrived'] + $summary['received'];

        $query = (clone $baseSales)->with(['client', 'deliveryServiceProvider', 'items.product'])
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

        $cargoQuery = (clone $baseCargo)->with(['deliveryServiceProvider'])->latest();
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $cargoQuery->where(function ($q) {
                    $q->whereNull('delivery_status')->orWhere('delivery_status', CargoShipment::DELIVERY_STATUS_PENDING);
                });
            } else {
                $cargoQuery->where('delivery_status', $request->status);
            }
        }
        $cargoShipments = $cargoQuery->paginate(15, ['*'], 'cargo_page');

        return view('logistics.index', [
            'deliveries' => $deliveries,
            'cargoShipments' => $cargoShipments,
            'summary' => $summary,
        ]);
    }

    public function createCargo(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $providers = $organization->serviceProviders()->orderBy('type')->orderBy('name')->get();

        return view('logistics.cargo-create', ['serviceProviders' => $providers]);
    }

    public function storeCargo(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'cargo_description' => ['nullable', 'string', 'max:5000'],
            'delivery_service_provider_id' => ['nullable', 'exists:service_providers,id'],
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($validated['delivery_service_provider_id']) &&
            ! $organization->serviceProviders()->where('id', $validated['delivery_service_provider_id'])->exists()) {
            return back()->withErrors(['delivery_service_provider_id' => __('Invalid transport.')]);
        }

        $shipment = $organization->cargoShipments()->create([
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'client_email' => $validated['client_email'] ?? null,
            'cargo_description' => $validated['cargo_description'] ?? null,
            'delivery_service_provider_id' => $validated['delivery_service_provider_id'] ?? null,
            'delivery_status' => CargoShipment::DELIVERY_STATUS_PENDING,
            'delivery_cost' => $validated['delivery_cost'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('logistics.flow', ['flow_token' => $shipment->logistics_flow_token])
            ->with('success', __('Custom cargo created. Reference: :ref', ['ref' => $shipment->reference_number]));
    }

    public function flow(string $flow_token): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $resolved = $this->resolveFlowRecord($organization, $flow_token);

        if ($resolved['type'] === 'sale') {
            $sale = $resolved['sale']->load(['client', 'deliveryServiceProvider', 'items.product']);

            return view('logistics.flow', [
                'flowType' => 'sale',
                'shipment' => $sale,
            ]);
        }

        $cargo = $resolved['cargo']->load(['deliveryServiceProvider']);

        return view('logistics.flow', [
            'flowType' => 'cargo',
            'shipment' => $cargo,
        ]);
    }

    public function updateDeliveryStatus(Request $request, string $flow_token): RedirectResponse|JsonResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            abort(404);
        }

        $resolved = $this->resolveFlowRecord($organization, $flow_token);

        if ($resolved['type'] === 'sale') {
            $model = $resolved['sale'];
            if (! $model->delivery_requested) {
                return $this->deliveryErrorResponse($request, __('This sale has no delivery.'));
            }
        } else {
            $model = $resolved['cargo'];
        }

        $allowedNext = array_keys($model->allowedNextDeliveryStatuses());
        if ($allowedNext === []) {
            return $this->deliveryErrorResponse($request, __('Delivery is already at the final stage.'));
        }

        $validator = Validator::make($request->all(), [
            'delivery_status' => ['required', 'string', Rule::in($allowedNext)],
            'delivery_pickup_office' => ['nullable', 'string', 'max:5000'],
        ]);

        $validator->after(function ($v) use ($request) {
            if ($request->input('delivery_status') === Sale::DELIVERY_STATUS_ARRIVED) {
                if (strlen(trim((string) $request->input('delivery_pickup_office', ''))) < 3) {
                    $v->errors()->add(
                        'delivery_pickup_office',
                        __('Please enter the pickup office or address where the customer can collect the cargo.')
                    );
                }
            }
        });

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        }

        $validated = $validator->validated();
        $status = $validated['delivery_status'];
        $pickup = isset($validated['delivery_pickup_office']) ? trim((string) $validated['delivery_pickup_office']) : '';

        $model->delivery_status = $status;
        if ($status === Sale::DELIVERY_STATUS_IN_TRANSIT && ! $model->delivery_dispatched_at) {
            $model->delivery_dispatched_at = now();
        }
        if ($status === Sale::DELIVERY_STATUS_ARRIVED) {
            $model->delivery_arrived_at = $model->delivery_arrived_at ?? now();
            $model->delivery_pickup_office = $pickup;
        }
        if ($status === Sale::DELIVERY_STATUS_RECEIVED) {
            $model->delivery_received_at = now();
        }
        $model->save();
        $model->refresh();

        if ($resolved['type'] === 'sale') {
            SendCargoDeliveryStatusEmailJob::dispatch(saleId: $model->id);
        } else {
            SendCargoDeliveryStatusEmailJob::dispatch(cargoShipmentId: $model->id);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Delivery status updated.'),
                'delivery_status' => $model->delivery_status,
                'delivery_status_label' => $model->delivery_status_label,
                'allowed_next' => array_keys($model->allowedNextDeliveryStatuses()),
                'delivery_pickup_office' => $model->delivery_pickup_office,
                'delivery_dispatched_at' => $model->delivery_dispatched_at?->toIso8601String(),
                'delivery_arrived_at' => $model->delivery_arrived_at?->toIso8601String(),
                'delivery_received_at' => $model->delivery_received_at?->toIso8601String(),
            ]);
        }

        return back()->with('success', __('Delivery status updated.'));
    }

    private function deliveryErrorResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('error', $message);
    }

    /**
     * @return array{type: 'sale', sale: Sale, cargo: null}|array{type: 'cargo', sale: null, cargo: CargoShipment}
     */
    private function resolveFlowRecord(\App\Models\Organization $organization, string $flow_token): array
    {
        $sale = Sale::query()
            ->where('organization_id', $organization->id)
            ->where('logistics_flow_token', $flow_token)
            ->where('delivery_requested', true)
            ->first();
        if ($sale) {
            return ['type' => 'sale', 'sale' => $sale, 'cargo' => null];
        }

        $cargo = CargoShipment::query()
            ->where('organization_id', $organization->id)
            ->where('logistics_flow_token', $flow_token)
            ->first();
        if ($cargo) {
            return ['type' => 'cargo', 'sale' => null, 'cargo' => $cargo];
        }

        abort(404);
    }

    private function countStatusPending($baseSalesQuery): int
    {
        return (clone $baseSalesQuery)->where(function ($q) {
            $q->whereNull('delivery_status')->orWhere('delivery_status', Sale::DELIVERY_STATUS_PENDING);
        })->count();
    }

    private function countStatusPendingCargo($baseCargoQuery): int
    {
        return (clone $baseCargoQuery)->where(function ($q) {
            $q->whereNull('delivery_status')->orWhere('delivery_status', CargoShipment::DELIVERY_STATUS_PENDING);
        })->count();
    }

    private function countStatus($baseSalesQuery, string $status): int
    {
        return (clone $baseSalesQuery)->where('delivery_status', $status)->count();
    }

    private function countStatusCargo($baseCargoQuery, string $status): int
    {
        return (clone $baseCargoQuery)->where('delivery_status', $status)->count();
    }
}
