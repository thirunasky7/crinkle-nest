@extends('admin.layout')

@section('title', 'Contact Messages')

@section('content')
<div class="adm-card !p-0 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th></th><th>From</th><th>Inquiry</th><th>Message</th><th>Event date</th><th>Received</th></tr></thead>
        <tbody>
        @forelse($messages as $m)
            <tr onclick="location.href='{{ route('admin.messages.show', $m) }}'" class="cursor-pointer {{ $m->is_read ? '' : 'font-semibold' }}">
                <td>@unless($m->is_read)<span class="w-2.5 h-2.5 rounded-full bg-secondary inline-block"></span>@endunless</td>
                <td class="text-primary">{{ $m->name }}<div class="text-xs text-outline font-normal">{{ $m->phone }}</div></td>
                <td class="text-xs">{{ $m->inquiry_type }}</td>
                <td class="text-sm text-on-surface-variant font-normal">{{ \Illuminate\Support\Str::limit($m->message, 80) }}</td>
                <td class="text-xs">{{ optional($m->event_date)->format('d M Y') }}</td>
                <td class="text-xs text-on-surface-variant whitespace-nowrap">{{ $m->created_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center py-10 text-on-surface-variant">No messages yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection
