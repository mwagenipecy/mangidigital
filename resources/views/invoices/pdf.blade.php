<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->display_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 700px; margin: 24px auto; padding: 0 20px; color: #111; font-size: 13px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .invoice-number { font-weight: 700; color: #1e3a44; font-size: 1.1rem; }
        .two-cols { display: flex; gap: 40px; margin-bottom: 24px; }
        .col { flex: 1; }
        .label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #666; margin-bottom: 4px; }
        .value { white-space: pre-wrap; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #ddd; }
        th { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #666; }
        td.num, th.num { text-align: right; }
        .total-row { font-weight: 700; font-size: 1.05rem; border-top: 2px solid #111; }
        .signature-block { margin-top: 48px; display: flex; justify-content: space-between; gap: 40px; }
        .sig-box { flex: 1; }
        .sig-line { border-bottom: 1px solid #111; height: 40px; margin-top: 8px; }
        .sig-label { font-size: 10px; color: #666; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px dashed #999; font-size: 11px; color: #666; text-align: center; }
        @media print {
            body { margin: 0; padding: 12px; }
            .no-print { display: none !important; }
        }
        .no-print { margin-bottom: 16px; }
        .no-print a, .no-print button { display: inline-block; padding: 8px 16px; margin-right: 8px; background: #2AA5BD; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print();">Print / Save as PDF</button>
        <a href="{{ route('invoices.show', $invoice) }}">Back to invoice</a>
    </div>

    <div class="header">
        <h1>INVOICE</h1>
        <p class="invoice-number">{{ $invoice->display_number }}</p>
        <p style="margin:4px 0 0;font-size:.9rem;color:#666;">Issue date: {{ $invoice->issue_date?->format('d M Y') }}@if($invoice->due_date) · Due: {{ $invoice->due_date->format('d M Y') }}@endif</p>
        @if($invoice->isPaid())
            <p style="margin:6px 0 0;font-weight:700;color:#22c55e;">PAID</p>
        @endif
    </div>

    <div class="two-cols">
        <div class="col">
            <div class="label">Origin (From)</div>
            <div class="value">{{ $invoice->origin ?? $organization->name }}</div>
        </div>
        <div class="col">
            <div class="label">Destination (Bill to)</div>
            <div class="value">{{ $invoice->destination_name }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item / Description</th>
                <th class="num">Qty</th>
                <th class="num">Unit price (TZS)</th>
                <th class="num">Amount (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="num">{{ number_format($item->quantity, 2) }}</td>
                <td class="num">{{ number_format($item->unit_price, 0) }}</td>
                <td class="num">{{ number_format($item->amount, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">Total (TZS)</td>
                <td class="num">{{ number_format($invoice->total, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
        <p style="margin:0 0 24px;font-size:.9rem;color:#666;"><strong>Notes:</strong> {{ $invoice->notes }}</p>
    @endif

    <div class="signature-block">
        <div class="sig-box">
            <div class="sig-label">Authorized signature</div>
            <div class="sig-line"></div>
        </div>
        <div class="sig-box">
            <div class="sig-label">Issuer</div>
            <div style="margin-top:8px;font-weight:600;">{{ $invoice->issuer_name ?? $organization->name }}</div>
        </div>
    </div>

    <div class="footer">
        {{ $organization->name }} — Invoice {{ $invoice->display_number }} · Source of income
    </div>
</body>
</html>
