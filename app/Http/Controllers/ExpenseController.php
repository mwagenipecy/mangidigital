<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $query = $organization->expenses()->with(['expenseCategory', 'createdByUser'])->latest('expense_date')->latest('id');
        $range = $this->resolveDateRange($request);

        $query->whereBetween('expense_date', [$range['from'], $range['to']]);

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->category);
        }

        $expenses = $query->paginate(20);
        $expenses->appends($request->query());
        $categories = $organization->expenseCategories()->orderBy('name')->get();

        $statsBaseQuery = $organization->expenses()
            ->whereBetween('expense_date', [$range['from'], $range['to']]);

        if ($request->filled('category')) {
            $statsBaseQuery->where('expense_category_id', $request->category);
        }

        $totalExpense = (float) (clone $statsBaseQuery)->sum('amount');
        $expenseCount = (clone $statsBaseQuery)->count();
        $avgExpense = $expenseCount > 0 ? ($totalExpense / $expenseCount) : 0;

        $highestExpenseDay = (clone $statsBaseQuery)
            ->selectRaw('expense_date, SUM(amount) as total_day')
            ->groupBy('expense_date')
            ->orderByDesc('total_day')
            ->first();

        return view('expenses.index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'range' => $range,
            'stats' => [
                'total_expense' => $totalExpense,
                'expense_count' => $expenseCount,
                'avg_expense' => $avgExpense,
                'highest_day' => $highestExpenseDay?->expense_date,
                'highest_day_total' => (float) ($highestExpenseDay?->total_day ?? 0),
            ],
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $today = CarbonImmutable::today();

        if ($request->filled('month')) {
            $monthDate = CarbonImmutable::parse($request->input('month').'-01');
            $from = $monthDate->startOfMonth();
            $to = $monthDate->endOfMonth();

            return [
                'month' => $monthDate->format('Y-m'),
                'from' => $from,
                'to' => $to,
                'from_display' => $from->format('d M Y'),
                'to_display' => $to->format('d M Y'),
            ];
        }

        $from = $request->filled('from')
            ? CarbonImmutable::parse($request->input('from'))->startOfDay()
            : $today->startOfMonth();

        $to = $request->filled('to')
            ? CarbonImmutable::parse($request->input('to'))->endOfDay()
            : $today->endOfDay();

        return [
            'month' => null,
            'from' => $from,
            'to' => $to,
            'from_display' => $from->format('d M Y'),
            'to_display' => $to->format('d M Y'),
        ];
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $categories = $organization->expenseCategories()->orderBy('name')->get();

        if ($categories->isEmpty()) {
            return redirect()->route('expense-categories.index')->with('error', __('Add at least one expense category first.'));
        }

        return view('expenses.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:2000'],
            'expense_date' => ['required', 'date'],
            'receipt' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:10240'],
        ]);

        if (! $organization->expenseCategories()->where('id', $validated['expense_category_id'])->exists()) {
            return back()->withErrors(['expense_category_id' => __('Invalid category.')]);
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $receiptPath = $file->store('receipts/' . $organization->id, 'local');
        }

        $organization->expenses()->create([
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'expense_date' => $validated['expense_date'],
            'receipt_path' => $receiptPath,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('expenses.index')->with('success', __('Expense recorded.'));
    }

    public function receipt(Expense $expense): StreamedResponse|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $expense->organization_id !== $organization->id) {
            abort(404);
        }
        if (! $expense->receipt_path || ! Storage::disk('local')->exists($expense->receipt_path)) {
            return redirect()->back()->with('error', __('Receipt not found.'));
        }

        return Storage::disk('local')->download(
            $expense->receipt_path,
            'receipt-expense-' . $expense->id . '.' . pathinfo($expense->receipt_path, PATHINFO_EXTENSION)
        );
    }
}
