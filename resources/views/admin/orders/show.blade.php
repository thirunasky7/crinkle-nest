@extends('admin.layout')

@section('title', 'Order #'.$order->order_number)

@section('content')
<div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
    <a href="{{ route('admin.orders.index') }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">arrow_back</span>Orders</a>
    <div class="flex gap-2">
        <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">print</span>Bake slip</a>
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $order->phone) }}?text={{ rawurlencode('Hi '.$order->name.', an update on your '.setting('site_name', 'Crinkle Nest').' order #'.$order->order_number.': ') }}" target="_blank" rel="noopener" class="adm-btn bg-whatsapp text-white hover:bg-whatsapp-dark"><span class="material-symbols-outlined">chat</span>WhatsApp customer</a>
    </div>
</div>
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 flex flex-col gap-6">
        <div class="adm-card !p-0 overflow-hidden">
            <table class="adm-table">
                <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th class="text-right">Total</th></tr></thead>
                <tbody>
                @foreach($order->items as $it)
                    <tr>
                        <td><div class="flex items-center gap-3"><img src="{{ media($it->image) }}" class="w-12 h-12 rounded-lg object-cover bg-surface-container" alt=""><div><div class="font-semibold text-primary">{{ $it->name }}</div><div class="text-xs text-outline">{{ $it->detail }}</div></div></div></td>
                        <td>{{ money($it->price) }}</td><td>{{ $it->qty }}</td><td class="text-right font-semibold">{{ money($it->total) }}</td>
                    </tr>
                @endforeach
                <tr><td colspan="3" class="text-right text-on-surface-variant">Subtotal</td><td class="text-right">{{ money($order->subtotal) }}</td></tr>
                <tr><td colspan="3" class="text-right text-on-surface-variant">Delivery</td><td class="text-right">{{ money($order->delivery_fee) }}</td></tr>
                @if($order->discount > 0)<tr><td colspan="3" class="text-right text-on-surface-variant">Discount ({{ $order->coupon_code }})</td><td class="text-right text-secondary">-{{ money($order->discount) }}</td></tr>@endif
                <tr><td colspan="3" class="text-right font-bold text-primary">Grand total</td><td class="text-right font-bold text-primary text-lg">{{ money($order->total) }}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="adm-card grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
            <div><div class="adm-label">Customer</div>{{ $order->name }}<br>{{ $order->phone }}<br>{{ $order->email }}@if($order->user)<br><a class="text-secondary underline" href="{{ route('admin.resource.edit', ['customers', $order->user_id]) }}">Account: {{ $order->user->email }}</a>@endif</div>
            <div><div class="adm-label">Delivery</div>{{ $order->address }}<br>{{ $order->city }}@if($order->instructions)<br><em class="text-on-surface-variant">{{ $order->instructions }}</em>@endif</div>
            <div><div class="adm-label">Slot</div>{{ $order->slot_label ?: '—' }}@if($order->custom_date)<br>Date: {{ $order->custom_date->format('d M Y') }}@endif</div>
            <div><div class="adm-label">Gift note</div>{{ $order->gift_note ?: '—' }}</div>
            @if($order->rating)<div><div class="adm-label">Customer rating</div>{{ str_repeat('★', $order->rating) }} {{ $order->review }}</div>@endif
        </div>
    </div>
    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="adm-card flex flex-col gap-4 h-fit">
        @csrf @method('PUT')
        <h2 class="font-headline-sm text-[18px] text-primary">Update order</h2>
        <div><label class="adm-label">Status</label><select name="status" class="adm-input">@foreach(\App\Models\Order::STATUSES as $k => $v)<option value="{{ $k }}" @selected($order->status === $k)>{{ $v }}</option>@endforeach</select>
            <p class="adm-help">Marking as Delivered awards loyalty points ({{ setting('points_per_100', 10) }} per {{ setting('currency_symbol', '₹') }}100) to registered customers.</p></div>
        <div><label class="adm-label">Status note (shown in tracker)</label><input name="status_note" value="{{ $order->status_note }}" class="adm-input" placeholder="e.g. Deck 02 at 220°C • Crust blooming"></div>
        <div><label class="adm-label">Target arrival</label><input name="target_arrival" value="{{ $order->target_arrival }}" class="adm-input" placeholder="e.g. Today by 3:30 PM"></div>
        <div><label class="adm-label">Live tracking link</label><input name="tracking_url" type="url" value="{{ $order->tracking_url }}" class="adm-input" placeholder="https://…"></div>
        <div><label class="adm-label">Order label</label><input name="label" value="{{ $order->label }}" class="adm-input" placeholder="e.g. Valentine Special, Custom Bake"></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="adm-label">Payment</label><select name="payment_status" class="adm-input">@foreach(\App\Models\Order::PAYMENT_STATUSES as $k => $v)<option value="{{ $k }}" @selected($order->payment_status === $k)>{{ $v }}</option>@endforeach</select></div>
            <div><label class="adm-label">Payment note</label><input name="payment_note" value="{{ $order->payment_note }}" class="adm-input" placeholder="UPI • GPay"></div>
        </div>
        <button class="adm-btn adm-btn-primary justify-center"><span class="material-symbols-outlined">save</span>Save changes</button>
    </form>
</div>
<form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="mt-6" onsubmit="return confirm('Delete this order permanently?')">@csrf @method('DELETE')<button class="adm-btn adm-btn-danger"><span class="material-symbols-outlined">delete</span>Delete order</button></form>
@endsection
