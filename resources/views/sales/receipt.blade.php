<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $sale->receipt_number ?? '#' . $sale->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 480px; margin: 24px auto; padding: 0 16px; color: #111; font-size: 14px; }
        .receipt-header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 16px; margin-bottom: 16px; }
        .receipt-header h1 { margin: 0; font-size: 1.5rem; }
        .receipt-number { font-weight: 700; color: #0d9488; font-size: 1.1rem; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; }
        .row.total { border-top: 2px solid #111; margin-top: 8px; padding-top: 12px; font-weight: 700; font-size: 1.1rem; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { text-align: left; padding: 8px 4px; border-bottom: 1px solid #eee; }
        th { font-size: 0.75rem; color: #666; text-transform: uppercase; }
        td.num { text-align: right; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 2px dashed #ccc; font-size: 0.85rem; color: #666; text-align: center; }
        @media print {
            body { margin: 0; padding: 12px; }
            .no-print { display: none !important; }
        }
        .no-print { margin-bottom: 16px; }
        .no-print a, .no-print button { display: inline-block; padding: 8px 16px; margin-right: 8px; background: #0d9488; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .no-print a:hover, .no-print button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print();">Print receipt</button>
        <a href="{{ route('sales.show', $sale) }}">Back to sale</a>
    </div>

    <div class="receipt-header">
        <h1>Sale Receipt</h1>
        <p class="receipt-number">{{ $sale->receipt_number ?? 'REC-' . $sale->id }}</p>
        <p style="margin:4px 0 0;font-size:.9rem;color:#666;">{{ $sale->sale_date?->format('d M Y') }}</p>
    </div>

    <div class="row"><span>Client</span><span>{{ $sale->display_client_name }}</span></div>
    <div class="row"><span>Phone</span><span>{{ $sale->display_client_phone }}</span></div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th class="num">Unit price</th>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->display_product_name }}</td>
                <td>{{ number_format($item->quantity, 0) }}</td>
                <td class="num">{{ number_format($item->unit_price, 0) }}</td>
                <td class="num">{{ number_format($item->line_total, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row"><span>Subtotal (TZS)</span><span>{{ number_format($sale->subtotal, 0) }}</span></div>
    @if($sale->delivery_requested)
    <div class="row"><span>Delivery (TZS)</span><span>{{ number_format($sale->delivery_cost, 0) }}</span></div>
    @if($sale->deliveryServiceProvider)
    <div class="row"><span>Transport</span><span>{{ $sale->deliveryServiceProvider->name }}</span></div>
    @endif
    @endif
    <div class="row total"><span>Total (TZS)</span><span>{{ number_format($sale->total, 0) }}</span></div>

    @if($sale->notes)
    <p style="margin-top:16px;font-size:.9rem;color:#666;">Note: {{ $sale->notes }}</p>
    @endif

    <div class="footer">
        Thank you for your business.
    </div>

    <script>
    window.onload = function() {
        if (window.location.search.indexOf('print=1') !== -1) window.print();
    };
    </script>
</body>
</html>
