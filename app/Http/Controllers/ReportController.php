<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Organization;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockOrder;
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
