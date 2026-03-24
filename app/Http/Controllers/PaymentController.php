<?php

namespace App\Http\Controllers;

use App\Jobs\SendClientPaymentRecordedEmailJob;
use App\Jobs\SendClientPaymentReminderEmailJob;
use App\Models\ClientInstallmentPayment;
use App\Models\ClientPaymentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $status = (string) $request->input('status', 'all');
        $search = trim((string) $request->input('search', ''));
        $year = $request->input('year');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $paidSubquery = '(select coalesce(sum(cip.amount),0) from client_installment_payments cip where cip.client_payment_plan_id = client_payment_plans.id)';

        $plansQuery = $organization->clientPaymentPlans()
            ->with(['client'])
            ->withSum('installments', 'amount')
            ->orderByDesc('created_at');

        if ($search !== '') {
            $plansQuery->whereHas('client', function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%');
            });
        }

        if ($year) {
            $plansQuery->whereYear('started_at', (int) $year);
        }

        if ($dateFrom) {
            $plansQuery->whereDate('started_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $plansQuery->whereDate('started_at', '<=', $dateTo);
        }

        if ($status === 'completed') {
            $plansQuery->where(function ($query) use ($paidSubquery): void {
                $query->where('status', 'closed')
                    ->orWhereRaw($paidSubquery.' >= client_payment_plans.goal_amount');
            });
        } elseif ($status === 'over_50') {
            $plansQuery
                ->where('status', 'open')
                ->whereRaw($paidSubquery.' >= (client_payment_plans.goal_amount * 0.5)')
                ->whereRaw($paidSubquery.' < client_payment_plans.goal_amount');
        } elseif ($status === 'in_progress') {
            $plansQuery
                ->where('status', 'open')
                ->whereRaw($paidSubquery.' < (client_payment_plans.goal_amount * 0.5)');
        }

        $summaryPlans = (clone $plansQuery)->get(['id', 'goal_amount', 'status']);
        $plans = $plansQuery->paginate(20)->withQueryString();

        $clients = $organization->clients()->orderBy('name')->get(['id', 'name', 'phone', 'email']);

        $metrics = [
            'active' => 0,
            'over_half' => 0,
            'completed' => 0,
        ];

        foreach ($plans as $plan) {
            $goal = (float) $plan->goal_amount;
            $paid = (float) ($plan->installments_sum_amount ?? 0);
            $progress = $goal > 0 ? ($paid / $goal) * 100 : 0;

            if ($plan->status === 'closed' || $progress >= 100) {
                $metrics['completed']++;
                continue;
            }

            if ($progress >= 50) {
                $metrics['over_half']++;
                continue;
            }

            $metrics['active']++;
        }

        $summary = [
            'total_to_be_paid' => 0.0,
            'total_paid' => 0.0,
            'total_pending' => 0.0,
        ];

        foreach ($summaryPlans as $plan) {
            $goal = (float) $plan->goal_amount;
            $paid = (float) ($plan->installments_sum_amount ?? 0);
            $pending = max(0, $goal - $paid);

            $summary['total_to_be_paid'] += $goal;
            $summary['total_paid'] += $paid;
            $summary['total_pending'] += $pending;
        }

        return view('payments.index', [
            'plans' => $plans,
            'clients' => $clients,
            'metrics' => $metrics,
            'summary' => $summary,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'year' => $year ? (int) $year : null,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function storePlan(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'client_id' => ['required', 'integer'],
            'plan_name' => ['required', 'string', 'max:255'],
            'goal_amount' => ['required', 'numeric', 'min:1'],
            'started_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $client = $organization->clients()->whereKey($validated['client_id'])->first();
        if (! $client) {
            return redirect()->route('payments.index')->with('error', __('Invalid client selected.'));
        }

        $organization->clientPaymentPlans()->create([
            'client_id' => $client->id,
            'plan_name' => $validated['plan_name'],
            'goal_amount' => $validated['goal_amount'],
            'status' => 'open',
            'started_at' => $validated['started_at'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('payments.index')->with('success', __('Client payment plan created.'));
    }

    public function show(ClientPaymentPlan $plan): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $plan->organization_id !== $organization->id) {
            return redirect()->route('payments.index')->with('error', __('Payment plan not found.'));
        }

        $plan->load(['client', 'installments.recordedBy']);
        $paid = (float) $plan->installments()->sum('amount');
        $goal = (float) $plan->goal_amount;
        $remaining = max(0, $goal - $paid);
        $progress = $goal > 0 ? min(100, ($paid / $goal) * 100) : 0;

        $transactions = $plan->installments()
            ->with('recordedBy')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('payments.show', [
            'plan' => $plan,
            'transactions' => $transactions,
            'paid' => $paid,
            'goal' => $goal,
            'remaining' => $remaining,
            'progress' => $progress,
        ]);
    }

    public function storeInstallment(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'plan_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:cash,bank,mobile_wallet'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $plan = $organization->clientPaymentPlans()
            ->with(['client'])
            ->whereKey($validated['plan_id'])
            ->first();

        if (! $plan) {
            return redirect()->route('payments.index')->with('error', __('Payment plan not found.'));
        }

        DB::transaction(function () use ($organization, $plan, $validated): void {
            $payment = ClientInstallmentPayment::create([
                'organization_id' => $organization->id,
                'client_payment_plan_id' => $plan->id,
                'client_id' => $plan->client_id,
                'recorded_by_user_id' => auth()->id(),
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'paid_at' => $validated['paid_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $paid = (float) $plan->installments()->sum('amount');
            if ($paid >= (float) $plan->goal_amount) {
                $plan->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                ]);
            }

            SendClientPaymentRecordedEmailJob::dispatch($payment->id);
        });

        return redirect()->route('payments.index')->with('success', __('Installment recorded successfully.'));
    }

    public function sendReminder(ClientPaymentPlan $plan): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $plan->organization_id !== $organization->id) {
            return redirect()->route('payments.index')->with('error', __('Payment plan not found.'));
        }

        $plan->update(['last_reminded_at' => now()]);
        SendClientPaymentReminderEmailJob::dispatch($plan->id);

        return redirect()->route('payments.index')->with('success', __('Reminder queued and will be emailed shortly.'));
    }

    public function verifyReceipt(Request $request, ClientInstallmentPayment $payment): View
    {
        $payment->load(['organization', 'client', 'paymentPlan']);

        $paidTotal = (float) $payment->paymentPlan?->installments()->sum('amount');
        $goalAmount = (float) ($payment->paymentPlan?->goal_amount ?? 0);
        $remainingBalance = max(0, $goalAmount - $paidTotal);

        return view('payments.receipt-verify', [
            'payment' => $payment,
            'paidTotal' => $paidTotal,
            'goalAmount' => $goalAmount,
            'remainingBalance' => $remainingBalance,
        ]);
    }
}
