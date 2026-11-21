@extends('admin.layout')

@section('title', ($record->exists ? 'Edit ' : 'New ').strtolower($cfg['singular']))

@section('content')
@php
    $rep = $cfg['repeater'] ?? null;
    $repLabel = $rep['label'] ?? null;
    $repFields = $rep['fields'] ?? [];
    if ($rep && ! empty($rep['label_field'])) {
        $raw = (string) $record->{$rep['label_field']};
        if ($raw === '') {
            $rep = null;
        } else {
            [$repLabel, $only] = array_pad(explode('|', $raw, 2), 2, null);
            if ($only) {
                $keep = array_map('trim', explode(',', $only));
                $keep = array_merge($keep, ['sort', 'is_active']);
                $repFields = array_values(array_filter($repFields, fn ($f) => in_array($f['name'], $keep, true)));
            }
        }
    }
    $rows = $rep && $record->exists ? $record->{$rep['relation']}()->get() : collect();
@endphp
<div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
    <a href="{{ route('admin.resource.index', $resource) }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">arrow_back</span>{{ $cfg['title'] }}</a>
    <div class="flex items-center gap-2">
        @if($record->exists && $resource === 'sections')
            <span class="text-xs font-mono px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant">{{ \App\Models\Section::PAGES[$record->page] ?? $record->page }} · {{ $record->key }}</span>
        @endif
        @if($record->exists && ! empty($cfg['view_url']))
            <a href="{{ url(str_replace('{slug}', $record->slug, $cfg['view_url'])) }}" target="_blank" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">visibility</span>View</a>
        @endif
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ $record->exists ? route('admin.resource.update', [$resource, $record->getKey()]) : route('admin.resource.store', $resource) }}">
    @csrf
    @if($record->exists) @method('PUT') @endif

    <div class="adm-card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($cfg['fields'] as $f)
                @include('admin.resource.field', ['f' => $f, 'name' => $f['name'], 'value' => old($f['name'], $f['type'] === 'password' ? null : $record->{$f['name']})])
            @endforeach
        </div>
    </div>

    @if($rep)
    <div class="adm-card mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-headline-sm text-[18px] text-primary">{{ $repLabel }}</h2>
            <button type="button" class="adm-btn adm-btn-soft" onclick="addRow()"><span class="material-symbols-outlined">add</span>Add item</button>
        </div>
        <div id="repRows" class="flex flex-col gap-3">
            @foreach($rows as $i => $row)
                @include('admin.resource.row', ['i' => $i, 'row' => $row, 'fields' => $repFields])
            @endforeach
        </div>
        <template id="repTemplate">
            @include('admin.resource.row', ['i' => '__I__', 'row' => null, 'fields' => $repFields])
        </template>
        <p class="adm-help mt-3">Drag-free ordering: use the “Order” number on each item (lower shows first). Unchecked “Visible” hides an item without deleting it.</p>
    </div>
    @endif

    <div class="sticky bottom-0 bg-surface-container-low/90 backdrop-blur py-3 flex items-center gap-2 justify-end border-t border-surface-container -mx-4 lg:-mx-8 px-4 lg:px-8">
        <a href="{{ route('admin.resource.index', $resource) }}" class="adm-btn adm-btn-soft">Cancel</a>
        <button class="adm-btn adm-btn-primary"><span class="material-symbols-outlined">save</span>Save {{ strtolower($cfg['singular']) }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let repIndex = {{ $rows->count() + 100 }};
    function addRow() {
        const tpl = document.getElementById('repTemplate').innerHTML.replaceAll('__I__', repIndex++);
        const wrap = document.createElement('div');
        wrap.innerHTML = tpl.trim();
        const el = wrap.firstElementChild;
        el.open = true;
        document.getElementById('repRows').appendChild(el);
    }
    function removeRow(btn) {
        const row = btn.closest('[data-row]');
        const del = row.querySelector('[data-delete]');
        if (row.dataset.new === '1') { row.remove(); return; }
        del.value = del.value === '1' ? '0' : '1';
        row.classList.toggle('opacity-40', del.value === '1');
        btn.querySelector('span:last-child').textContent = del.value === '1' ? 'Undo' : 'Remove';
    }
    function addKv(btn) {
        const box = btn.closest('[data-kv]');
        const n = box.dataset.name, i = Date.now();
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 items-start';
        row.setAttribute('data-kv-row', '');
        row.innerHTML = `<input name="${n}[${i}][key]" class="adm-input col-span-4 !text-xs font-mono" placeholder="key"><textarea name="${n}[${i}][value]" rows="1" class="adm-input col-span-7 !text-sm" placeholder="value"></textarea><button type="button" class="col-span-1 adm-btn adm-btn-danger !px-2 justify-center" onclick="this.closest('[data-kv-row]').remove()"><span class="material-symbols-outlined">close</span></button>`;
        box.insertBefore(row, btn);
    }
    function previewImg(input) {
        const box = input.closest('.flex.items-start').querySelector('[data-img-preview]');
        if (input.files && input.files[0]) box.innerHTML = '<img class="w-full h-full object-cover" src="' + URL.createObjectURL(input.files[0]) + '">';
    }
    function htmlTab(btn, mode) {
        const root = btn.closest('.rounded-xl');
        const src = root.querySelector('[data-html-src]'), prev = root.querySelector('[data-html-preview]');
        root.querySelectorAll('button').forEach(b => b.classList.remove('adm-btn-soft'));
        btn.classList.add('adm-btn-soft');
        if (mode === 'preview') { prev.innerHTML = src.value; prev.classList.remove('hidden'); src.classList.add('hidden'); }
        else { prev.classList.add('hidden'); src.classList.remove('hidden'); }
    }
</script>
@endpush
