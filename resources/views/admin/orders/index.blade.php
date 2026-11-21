@extends('admin.layout')

@section('title', 'Orders')

@section('content')
<form method="GET" class="flex flex-wrap items-center gap-2 mb-5">
    <input name="q" value="{{ request('q') }}" placeholder="Order #, name or phone" class="adm-input !w-64">
    <select name="status" class="adm-input !w-auto" onchange="this.form.submit()">
        <option value="">All statuses</option>
        @foreach(\App\Models\Order::STATUSES as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach
    </select>
    <button class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">search</span>Filter</button>
</form>
<div class="adm-card !p-0 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Slot</th><th>Placed</th></tr></thead>
        <tbody>
        @forelse($orders as $o)
            <tr onclick="location.href='{{ route('admin.orders.show', $o) }}'" class="cursor-pointer">
                <td class="font-semibold text-primary">#{{ $o->order_number }}</td>
                <td>{{ $o->name }}<div class="text-xs text-outline">{{ $o->phone }}</div></td>
                <td>{{ $o->items_count }}</td>
                <td class="font-semibold">{{ money($o->total) }}</td>
                <td><span class="text-xs">{{ ucfirst($o->payment_method) }}</span><div class="text-xs {{ $o->payment_status === 'paid' ? 'text-emerald-700' : 'text-secondary' }} font-semibold">{{ ucfirst($o->payment_status) }}</div></td>
                <td><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $o->isActive() ? 'bg-secondary-fixed text-on-secondary-fixed' : ($o->status === 'cancelled' ? 'bg-error-container text-on-error-container' : 'bg-surface-container text-on-surface-variant') }}">{{ $o->statusLabel() }}</span></td>
                <td class="text-xs text-on-surface-variant">{{ $o->slot_label }}</td>
                <td class="text-xs text-on-surface-variant whitespace-nowrap">{{ $o->created_at->format('d M, g:i A') }}</td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center py-10 text-on-surface-variant">No orders found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
