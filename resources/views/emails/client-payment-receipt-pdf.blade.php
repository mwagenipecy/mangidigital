<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        @page { margin: 18px 20px; }
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #1e3a44; font-size: 12px; }
        .page {
            position: relative;
            min-height: 100%;
            border: 1px solid #d8edf2;
            background: #ffffff;
            overflow: hidden;
        }
        .watermark {
            position: absolute;
            top: 45%;
            left: 8%;
            transform: rotate(-24deg);
            font-size: 64px;
            font-weight: 700;
            color: rgba(42, 165, 189, 0.08);
            letter-spacing: 3px;
            z-index: 0;
            white-space: nowrap;
        }
        .content { position: relative; z-index: 1; }
        .header {
            background: #2AA5BD;
            color: #ffffff;
            padding: 18px 20px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left, .header-right { vertical-align: top; }
        .header-right { text-align: right; }
        .logo-box {
            display: inline-block;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.35);
            text-align: center;
            line-height: 36px;
            font-size: 13px;
            font-weight: 700;
            margin-right: 8px;
            vertical-align: middle;
        }
        .org-name { display: inline-block; font-size: 18px; font-weight: 700; vertical-align: middle; }
        .org-address { font-size: 11px; margin-top: 7px; color: rgba(255,255,255,0.9); }
        .id-label { font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.8; }
        .id-value { font-size: 17px; font-weight: 700; margin-top: 2px; }
        .date-line { font-size: 11px; opacity: 0.9; margin-top: 4px; }
        .section { padding: 14px 20px; border-bottom: 1px solid #e8f2f5; }
        .status {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            color: #1a8f4f;
            background: rgba(26,143,79,0.11);
            border: 1px solid rgba(26,143,79,0.26);
            border-radius: 999px;
            padding: 4px 10px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #6a8e99;
            margin-bottom: 8px;
        }
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col td { width: 50%; vertical-align: top; padding-right: 8px; }
        .meta-label { font-size: 10px; color: #6a8e99; margin-bottom: 2px; }
        .meta-value { font-size: 13px; color: #0b1f26; font-weight: 700; margin-bottom: 6px; }
        .summary-table { width: 100%; border-collapse: collapse; border: 1px solid #d8edf2; }
        .summary-table td { padding: 9px 10px; border: 1px solid #e8f2f5; }
        .summary-label { background: #f5fbfc; font-weight: 600; width: 45%; }
        .summary-value { text-align: right; font-weight: 700; color: #0b1f26; }
        .method-box {
            border: 1px solid #d8edf2;
            border-radius: 8px;
            background: #f5fbfc;
            padding: 10px;
        }
        .method-title { font-size: 10px; color: #6a8e99; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 3px; }
        .method-value { font-size: 13px; color: #0b1f26; font-weight: 700; }
        .verify-table { width: 100%; border-collapse: collapse; }
        .verify-left, .verify-right { vertical-align: top; }
        .verify-right { text-align: right; }
        .qr-note { font-size: 10px; color: #6a8e99; margin-top: 4px; }
        .footer {
            padding: 12px 20px;
            background: #f5fbfc;
            font-size: 10px;
            color: #6a8e99;
            border-top: 1px solid #d8edf2;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="watermark">MANGI DIGITAL</div>
        <div class="content">
            @php
                $orgName = $payment->organization?->name ?? 'Mangi Digital';
                $logoUrl = $payment->organization?->logo_path ? public_path($payment->organization->logo_path) : null;
                $orgAddress = $payment->organization?->address ?: 'Address not set';
                $orgInitials = collect(explode(' ', trim($orgName)))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                $orgInitials = strtoupper($orgInitials ?: 'MD');
            @endphp

            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-left">
                        @if($logoUrl && file_exists($logoUrl))
                            <img src="{{ $logoUrl }}" alt="Organization Logo" style="width:34px;height:34px;border-radius:7px;vertical-align:top;display:inline-block;border:1px solid rgba(255,255,255,.38);margin-right:8px;">
                        @else
                            <span class="logo-box">{{ $orgInitials }}</span>
                        @endif
                        <span class="org-name">{{ $orgName }}</span>
                        <div class="org-address">Receiver Address: {{ $orgAddress }}</div>
                        </td>
                        <td class="header-right">
                            <div class="id-label">Receipt No</div>
                            <div class="id-value">PRC-{{ str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT) }}</div>
                            <div class="date-line">{{ now()->format('d M Y H:i') }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <span class="status">Successfully Received</span>
            </div>

            <div class="section">
                <div class="section-title">Parties</div>
                <table class="two-col">
                    <tr>
                        <td>
                            <div class="meta-label">Receiver Organization</div>
                            <div class="meta-value">{{ $payment->organization?->name ?? 'Organization' }}</div>
                            <div class="meta-label">Receiver Address</div>
                            <div class="meta-value">{{ $payment->organization?->address ?: 'Address not set' }}</div>
                        </td>
                        <td>
                            <div class="meta-label">Sender</div>
                            <div class="meta-value">{{ $payment->client?->name ?? 'Client' }}</div>
                            <div class="meta-label">Date Received</div>
                            <div class="meta-value">{{ optional($payment->paid_at)->format('d M Y') }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Payment Summary</div>
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Plan</td>
                        <td class="summary-value">{{ $payment->paymentPlan?->plan_name ?? 'Installment Plan' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">This Payment</td>
                        <td class="summary-value">TZS {{ number_format((float) $payment->amount, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Total Paid So Far</td>
                        <td class="summary-value">TZS {{ number_format($paidTotal, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Plan Total</td>
                        <td class="summary-value">TZS {{ number_format($goalAmount, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Balance Remaining</td>
                        <td class="summary-value">TZS {{ number_format($remainingBalance, 0) }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Payment Method And Verification</div>
                <table class="verify-table">
                    <tr>
                        <td class="verify-left">
                            <div class="method-box">
                                <div class="method-title">Method</div>
                                <div class="method-value">
                                    {{ str($payment->payment_method ?? 'cash')->replace('_', ' ')->title() }}
                                    @if($payment->payment_reference)
                                        · Ref: {{ $payment->payment_reference }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="verify-right">
                            {!! $qrSvg !!}
                            <div class="qr-note">Scan QR to verify authenticity</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                Generated by Mangi Digital · Receipt Validation via QR Code Only
            </div>
        </div>
    </div> 
</body>
</html>
