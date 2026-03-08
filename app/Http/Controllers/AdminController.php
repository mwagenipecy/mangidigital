<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $incomeTotal = Income::sum('amount');
        $incomeThisMonth = Income::whereMonth('recorded_at', now()->month)
            ->whereYear('recorded_at', now()->year)
            ->sum('amount');
        $incomeThisYear = Income::whereYear('recorded_at', now()->year)->sum('amount');

        $registrations = User::with('organization')
            ->where('is_admin', false)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.index', [
            'incomeTotal' => $incomeTotal,
            'incomeThisMonth' => $incomeThisMonth,
            'incomeThisYear' => $incomeThisYear,
            'registrations' => $registrations,
        ]);
    }

    public function showUser(User $user): View
    {
        $user->load(['organization', 'payments' => fn ($q) => $q->orderByDesc('recorded_at')]);
        $payments = $user->organization
            ? $user->organization->payments()->with('user:id,name')->orderByDesc('recorded_at')->get()
            : $user->payments;

        return view('admin.user', [
            'registration' => $user,
            'payments' => $payments,
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['status' => User::STATUS_APPROVED]);
        $user->organization?->update(['status' => 'active']);

        return back()->with('success', __('User approved.'));
    }

    public function suspend(User $user): RedirectResponse
    {
        $user->update(['status' => User::STATUS_SUSPENDED]);
        $user->organization?->update(['status' => 'suspended']);

        return back()->with('success', __('User suspended.'));
    }

    public function terminate(User $user): RedirectResponse
    {
        $user->update(['status' => User::STATUS_TERMINATED]);
        $user->organization?->update(['status' => 'terminated']);

        return back()->with('success', __('User terminated.'));
    }

    public function showOrganization(Organization $organization): View
    {
        $organization->loadCount('users');
        $users = $organization->users()->orderBy('name')->get();

        return view('admin.organization', [
            'organization' => $organization,
            'users' => $users,
        ]);
    }
}
