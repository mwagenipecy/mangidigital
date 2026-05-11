<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->display_number }}</title>
    <style>
        @page { margin: 22px 22px 34px 22px; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #0b1f26; font-size: 12px; line-height: 1.45; }
        .muted { color: #5b7680; }
        .mono { font-family: DejaVu Sans Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        .label { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .10em; color: #6a8e99; margin-bottom: 3px; }
        .value { white-space: pre-wrap; }
        .wrap { width: 100%; }
        .rule { height: 1px; background: #e2eef2; }

        .sheet { border: 0; border-radius: 0; overflow: visible; background: #fff; }
        .topbar { padding: 0 0 12px; background: transparent; color: #0b1f26; }
        .topbar-grid { width: 100%; border-collapse: collapse; }
        .topbar-left, .topbar-right { vertical-align: top; }
        .topbar-right { text-align: right; }
        .org-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .org-table td { vertical-align: middle; }
        .org-logo { width: 46px; }
        .org-text { padding-left: 10px; }
        .org-name { font-size: 16px; font-weight: 800; letter-spacing: .2px; }
        .logo-img { height: 38px; max-width: 170px; object-fit: contain; display: inline-block; }
        .logo-fallback { width: 38px; height: 38px; border-radius: 10px; background: #f6fcfd; border: 1px solid #e2eef2; display: inline-block; text-align: center; line-height: 38px; font-weight: 800; color: #2a6673; }
        .invoice-title { font-size: 18px; font-weight: 900; margin: 0; letter-spacing: .06em; }
        .invoice-number { font-weight: 900; margin-top: 6px; font-size: 12.5px; color: #0b1f26; }
        .status-pill { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 10px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; margin-top: 10px; }
        .status-paid { background: rgba(34,197,94,.18); border: 1px solid rgba(34,197,94,.35); }
        .status-unpaid { background: rgba(245,158,11,.18); border: 1px solid rgba(245,158,11,.35); }

        .content { padding: 14px 0 0; }
        .section { margin-top: 14px; }
        .two-col { width: 100%; border-collapse: collapse; margin: 0; }
        .two-col td { vertical-align: top; width: 50%; }
        .two-col .left { padding-right: 10px; }
        .two-col .right { padding-left: 10px; }
        .addr-grid { width: 100%; border-collapse: collapse; margin: 10px 0 0; }
        .addr-grid td { vertical-align: top; width: 50%; }
        .addr-grid .left { padding-right: 12px; }
        .addr-grid .right { padding-left: 12px; }
        .card-title { font-size: 11px; font-weight: 850; letter-spacing: .08em; text-transform: uppercase; color: #2a6673; margin: 0 0 6px; }

        table.items { width: 100%; border-collapse: collapse; margin: 0; }
        table.items thead th { background: #f6fcfd; font-size: 9.5px; font-weight: 900; text-transform: uppercase; letter-spacing: .10em; color: #5b7680; padding: 9px 8px; border-bottom: 1px solid #e2eef2; }
        table.items tbody td { padding: 9px 8px; border-bottom: 1px solid #eef6f8; vertical-align: top; }
        table.items tbody tr:last-child td { border-bottom: 1px solid #e2eef2; }
        td.num, th.num { text-align: right; white-space: nowrap; }
        .desc { font-weight: 650; color: #0b1f26; }
        .totals-table { width: 320px; border-collapse: collapse; margin-top: 12px; margin-left: auto; }
        .totals-table td { padding: 6px 0; }
        .totals-table .label-cell { color: #5b7680; font-weight: 700; }
        .totals-table .value-cell { text-align: right; font-weight: 900; }
        .grand { font-size: 15px; }

        .note { margin-top: 14px; }
        .signature-block { margin-top: 22px; }
        .sig-grid { width: 100%; border-collapse: collapse; }
        .sig-grid td { vertical-align: top; width: 50%; }
        .sig-grid .left { padding-right: 18px; }
        .sig-grid .right { padding-left: 18px; }
        .sig-line { border-bottom: 1px solid #0b1f26; height: 34px; margin-top: 10px; }
        .sig-label { font-size: 10px; color: #6a8e99; font-weight: 850; text-transform: uppercase; letter-spacing: .09em; }
        .footer { margin-top: 18px; padding-top: 12px; border-top: 1px dashed #cfe4ea; font-size: 11px; color: #6a8e99; text-align: center; }
        .pdf-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -18px;
            height: 28px;
            border-top: 1px dashed #cfe4ea;
            padding-top: 6px;
            font-size: 10px;
            color: #6a8e99;
        }
        .pdf-footer-table { width: 100%; border-collapse: collapse; }
        .pdf-footer-left, .pdf-footer-right { vertical-align: top; }
        .pdf-footer-right { text-align: right; }
    </style>
</head>
<body>
    @php
        $logoPath = $organization?->logo_path;
        $logoFile = $logoPath ? public_path($logoPath) : null;
        $logoUrl = $logoPath ? asset($logoPath) : null;
        $orgName = $organization?->name ?? 'Organization';
        $orgInitials = collect(explode(' ', trim($orgName)))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
        $orgInitials = strtoupper($orgInitials ?: 'ORG');
    @endphp
    <div class="wrap">
    <div class="sheet">
        <div class="topbar">
            <table class="topbar-grid">
                <tr>
                    <td class="topbar-left">
                        <table class="org-table">
                            <tr>
                                <td class="org-logo">
                                    @if($logoFile && file_exists($logoFile))
                                        <img src="{{ $logoUrl }}" alt="Organization logo" class="logo-img">
                                    @else
                                        <span class="logo-fallback">{{ $orgInitials }}</span>
                                    @endif
                                </td>
                                <td class="org-text">
                                    <div class="org-name">{{ $orgName }}</div>
                                    @if($organization?->address)
                                        <div style="font-size:11px;opacity:.92;margin-top:2px;">{{ $organization->address }}</div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="topbar-right">
                        <h1 class="invoice-title">INVOICE</h1>
                        <div class="invoice-number mono">{{ $invoice->display_number }}</div>
                        <div style="margin-top:8px;font-size:11px;">
                            <div class="muted"><span style="opacity:.85;">Issue:</span> {{ $invoice->issue_date?->format('d M Y') }}</div>
                            <div class="muted"><span style="opacity:.85;">Due:</span> {{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>
                        </div>
                        <span class="status-pill {{ $invoice->isPaid() ? 'status-paid' : 'status-unpaid' }}">
                            {{ $invoice->isPaid() ? 'PAID' : 'UNPAID' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="rule"></div>

        <div class="content">
            <div class="section">
            <table class="two-col">
                <tr>
                    <td class="left">
                        <div class="label">Amount due</div>
                        <div class="mono" style="font-weight:950;font-size:16px;">TZS {{ number_format($invoice->total, 0) }}</div>
                    </td>
                    <td class="right">
                        <div class="label">Issuer</div>
                        <div style="font-weight:800;">{{ $invoice->issuer_name ?? $orgName }}</div>
                        @if($invoice->paid_at)
                            <div class="muted" style="font-size:11px;margin-top:4px;">Paid on {{ $invoice->paid_at->format('d M Y') }}</div>
                        @endif
                    </td>
                </tr>
            </table>
            </div>

            <div class="section">
            <table class="addr-grid">
                <tr>
                    <td class="left">
                        <div class="card-title">From</div>
                        <div class="value">{{ $invoice->origin ?? $orgName }}</div>
                    </td>
                    <td class="right">
                        <div class="card-title">Bill to</div>
                        <div class="value">{{ $invoice->destination_name }}</div>
                    </td>
                </tr>
            </table>
            </div>

            <div class="section">
            <table class="items">
                <thead>
                    <tr>
                        <th style="width:52%;">Item / Description</th>
                        <th class="num" style="width:16%;">Qty</th>
                        <th class="num" style="width:16%;">Unit (TZS)</th>
                        <th class="num" style="width:16%;">Amount (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="desc">{{ $item->description }}</div>
                        </td>
                        <td class="num mono">{{ number_format($item->quantity, 2) }}</td>
                        <td class="num mono">{{ number_format($item->unit_price, 0) }}</td>
                        <td class="num mono" style="font-weight:850;">{{ number_format($item->amount, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

                <table class="totals-table">
                    <tr>
                        <td class="label-cell">Subtotal</td>
                        <td class="value-cell mono">TZS {{ number_format($invoice->subtotal ?? $invoice->total, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-top:1px dashed #e2eef2;padding-top:8px;">Total</td>
                        <td class="value-cell mono grand" style="border-top:1px dashed #e2eef2;padding-top:8px;">TZS {{ number_format($invoice->total, 0) }}</td>
                    </tr>
                </table>
            </div>

            @if($invoice->notes)
                <div class="note section">
                    <div class="label" style="margin-bottom:6px;">Notes</div>
                    <div class="value">{{ $invoice->notes }}</div>
                </div>
            @endif

            <div class="signature-block">
                <table class="sig-grid">
                    <tr>
                        <td class="left">
                            <div class="sig-label">Authorized signature</div>
                            <div class="sig-line"></div>
                        </td>
                        <td class="right">
                            <div class="sig-label">Issuer</div>
                            <div style="margin-top:8px;font-weight:850;">{{ $invoice->issuer_name ?? $orgName }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                {{ $orgName }} · Invoice <span class="mono">{{ $invoice->display_number }}</span> · Generated {{ now()->format('d M Y H:i') }}
            </div>
        </div>
    </div>
    </div>

    <div class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="pdf-footer-left">
                    {{ $orgName }}@if($organization?->address) · {{ $organization->address }}@endif
                </td>
                <td class="pdf-footer-right">
                    <span class="mono">{{ $invoice->display_number }}</span> · Page <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                $size = 9;
                $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                $x = $pdf->get_width() - 22 - $fontMetrics->get_text_width($text, $font, $size);
                $y = $pdf->get_height() - 18;
                $pdf->text($x, $y, $text, $font, $size, array(106/255, 142/255, 153/255));
            ');
        }
    </script>
</body>
</html>
