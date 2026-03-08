<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $categories = $organization->expenseCategories()->orderBy('name')->get();

        return view('expense-categories.index', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $organization->expenseCategories()->create($validated);

        return redirect()->route('expense-categories.index')->with('success', __('Expense category added.'));
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $expenseCategory->organization_id !== $organization->id) {
            abort(404);
        }
        if ($expenseCategory->expenses()->exists()) {
            return back()->with('error', __('Cannot delete: category has expenses.'));
        }
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', __('Category removed.'));
    }
}
