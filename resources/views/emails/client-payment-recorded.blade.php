<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1e3a44; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Payment Received</h2>
    <p>Hello {{ $payment->client?->name ?? 'Client' }},</p>
    <p>We have recorded your installment payment successfully.</p>

    <div style="background: #f5fbfc; border: 1px solid #daeef3; border-radius: 8px; padding: 12px 14px;">
        <p style="margin: 0 0 4px;"><strong>Plan:</strong> {{ $payment->paymentPlan?->plan_name }}</p>
        <p style="margin: 0 0 4px;"><strong>Amount Paid:</strong> TZS {{ number_format((float) $payment->amount, 0) }}</p>
        <p style="margin: 0 0 4px;"><strong>Method:</strong> {{ str($payment->payment_method ?? 'cash')->replace('_', ' ')->title() }}{{ $payment->payment_reference ? ' · '.$payment->payment_reference : '' }}</p>
        <p style="margin: 0 0 4px;"><strong>Total Paid So Far:</strong> TZS {{ number_format($paidTotal, 0) }}</p>
        <p style="margin: 0 0 4px;"><strong>Plan Total:</strong> TZS {{ number_format($goalAmount, 0) }}</p>
        <p style="margin: 0;"><strong>Balance Remaining:</strong> TZS {{ number_format($remainingBalance, 0) }}</p>
    </div>

    <div style="margin-top: 10px; background: #ffffff; border: 1px dashed #daeef3; border-radius: 8px; padding: 10px 12px;">
        <p style="margin: 0;"><strong>Date:</strong> {{ optional($payment->paid_at)->format('d M Y') }}</p>
    </div>

    <p style="margin-top: 14px;">Thank you for your payment.</p>
    <p style="margin-top: 4px;">Your PDF receipt is attached to this email. Use the QR code in the receipt to validate authenticity.</p>
</body>
</html>
