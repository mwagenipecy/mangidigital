<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Organization;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->organizationDashboard($user);
    }

    private function organizationDashboard($user): View|RedirectResponse
    {
        $organization = $user->organization;
        if (! $organization) {
            return view('dashboard.organization-guest', [
                'user' => $user,
            ]);
        }

        $salesTotal = $organization->sales()->sum('total');
        $salesCount = $organization->sales()->count();
        $salesThisMonth = $organization->sales()->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->sum('total');
        $salesThisMonthCount = $organization->sales()->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->count();

        $expensesTotal = $organization->expenses()->sum('amount');
        $expensesThisMonth = $organization->expenses()->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');

        $clientsCount = $organization->clients()->count();
        $inventoryCount = $organization->inventories()->where('is_out_of_stock', false)->where('quantity', '>', 0)->count();
        $stockOrdersInTransit = $organization->stockOrders()->where('status', 'in_transit')->count();
        $deliveriesPending = $organization->sales()->where('delivery_requested', true)
            ->where(function ($q) {
                $q->whereNull('delivery_status')
                    ->orWhere('delivery_status', '!=', Sale::DELIVERY_STATUS_RECEIVED);
            })
            ->count();

        $recentSales = $organization->sales()->with(['client', 'items'])->latest('sale_date')->latest('id')->limit(5)->get();

        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $daySales = $organization->sales()->whereDate('sale_date', $date)->sum('total');
            $dayExpenses = $organization->expenses()->whereDate('expense_date', $date)->sum('amount');
            $last7Days->push([
                'label' => $date->format('D'),
                'date' => $date->format('Y-m-d'),
                'sales' => (float) $daySales,
                'expenses' => (float) $dayExpenses,
            ]);
        }

        $expensesByCategory = $organization->expenses()
            ->selectRaw('expense_category_id, sum(amount) as total')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->groupBy('expense_category_id')
            ->get();
        $categoryIds = $expensesByCategory->pluck('expense_category_id')->unique()->filter();
        $categories = $categoryIds->isNotEmpty() ? $organization->expenseCategories()->whereIn('id', $categoryIds)->pluck('name', 'id') : collect();
        $expensesByCategory = $expensesByCategory->map(function ($row) use ($categories) {
            $row->category_name = $categories->get($row->expense_category_id, '—');
            return $row;
        });

        return view('dashboard.organization', [
            'organization' => $organization,
            'salesTotal' => $salesTotal,
            'salesCount' => $salesCount,
            'salesThisMonth' => $salesThisMonth,
            'salesThisMonthCount' => $salesThisMonthCount,
            'expensesTotal' => $expensesTotal,
            'expensesThisMonth' => $expensesThisMonth,
            'clientsCount' => $clientsCount,
            'inventoryCount' => $inventoryCount,
            'stockOrdersInTransit' => $stockOrdersInTransit,
            'deliveriesPending' => $deliveriesPending,
            'recentSales' => $recentSales,
            'last7Days' => $last7Days,
            'expensesByCategory' => $expensesByCategory,
        ]);
    }

    private function adminDashboard(): View
    {
        $incomeTotal = Income::sum('amount');
        $incomeThisMonth = Income::whereMonth('recorded_at', now()->month)->whereYear('recorded_at', now()->year)->sum('amount');
        $incomeThisYear = Income::whereYear('recorded_at', now()->year)->sum('amount');

        $systemUsersTotal = User::where('is_admin', false)->count();
        $usersPending = User::where('is_admin', false)->where('status', User::STATUS_PENDING)->count();
        $usersApproved = User::where('is_admin', false)->where('status', User::STATUS_APPROVED)->count();
        $usersSuspended = User::where('is_admin', false)->where('status', User::STATUS_SUSPENDED)->count();
        $organizationsTotal = Organization::count();
        $usersThisMonth = User::where('is_admin', false)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $loggedInToday = (int) DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->startOfDay()->timestamp)
            ->groupBy('user_id')
            ->count();

        $recentUsers = User::with('organization')
            ->where('is_admin', false)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('dashboard.admin', [
            'incomeTotal' => $incomeTotal,
            'incomeThisMonth' => $incomeThisMonth,
            'incomeThisYear' => $incomeThisYear,
            'systemUsersTotal' => $systemUsersTotal,
            'loggedInToday' => $loggedInToday,
            'usersPending' => $usersPending,
            'usersApproved' => $usersApproved,
            'usersSuspended' => $usersSuspended,
            'organizationsTotal' => $organizationsTotal,
            'usersThisMonth' => $usersThisMonth,
            'recentUsers' => $recentUsers,
        ]);
    }
}
