<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'organization_logo' => ['nullable', 'file', 'image', 'max:4096'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [], [
            'items.*.description' => 'item description',
            'items.*.quantity' => 'quantity',
            'items.*.unit_price' => 'unit price',
        ]);

        if ($request->hasFile('organization_logo')) {
            $file = $request->file('organization_logo');
            $contents = file_get_contents($file->getRealPath());

            $png = $this->removeNearWhiteBackgroundToPng($contents);
            $filename = 'org-' . $organization->id . '-' . Str::random(10) . '.png';
            $storagePath = 'org-logos/' . $filename;
            Storage::disk('public')->put($storagePath, $png);

            $organization->logo_path = 'storage/' . $storagePath;
            $organization->save();
        }

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

    public function pdf(Request $request, Invoice $invoice): Response|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $invoice->organization_id !== $organization->id) {
            abort(404);
        }
        $invoice->load(['client', 'items', 'createdByUser']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'organization' => $organization,
        ]);

        $filename = 'invoice-' . $invoice->display_number . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    private function removeNearWhiteBackgroundToPng(string $imageBytes): string
    {
        $img = @imagecreatefromstring($imageBytes);
        if (! $img) {
            return $imageBytes;
        }

        $w = imagesx($img);
        $h = imagesy($img);

        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefill($out, 0, 0, $transparent);

        $threshold = 242;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                if ($r >= $threshold && $g >= $threshold && $b >= $threshold) {
                    imagesetpixel($out, $x, $y, $transparent);
                    continue;
                }

                $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
                imagesetpixel($out, $x, $y, $color);
            }
        }

        ob_start();
        imagepng($out);
        $png = ob_get_clean();

        imagedestroy($img);
        imagedestroy($out);

        return $png ?: $imageBytes;
    }
}
