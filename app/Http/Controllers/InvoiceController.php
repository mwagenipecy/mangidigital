<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $month = $request->input('month', '');
        $monthCarbon = $month ? \Carbon\Carbon::parse($month . '-01') : now();

        $baseQuery = $organization->invoices();

        $totalInvoices = (clone $baseQuery)->count();
        $monthCount = $month
            ? (clone $baseQuery)->whereYear('issue_date', $monthCarbon->year)->whereMonth('issue_date', $monthCarbon->month)->count()
            : $totalInvoices;
        $monthTotal = $month
            ? (clone $baseQuery)->whereYear('issue_date', $monthCarbon->year)->whereMonth('issue_date', $monthCarbon->month)->sum('total')
            : (clone $baseQuery)->sum('total');
        $paidCount = (clone $baseQuery)->where('status', Invoice::STATUS_PAID)->count();
        $unpaidCount = (clone $baseQuery)->whereIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_SENT])->count();

        $invoices = $organization->invoices()
            ->with(['client', 'items'])
            ->when($month !== '', function ($q) use ($monthCarbon) {
                $q->whereYear('issue_date', $monthCarbon->year)
                    ->whereMonth('issue_date', $monthCarbon->month);
            })
            ->latest('issue_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $monthsAvailable = $organization->invoices()
            ->orderByDesc('issue_date')
            ->get()
            ->pluck('issue_date')
            ->map(fn ($d) => $d->format('Y-m'))
            ->unique()
            ->values();
        $currentMonth = now()->format('Y-m');
        if ($monthsAvailable->isEmpty()) {
            $monthsAvailable = collect([$currentMonth]);
        } elseif (! $monthsAvailable->contains($currentMonth)) {
            $monthsAvailable = $monthsAvailable->prepend($currentMonth)->values();
        }

        return view('invoices.index', [
            'invoices' => $invoices,
            'month' => $month,
            'monthCarbon' => $monthCarbon,
            'totalInvoices' => $totalInvoices,
            'monthCount' => $monthCount,
            'monthTotal' => $monthTotal,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
            'monthsAvailable' => $monthsAvailable,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $clients = $organization->clients()->orderBy('name')->get();

        return view('invoices.create', [
            'organization' => $organization,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'origin' => ['nullable', 'string', 'max:2000'],
            'destination' => ['nullable', 'string', 'max:2000'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'issuer_name' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [], [
            'items.*.description' => 'item description',
            'items.*.quantity' => 'quantity',
            'items.*.unit_price' => 'unit price',
        ]);

        $nextSeq = $organization->invoices()->count() + 1;
        $invoiceNumber = 'INV-' . str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);

        $subtotal = 0;
        foreach ($validated['items'] as $i => $item) {
            $amount = $item['quantity'] * $item['unit_price'];
            $subtotal += $amount;
        }

        $destination = $validated['destination'];
        if (! $destination && $validated['client_id']) {
            $client = Client::find($validated['client_id']);
            if ($client) {
                $destination = implode("\n", array_filter([$client->name, $client->address, $client->phone, $client->email]));
            }
        }
        $invoice = $organization->invoices()->create([
            'client_id' => $validated['client_id'] ?: null,
            'invoice_number' => $invoiceNumber,
            'origin' => $validated['origin'] ?: $organization->name,
            'destination' => $destination,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => Invoice::STATUS_DRAFT,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
            'issuer_name' => $validated['issuer_name'] ?? auth()->user()->name,
        ]);

        foreach ($validated['items'] as $i => $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'sort' => $i,
            ]);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', __('Invoice created.'));
    }

    public function show(Invoice $invoice): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $invoice->organization_id !== $organization->id) {
            abort(404);
        }
        $invoice->load(['client', 'items', 'createdByUser']);

        return view('invoices.show', ['invoice' => $invoice]);
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $invoice->organization_id !== $organization->id) {
            abort(404);
        }
        $invoice->update(['status' => Invoice::STATUS_PAID, 'paid_at' => now()]);

        return back()->with('success', __('Invoice marked as paid.'));
    }

    public function markUnpaid(Invoice $invoice): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $invoice->organization_id !== $organization->id) {
            abort(404);
        }
        $invoice->update(['status' => Invoice::STATUS_SENT, 'paid_at' => null]);

        return back()->with('success', __('Invoice marked as unpaid.'));
    }

    public function pdf(Invoice $invoice): Response|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $invoice->organization_id !== $organization->id) {
            abort(404);
        }
        $invoice->load(['client', 'items', 'createdByUser']);

        $html = view('invoices.pdf', [
            'invoice' => $invoice,
            'organization' => $organization,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="invoice-' . $invoice->display_number . '.html"',
        ]);
    }
}
