<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1e3a44; line-height: 1.6;">
    @php
        $goal = (float) $plan->goal_amount;
        $paid = (float) ($plan->installments_sum_amount ?? 0);
        $remaining = max(0, $goal - $paid);
        $progress = $goal > 0 ? min(100, round(($paid / $goal) * 100, 1)) : 0;
    @endphp

    <h2 style="margin-bottom: 8px;">Payment Reminder</h2>
    <p>Hello {{ $plan->client?->name ?? 'Client' }},</p>
    <p>This is a gentle reminder to continue payment on your current plan.</p>

    <div style="background: #f5fbfc; border: 1px solid #daeef3; border-radius: 8px; padding: 12px 14px;">
        <p style="margin: 0 0 4px;"><strong>Plan:</strong> {{ $plan->plan_name }}</p>
        <p style="margin: 0 0 4px;"><strong>Target:</strong> TZS {{ number_format($goal, 0) }}</p>
        <p style="margin: 0 0 4px;"><strong>Paid:</strong> TZS {{ number_format($paid, 0) }}</p>
        <p style="margin: 0 0 4px;"><strong>Remaining:</strong> TZS {{ number_format($remaining, 0) }}</p>
        <p style="margin: 0;"><strong>Progress:</strong> {{ $progress }}%</p>
    </div>

    <p style="margin-top: 14px;">Please complete the remaining amount at your earliest convenience.</p>
</body>
</html>
