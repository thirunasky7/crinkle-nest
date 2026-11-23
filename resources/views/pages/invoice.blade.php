<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bake Slip #{{ $order->order_number }} | {{ setting('site_name', 'Crinkle Nest') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif;color:#1d1c17;background:#fef9f1;margin:0;padding:32px}
        .slip{max-width:760px;margin:0 auto;background:#fff;border-radius:24px;padding:40px;box-shadow:0 4px 20px -2px rgba(61,35,20,.08)}
        h1,h2{font-family:'Playfair Display',serif;color:#250f03;margin:0}
        .muted{color:#50443f;font-size:13px}
        table{width:100%;border-collapse:collapse;margin-top:24px}
        th,td{text-align:left;padding:10px 6px;border-bottom:1px solid #f2ede5;font-size:14px}
        th{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8e4e08}
        .r{text-align:right}.tot td{border:0;padding:4px 6px}.grand td{font-weight:700;font-size:18px;color:#250f03;padding-top:12px}
        .row{display:flex;justify-content:space-between;gap:24px;flex-wrap:wrap}
        .chip{display:inline-block;padding:4px 12px;border-radius:999px;background:#ffdcc2;color:#2e1500;font-size:12px;font-weight:700}
        .btn{display:inline-block;margin-top:24px;padding:10px 22px;border-radius:999px;background:#250f03;color:#fff;text-decoration:none;font-weight:600;border:0;cursor:pointer;font-family:inherit}
        @media print{.btn{display:none}body{padding:0;background:#fff}.slip{box-shadow:none}}
    </style>
</head>
<body>
<div class="slip">
    <div class="row">
        <div>
            <h1>{{ setting('site_name', 'Crinkle Nest') }}</h1>
            <div class="muted">{{ setting('address_full', setting('address_short')) }}<br>{{ setting('phone') }} · {{ setting('email') }}</div>
            @if(setting('fssai_number'))<div class="muted">FSSAI Lic. {{ setting('fssai_number') }}</div>@endif
        </div>
        <div style="text-align:right">
            <h2>Bake Slip</h2>
            <div class="muted">#{{ $order->order_number }}<br>{{ $order->created_at->format('d M Y, g:i A') }}</div>
            <span class="chip">{{ $order->statusLabel() }}</span>
        </div>
    </div>
    <div class="row" style="margin-top:28px">
        <div><strong>Deliver to</strong><div class="muted">{{ $order->name }}<br>{{ $order->address }}<br>{{ $order->city }}<br>{{ $order->phone }}</div></div>
        <div><strong>Slot</strong><div class="muted">{{ $order->slot_label }}@if($order->custom_date)<br>{{ $order->custom_date->format('d M Y') }}@endif</div>
            <strong style="display:block;margin-top:8px">Payment</strong><div class="muted">{{ ucfirst($order->payment_method) }} · {{ ucfirst($order->payment_status) }}</div></div>
    </div>
    <table>
        <thead><tr><th>Item</th><th class="r">Price</th><th class="r">Qty</th><th class="r">Total</th></tr></thead>
        <tbody>
        @foreach($order->items as $it)
            <tr><td>{{ $it->name }}<div class="muted">{{ $it->detail }}</div></td><td class="r">{{ money($it->price) }}</td><td class="r">{{ $it->qty }}</td><td class="r">{{ money($it->total) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <table>
        <tr class="tot"><td class="r" style="width:80%">Subtotal</td><td class="r">{{ money($order->subtotal) }}</td></tr>
        <tr class="tot"><td class="r">Delivery</td><td class="r">{{ $order->delivery_fee > 0 ? money($order->delivery_fee) : 'FREE' }}</td></tr>
        @if($order->discount > 0)<tr class="tot"><td class="r">Discount ({{ $order->coupon_code }})</td><td class="r">-{{ money($order->discount) }}</td></tr>@endif
        <tr class="tot grand"><td class="r">Grand Total</td><td class="r">{{ money($order->total) }}</td></tr>
    </table>
    @if($order->gift_note)<p class="muted" style="margin-top:20px"><strong>Gift note:</strong> {{ $order->gift_note }}</p>@endif
    <button class="btn" onclick="window.print()">Print / Save as PDF</button>
</div>
</body>
</html>
