@php
    $summary = $row ? ($row->title ?: $row->label ?: $row->subtitle ?: 'Item #'.$row->id) : 'New item';
@endphp
<details class="rounded-xl bg-surface-container-low border border-surface-container" data-row data-new="{{ $row ? 0 : 1 }}">
    <summary class="flex items-center gap-3 p-3 cursor-pointer select-none">
        <span class="material-symbols-outlined text-outline">drag_indicator</span>
        @if($row && ($row->icon ?? null))<span class="material-symbols-outlined text-secondary">{{ $row->icon }}</span>@endif
        @if($row && ($row->image ?? null))<img src="{{ media($row->image) }}" class="w-8 h-8 rounded-lg object-cover" alt="">@endif
        <span class="font-semibold text-primary text-sm truncate">{{ \Illuminate\Support\Str::limit(strip_tags($summary), 80) }}</span>
        @if($row && ! $row->is_active)<span class="text-[11px] px-2 py-0.5 rounded-full bg-surface-container text-outline">hidden</span>@endif
        <button type="button" onclick="event.preventDefault(); removeRow(this)" class="ml-auto adm-btn adm-btn-danger !py-1 !px-2.5 !text-xs"><span class="material-symbols-outlined !text-[16px]">delete</span><span>Remove</span></button>
    </summary>
    <div class="p-4 pt-1 grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id ?? '' }}">
        <input type="hidden" name="rows[{{ $i }}][_delete]" value="0" data-delete>
        @foreach($fields as $f)
            @include('admin.resource.field', [
                'f' => $f,
                'name' => 'rows['.$i.']['.$f['name'].']',
                'value' => $row ? $row->{$f['name']} : ($f['default'] ?? null),
                'id' => 'r_'.$i.'_'.$f['name'],
            ])
        @endforeach
    </div>
</details>
