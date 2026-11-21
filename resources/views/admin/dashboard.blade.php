@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @foreach([
        ['Orders today', $stats['orders_today'], 'today', route('admin.orders.index')],
        ['Active orders', $stats['active_orders'], 'local_fire_department', route('admin.orders.index')],
        ['Revenue this month', money($stats['revenue_month']), 'payments', route('admin.orders.index')],
        ['Customers', $stats['customers'], 'group', route('admin.resource.index', 'customers')],
        ['Live products', $stats['products'], 'cookie', route('admin.resource.index', 'products')],
        ['Unread messages', $stats['unread'], 'mail', route('admin.messages.index')],
    ] as [$label, $value, $icon, $href])
    <a href="{{ $href }}" class="adm-card !p-5 hover:-translate-y-0.5 transition-transform">
        <div class="w-10 h-10 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center mb-3"><span class="material-symbols-outlined">{{ $icon }}</span></div>
        <div class="text-[24px] font-bold text-primary leading-none">{{ $value }}</div>
        <div class="text-[12px] text-on-surface-variant mt-1">{{ $label }}</div>
    </a>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="adm-card xl:col-span-2 !p-0 overflow-hidden">
        <div class="flex items-center justify-between p-5"><h2 class="font-headline-sm text-[18px] text-primary">Recent orders</h2><a class="text-secondary text-sm font-semibold" href="{{ route('admin.orders.index') }}">View all →</a></div>
        <div class="overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Placed</th></tr></thead>
            <tbody>
            @forelse($recentOrders as $o)
                <tr onclick="location.href='{{ route('admin.orders.show', $o) }}'" class="cursor-pointer">
                    <td class="font-semibold text-primary">#{{ $o->order_number }}</td>
                    <td>{{ $o->name }}<div class="text-xs text-outline">{{ $o->phone }}</div></td>
                    <td>{{ money($o->total) }}</td>
                    <td><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $o->isActive() ? 'bg-secondary-fixed text-on-secondary-fixed' : 'bg-surface-container text-on-surface-variant' }}">{{ $o->statusLabel() }}</span></td>
                    <td class="text-on-surface-variant">{{ $o->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-on-surface-variant py-8">No orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    <div class="adm-card">
        <div class="flex items-center justify-between mb-4"><h2 class="font-headline-sm text-[18px] text-primary">Latest messages</h2><a class="text-secondary text-sm font-semibold" href="{{ route('admin.messages.index') }}">All →</a></div>
        <div class="flex flex-col gap-2">
        @forelse($recentMessages as $m)
            <a href="{{ route('admin.messages.show', $m) }}" class="p-3 rounded-xl {{ $m->is_read ? 'bg-surface-container-low' : 'bg-secondary-fixed/40' }} hover:bg-surface-container">
                <div class="flex justify-between gap-2"><span class="font-semibold text-primary text-sm">{{ $m->name }}</span><span class="text-xs text-outline">{{ $m->created_at->diffForHumans() }}</span></div>
                <div class="text-xs text-secondary">{{ $m->inquiry_type }}</div>
                <div class="text-sm text-on-surface-variant line-clamp-2">{{ $m->message }}</div>
            </a>
        @empty
            <p class="text-sm text-on-surface-variant">No messages yet.</p>
        @endforelse
        </div>
    </div>
</div>

<div class="adm-card mt-6">
    <h2 class="font-headline-sm text-[18px] text-primary mb-3">Quick edits</h2>
    <div class="flex flex-wrap gap-2">
        @foreach(\App\Models\Section::PAGES as $pk => $pl)
            <a href="{{ route('admin.resource.index', ['sections', 'filter' => $pk]) }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">edit_note</span>{{ $pl }}</a>
        @endforeach
        <a href="{{ route('admin.resource.index', 'videos') }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">smart_display</span>Videos</a>
        <a href="{{ route('admin.settings', ['group' => 'contact']) }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">call</span>Contact details</a>
    </div>
</div>
@endsection
