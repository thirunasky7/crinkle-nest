@extends('admin.layout')

@section('title', $cfg['title'])

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input !w-56">
        @if(! empty($cfg['filter']))
            <select name="filter" class="adm-input !w-auto" onchange="this.form.submit()">
                <option value="">All {{ strtolower($cfg['filter']['label']) }}s</option>
                @foreach(\App\Http\Controllers\Admin\ResourceController::options($cfg['filter']['options']) as $k => $v)
                    <option value="{{ $k }}" @selected((string) request('filter') === (string) $k)>{{ $v }}</option>
                @endforeach
            </select>
        @endif
        <button class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">search</span>Filter</button>
    </form>
    <div class="flex items-center gap-2">
    @if(! empty($cfg['export']))
        <a href="{{ route('admin.resource.export', [$resource] + request()->only('q', 'filter')) }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">download</span>Export CSV</a>
    @endif
    @if($cfg['can_create'])
        <a href="{{ route('admin.resource.create', $resource) }}" class="adm-btn adm-btn-primary"><span class="material-symbols-outlined">add</span>New {{ strtolower($cfg['singular']) }}</a>
    @endif
    </div>
</div>

<div class="adm-card !p-0 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="adm-table">
        <thead><tr>@foreach($cfg['columns'] as $col => $c)<th>{{ $c['label'] }}</th>@endforeach<th class="text-right">Actions</th></tr></thead>
        <tbody>
        @forelse($records as $r)
            <tr>
                @foreach($cfg['columns'] as $col => $c)
                    @php $v = data_get($r, $col); $type = $c['type'] ?? 'text'; @endphp
                    <td>
                        @switch($type)
                            @case('image')
                                @if($v)<img src="{{ media($v) }}" class="w-12 h-12 rounded-lg object-cover bg-surface-container" alt="">@else<div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center text-outline"><span class="material-symbols-outlined">image</span></div>@endif
                                @break
                            @case('bool')
                                <span class="material-symbols-outlined {{ $v ? 'text-emerald-700' : 'text-outline-variant' }}">{{ $v ? 'check_circle' : 'cancel' }}</span>
                                @break
                            @case('money') {{ money($v) }} @break
                            @case('map') <span class="px-2 py-0.5 rounded-full bg-surface-container text-xs font-semibold">{{ $c['map'][$v] ?? $v }}</span> @break
                            @case('prefix') <span class="font-mono text-xs">{{ $c['prefix'] }}{{ $v }}</span> @break
                            @case('date') {{ optional($v)->format('d M Y') }} @break
                            @default
                                <span class="{{ $loop->first || $loop->index === 1 ? 'font-semibold text-primary' : '' }}">{{ \Illuminate\Support\Str::limit(strip_tags((string) $v), $c['limit'] ?? 70) }}</span>
                        @endswitch
                    </td>
                @endforeach
                <td class="text-right whitespace-nowrap">
                    @if(! empty($cfg['view_url']))
                        <a href="{{ url(str_replace('{slug}', $r->slug, $cfg['view_url'])) }}" target="_blank" class="adm-btn adm-btn-soft !px-3" title="View"><span class="material-symbols-outlined">visibility</span></a>
                    @endif
                    <a href="{{ route('admin.resource.edit', [$resource, $r->getKey()]) }}" class="adm-btn adm-btn-soft !px-3" title="Edit"><span class="material-symbols-outlined">edit</span></a>
                    @if($cfg['can_delete'])
                    <form method="POST" action="{{ route('admin.resource.destroy', [$resource, $r->getKey()]) }}" class="inline" onsubmit="return confirm('Delete this {{ strtolower($cfg['singular']) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="adm-btn adm-btn-danger !px-3" title="Delete"><span class="material-symbols-outlined">delete</span></button>
                    </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ count($cfg['columns']) + 1 }}" class="text-center py-10 text-on-surface-variant">Nothing here yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $records->links() }}</div>
@endsection
