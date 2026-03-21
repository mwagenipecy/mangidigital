<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f8fa; color: #1e3a44; margin: 0; padding: 24px; }
        .card { max-width: 760px; margin: 0 auto; background: #fff; border: 1px solid #daeef3; border-radius: 12px; padding: 22px; }
        .title { font-size: 24px; font-weight: 700; color: #0b1f26; margin: 0 0 6px; }
        .ok { display: inline-block; background: rgba(34,197,94,.12); color: #16a34a; font-size: 13px; font-weight: 700; padding: 6px 10px; border-radius: 999px; margin-bottom: 14px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .item { border: 1px solid #e7f2f5; border-radius: 8px; padding: 10px 12px; }
        .label { font-size: 12px; color: #6a8e99; margin-bottom: 4px; }
        .value { font-size: 15px; font-weight: 600; color: #0b1f26; }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="title">Receipt Verified</h1>
        <span class="ok">Successfully Received</span>

        <div class="grid">
            <div class="item"><div class="label">Organization (Receiver)</div><div class="value">{{ $payment->organization?->name ?? 'Organization' }}</div></div>
            <div class="item"><div class="label">Sender</div><div class="value">{{ $payment->client?->name ?? 'Client' }}</div></div>
            <div class="item"><div class="label">Date Received</div><div class="value">{{ optional($payment->paid_at)->format('d M Y') }}</div></div>
            <div class="item"><div class="label">Receipt No</div><div class="value">PRC-{{ str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT) }}</div></div>
            <div class="item"><div class="label">This Payment</div><div class="value">TZS {{ number_format((float) $payment->amount, 0) }}</div></div>
            <div class="item"><div class="label">Payment Method</div><div class="value">{{ str($payment->payment_method ?? 'cash')->replace('_', ' ')->title() }}{{ $payment->payment_reference ? ' · '.$payment->payment_reference : '' }}</div></div>
            <div class="item"><div class="label">Plan</div><div class="value">{{ $payment->paymentPlan?->plan_name ?? 'Installment Plan' }}</div></div>
            <div class="item"><div class="label">Total Paid</div><div class="value">TZS {{ number_format($paidTotal, 0) }}</div></div>
            <div class="item"><div class="label">Balance Remaining</div><div class="value">TZS {{ number_format($remainingBalance, 0) }}</div></div>
        </div>
    </div>
</body>
</html>
