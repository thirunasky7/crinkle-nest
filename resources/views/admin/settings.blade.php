@extends('admin.layout')

@section('title', 'Settings · '.(\App\Models\Setting::GROUPS[$group] ?? ucfirst($group)))

@section('content')
<div class="flex flex-wrap gap-2 mb-5">
    @foreach(\App\Models\Setting::GROUPS as $gk => $gl)
        <a href="{{ route('admin.settings', ['group' => $gk]) }}" class="adm-btn {{ $group === $gk ? 'adm-btn-primary' : 'adm-btn-soft' }}">{{ $gl }}</a>
    @endforeach
</div>
<form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="group" value="{{ $group }}">
    <div class="adm-card">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($settings as $s)
                <div class="{{ in_array($s->type, ['textarea', 'code'], true) ? 'md:col-span-2' : '' }}">
                    @if($s->type === 'boolean')
                        <label class="flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low cursor-pointer">
                            <input type="checkbox" name="values[{{ $s->key }}]" value="1" class="w-4 h-4 accent-secondary" @checked($s->value === '1')>
                            <span class="text-sm font-semibold text-primary">{{ $s->label }}</span>
                        </label>
                    @else
                        <label class="adm-label" for="s_{{ $s->key }}">{{ $s->label }}</label>
                        @switch($s->type)
                            @case('textarea')
                                <textarea id="s_{{ $s->key }}" name="values[{{ $s->key }}]" rows="3" class="adm-input">{{ $s->value }}</textarea>
                                @break
                            @case('code')
                                <textarea id="s_{{ $s->key }}" name="values[{{ $s->key }}]" rows="5" class="adm-input font-mono !text-xs">{{ $s->value }}</textarea>
                                @break
                            @case('image')
                                <div class="flex items-start gap-3">
                                    <div class="w-20 h-20 rounded-xl bg-surface-container overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($s->value)<img src="{{ media($s->value) }}" class="w-full h-full object-contain" alt="">@else<span class="material-symbols-outlined text-outline">image</span>@endif
                                    </div>
                                    <div class="flex-1 flex flex-col gap-1.5">
                                        <input type="file" name="files[{{ $s->key }}]" accept="image/*" class="text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-primary file:text-on-primary file:text-xs">
                                        <input type="text" name="values[{{ $s->key }}]" value="{{ $s->value }}" placeholder="…or image URL" class="adm-input !py-1.5 !text-xs">
                                        @if($s->value)<label class="text-xs text-error flex items-center gap-1"><input type="checkbox" name="remove[{{ $s->key }}]" value="1"> Remove</label>@endif
                                    </div>
                                </div>
                                @break
                            @case('color')
                                <div class="flex items-center gap-2">
                                    <input type="color" value="{{ $s->value }}" class="w-12 h-10 rounded-lg border border-surface-container" oninput="this.nextElementSibling.value=this.value">
                                    <input id="s_{{ $s->key }}" type="text" name="values[{{ $s->key }}]" value="{{ $s->value }}" class="adm-input font-mono" oninput="this.previousElementSibling.value=this.value">
                                </div>
                                @break
                            @default
                                <input id="s_{{ $s->key }}" type="{{ in_array($s->type, ['number', 'email', 'url'], true) ? $s->type : 'text' }}" @if($s->type === 'number') step="any" @endif name="values[{{ $s->key }}]" value="{{ $s->value }}" class="adm-input">
                        @endswitch
                    @endif
                    @if($s->help)<p class="adm-help">{{ $s->help }}</p>@endif
                </div>
            @empty
                <p class="text-on-surface-variant">No settings in this group.</p>
            @endforelse
        </div>
    </div>
    <div class="sticky bottom-0 bg-surface-container-low/90 backdrop-blur py-3 flex justify-end border-t border-surface-container -mx-4 lg:-mx-8 px-4 lg:px-8 mt-6">
        <button class="adm-btn adm-btn-primary"><span class="material-symbols-outlined">save</span>Save settings</button>
    </div>
</form>
@endsection
