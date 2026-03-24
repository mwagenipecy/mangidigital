<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockOrder;
use App\Models\CargoShipment;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $range = $this->resolveDateRange($request);

        $report = $this->buildReport($organization, $range['from'], $range['to']);

        return view('reports.index', [
            'report' => $report,
            'range' => $range,
        ]);
    }

    // ------------------------------------------------------------------
    // Date range
    // ------------------------------------------------------------------

    private function resolveDateRange(Request $request): array
    {
        $preset = $request->input('range', 'this_month');

        $today = CarbonImmutable::today();

        switch ($preset) {
            case 'today':
                $from = $today;
                $to = $today;
                break;
            case 'this_week':
                $from = $today->startOfWeek();
                $to = $today->endOfWeek();
                break;
            case 'last_month':
                $from = $today->subMonth()->startOfMonth();
                $to = $today->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $from = $today->startOfYear();
                $to = $today;
                break;
            case 'all_time':
                $from = CarbonImmutable::parse('2020-01-01');
                $to = $today;
                break;
            case 'custom':
                $from = $request->filled('from') ? CarbonImmutable::parse($request->input('from'))->startOfDay() : $today->startOfMonth();
                $to = $request->filled('to') ? CarbonImmutable::parse($request->input('to'))->endOfDay() : $today;
                break;
            default: // this_month
                $from = $today->startOfMonth();
                $to = $today;
                break;
        }

        return [
            'preset' => $preset,
            'from' => $from,
            'to' => $to,
            'from_display' => $from->format('d M Y'),
            'to_display' => $to->format('d M Y'),
        ];
    }

    // ------------------------------------------------------------------
    // Build
    // ------------------------------------------------------------------

    private function buildReport(Organization $organization, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $orgId = $organization->id;

        // --- Revenue from sales ---
        $salesQuery = Sale::query()
            ->where('organization_id', $orgId)
            ->whereBetween('sale_date', [$from, $to]);

        $salesCount = (clone $salesQuery)->count();
        $totalRevenue = (float) (clone $salesQuery)->sum('total');
        $totalSubtotal = (float) (clone $salesQuery)->sum('subtotal');
        $totalDeliveryCost = (float) (clone $salesQuery)->where('delivery_requested', true)->sum('delivery_cost');

        // --- Cost of goods sold (COGS) ---
        // SaleItems in range, joined with inventory buying_price_per_unit
        $cogs = (float) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('inventories', function ($join) {
                $join->on('sale_items.product_id', '=', 'inventories.product_id')
                    ->on('sale_items.store_id', '=', 'inventories.store_id');
            })
            ->where('sales.organization_id', $orgId)
            ->whereBetween('sales.sale_date', [$from, $to])
            ->selectRaw('COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0) as cogs')
            ->value('cogs');

        // --- Expenses by category ---
        $expenseRows = Expense::query()
            ->where('expenses.organization_id', $orgId)
            ->whereBetween('expenses.expense_date', [$from, $to])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name as category_name')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(expenses.amount) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = (float) $expenseRows->sum('total');

        // --- Sales insights ---
        $topClientsBySales = Sale::query()
            ->where('organization_id', $orgId)
            ->whereBetween('sale_date', [$from, $to])
            ->selectRaw('COALESCE(client_name, "Walk-in") as client_label')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('SUM(total) as total_sales')
            ->groupBy('client_label')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        $highProfitSales = Sale::query()
            ->where('sales.organization_id', $orgId)
            ->whereBetween('sales.sale_date', [$from, $to])
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('inventories', function ($join) {
                $join->on('sale_items.product_id', '=', 'inventories.product_id')
                    ->on('sale_items.store_id', '=', 'inventories.store_id');
            })
            ->groupBy('sales.id', 'sales.receipt_number', 'sales.sale_date', 'sales.total')
            ->selectRaw('sales.id as sale_id')
            ->selectRaw('sales.receipt_number as receipt_number')
            ->selectRaw('sales.sale_date as sale_date')
            ->selectRaw('sales.total as total')
            ->selectRaw('COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0) as cogs')
            ->selectRaw('(sales.total - COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0)) as profit')
            ->orderByDesc('profit')
            ->limit(5)
            ->get();

        // --- Logistics report ---
        $salesDeliveryBase = Sale::query()
            ->where('organization_id', $orgId)
            ->where('delivery_requested', true)
            ->whereBetween('sale_date', [$from, $to]);

        $cargoBase = CargoShipment::query()
            ->where('organization_id', $orgId)
            ->whereBetween('created_at', [$from, $to]);

        $deliveryPending = (clone $salesDeliveryBase)->where(function ($q) {
            $q->whereNull('delivery_status')->orWhere('delivery_status', Sale::DELIVERY_STATUS_PENDING);
        })->count();
        $deliveryInTransit = (clone $salesDeliveryBase)->where('delivery_status', Sale::DELIVERY_STATUS_IN_TRANSIT)->count();
        $deliveryArrived = (clone $salesDeliveryBase)->where('delivery_status', Sale::DELIVERY_STATUS_ARRIVED)->count();
        $deliveryReceived = (clone $salesDeliveryBase)->where('delivery_status', Sale::DELIVERY_STATUS_RECEIVED)->count();

        $cargoPending = (clone $cargoBase)->where(function ($q) {
            $q->whereNull('delivery_status')->orWhere('delivery_status', CargoShipment::DELIVERY_STATUS_PENDING);
        })->count();
        $cargoInTransit = (clone $cargoBase)->where('delivery_status', CargoShipment::DELIVERY_STATUS_IN_TRANSIT)->count();
        $cargoArrived = (clone $cargoBase)->where('delivery_status', CargoShipment::DELIVERY_STATUS_ARRIVED)->count();
        $cargoReceived = (clone $cargoBase)->where('delivery_status', CargoShipment::DELIVERY_STATUS_RECEIVED)->count();

        $logisticsTotals = [
            'pending' => $deliveryPending + $cargoPending,
            'in_transit' => $deliveryInTransit + $cargoInTransit,
            'arrived' => $deliveryArrived + $cargoArrived,
            'received' => $deliveryReceived + $cargoReceived,
            'delivery_sales_count' => (clone $salesDeliveryBase)->count(),
            'cargo_shipments_count' => (clone $cargoBase)->count(),
            'delivery_sales_revenue' => (float) (clone $salesDeliveryBase)->sum('total'),
            'cargo_revenue' => (float) (clone $cargoBase)->sum('delivery_cost'),
        ];
        $logisticsTotals['total_shipments'] = $logisticsTotals['delivery_sales_count'] + $logisticsTotals['cargo_shipments_count'];

        // --- Client report ---
        $totalClients = (int) $organization->clients()->count();
        $newClients = (int) $organization->clients()->whereBetween('created_at', [$from, $to])->count();
        $activeClients = (int) Sale::query()
            ->where('organization_id', $orgId)
            ->whereBetween('sale_date', [$from, $to])
            ->whereNotNull('client_id')
            ->distinct('client_id')
            ->count('client_id');

        $topClientsByProfit = Sale::query()
            ->where('sales.organization_id', $orgId)
            ->whereBetween('sales.sale_date', [$from, $to])
            ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('inventories', function ($join) {
                $join->on('sale_items.product_id', '=', 'inventories.product_id')
                    ->on('sale_items.store_id', '=', 'inventories.store_id');
            })
            ->selectRaw('COALESCE(sales.client_name, "Walk-in") as client_label')
            ->selectRaw('COUNT(DISTINCT sales.id) as sales_count')
            ->selectRaw('SUM(sales.total) as total_sales')
            ->selectRaw('COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0) as cogs')
            ->selectRaw('(SUM(sales.total) - COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0)) as profit')
            ->groupBy('client_label')
            ->orderByDesc('profit')
            ->limit(5)
            ->get();

        // --- Invoice report ---
        $invoiceBase = Invoice::query()
            ->where('organization_id', $orgId)
            ->whereBetween('issue_date', [$from, $to]);

        $invoiceTotals = [
            'total_count' => (clone $invoiceBase)->count(),
            'draft_count' => (clone $invoiceBase)->where('status', Invoice::STATUS_DRAFT)->count(),
            'sent_count' => (clone $invoiceBase)->where('status', Invoice::STATUS_SENT)->count(),
            'paid_count' => (clone $invoiceBase)->where('status', Invoice::STATUS_PAID)->count(),
            'issued_total' => (float) (clone $invoiceBase)->sum('total'),
            'paid_total' => (float) (clone $invoiceBase)->where('status', Invoice::STATUS_PAID)->sum('total'),
        ];
        $invoiceTotals['outstanding_total'] = max(0, $invoiceTotals['issued_total'] - $invoiceTotals['paid_total']);

        $largestInvoices = (clone $invoiceBase)
            ->with('client')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // --- Inventory report ---
        $inventoryRows = Inventory::query()
            ->where('organization_id', $orgId)
            ->with(['product', 'store'])
            ->where('quantity', '>', 0)
            ->get();

        $lowStockItems = $inventoryRows
            ->filter(fn (Inventory $inv) => (float) $inv->quantity <= 5)
            ->sortBy('quantity')
            ->take(5)
            ->values();

        $topInventoryValue = $inventoryRows
            ->sortByDesc(fn (Inventory $inv) => $inv->stock_value)
            ->take(5)
            ->values();

        // --- Stock orders (capital invested in stock) ---
        $stockOrdersQuery = StockOrder::query()
            ->where('organization_id', $orgId)
            ->whereBetween('payment_date', [$from, $to]);

        $stockOrdersTotal = (float) (clone $stockOrdersQuery)->selectRaw('COALESCE(SUM(amount_paid + transport_charges + other_charges), 0) as total')->value('total');
        $stockOrdersCount = (clone $stockOrdersQuery)->count();

        // --- Current inventory snapshot (all-time — not date-filtered) ---
        $inventories = Inventory::query()
            ->where('organization_id', $orgId)
            ->where('quantity', '>', 0)
            ->get();

        $inventoryStockCost = $inventories->sum(fn (Inventory $inv) => $inv->stock_cost);
        $inventoryStockValue = $inventories->sum(fn (Inventory $inv) => $inv->stock_value);

        // --- P&L summary ---
        $grossProfit = $totalRevenue - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        // --- Monthly trend (last 6 months including current) ---
        $trend = $this->buildMonthlyTrend($orgId, 6);

        return [
            'sales_count' => $salesCount,
            'total_revenue' => $totalRevenue,
            'total_subtotal' => $totalSubtotal,
            'total_delivery_cost' => $totalDeliveryCost,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'expense_rows' => $expenseRows,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'top_clients_by_sales' => $topClientsBySales,
            'high_profit_sales' => $highProfitSales,
            'logistics_totals' => $logisticsTotals,
            'client_totals' => [
                'total_clients' => $totalClients,
                'active_clients' => $activeClients,
                'new_clients' => $newClients,
            ],
            'top_clients_by_profit' => $topClientsByProfit,
            'invoice_totals' => $invoiceTotals,
            'largest_invoices' => $largestInvoices,
            'inventory_totals' => [
                'items_in_stock' => $inventoryRows->count(),
                'low_stock_count' => $lowStockItems->count(),
                'stock_available_qty' => (float) $inventoryRows->sum(fn (Inventory $inv) => (float) $inv->quantity),
                'stock_worth' => (float) $inventoryRows->sum(fn (Inventory $inv) => (float) $inv->stock_value),
            ],
            'low_stock_items' => $lowStockItems,
            'top_inventory_value' => $topInventoryValue,
            'stock_orders_total' => $stockOrdersTotal,
            'stock_orders_count' => $stockOrdersCount,
            'inventory_stock_cost' => $inventoryStockCost,
            'inventory_stock_value' => $inventoryStockValue,
            'trend' => $trend,
        ];
    }

    // ------------------------------------------------------------------
    // Trend
    // ------------------------------------------------------------------

    private function buildMonthlyTrend(int $orgId, int $months): array
    {
        $results = [];
        $now = CarbonImmutable::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = $now->subMonths($i)->startOfMonth();
            $end = $start->endOfMonth();
            $label = $start->format('M Y');

            $revenue = (float) Sale::query()
                ->where('organization_id', $orgId)
                ->whereBetween('sale_date', [$start, $end])
                ->sum('total');

            $expense = (float) Expense::query()
                ->where('organization_id', $orgId)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $cogsMonth = (float) SaleItem::query()
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->leftJoin('inventories', function ($join) {
                    $join->on('sale_items.product_id', '=', 'inventories.product_id')
                        ->on('sale_items.store_id', '=', 'inventories.store_id');
                })
                ->where('sales.organization_id', $orgId)
                ->whereBetween('sales.sale_date', [$start, $end])
                ->selectRaw('COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0) as cogs')
                ->value('cogs');

            $net = $revenue - $cogsMonth - $expense;

            $results[] = [
                'label' => $label,
                'revenue' => $revenue,
                'cogs' => $cogsMonth,
                'expenses' => $expense,
                'net' => $net,
            ];
        }

        return $results;
    }
}
