{{-- Renders one admin form field. Vars: $f (field config), $name (input name), $value, $id --}}
@php
    $type = $f['type'];
    $id = $id ?? 'f_'.preg_replace('/[^a-z0-9]+/i', '_', $name);
    $req = ! empty($f['required']) && $type !== 'password';
@endphp
<div class="{{ in_array($type, ['html', 'keyvalue', 'textarea'], true) ? 'md:col-span-2' : '' }} {{ $type === 'checkbox' ? 'flex items-end' : '' }}">
    @if($type === 'checkbox')
        <label class="flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low w-full cursor-pointer">
            <input type="checkbox" name="{{ $name }}" value="1" class="w-4 h-4 accent-secondary" @checked($value)>
            <span class="text-sm font-semibold text-primary">{{ $f['label'] }}</span>
        </label>
    @else
        <label class="adm-label" for="{{ $id }}">{{ $f['label'] }} @if($req)<span class="text-error">*</span>@endif</label>
        @switch($type)
            @case('textarea')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $f['rows'] ?? 3 }}" class="adm-input" @if($req) required @endif>{{ $value }}</textarea>
                @break
            @case('html')
                <div class="rounded-xl border border-surface-container overflow-hidden bg-white">
                    <div class="flex items-center gap-1 p-1.5 bg-surface-container-low border-b border-surface-container text-xs">
                        <button type="button" class="adm-btn adm-btn-soft !py-1 !px-2.5 !text-xs" onclick="htmlTab(this,'code')">HTML</button>
                        <button type="button" class="adm-btn !py-1 !px-2.5 !text-xs" onclick="htmlTab(this,'preview')">Preview</button>
                        <span class="ml-auto text-outline pr-2">Supports the site's Tailwind classes</span>
                    </div>
                    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $f['rows'] ?? 10 }}" class="w-full p-3 font-mono text-[12.5px] leading-relaxed focus:outline-none" data-html-src>{{ $value }}</textarea>
                    <div class="hidden p-4 bg-surface-container-low" data-html-preview></div>
                </div>
                @break
            @case('image')
                <div class="flex items-start gap-3">
                    <div class="w-20 h-20 rounded-xl bg-surface-container overflow-hidden shrink-0 flex items-center justify-center" data-img-preview>
                        @if($value)<img src="{{ media($value) }}" class="w-full h-full object-cover" alt="">@else<span class="material-symbols-outlined text-outline">image</span>@endif
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1.5">
                        <input type="file" name="{{ preg_replace('/\]$/', '_file]', $name) === $name ? $name.'_file' : preg_replace('/\]$/', '_file]', $name) }}" accept="image/*" class="text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-primary file:text-on-primary file:text-xs" onchange="previewImg(this)">
                        <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="…or paste an image URL" class="adm-input !py-1.5 !text-xs">
                        @if($value)
                        <label class="text-xs text-error flex items-center gap-1"><input type="checkbox" name="{{ preg_replace('/\]$/', '_remove]', $name) === $name ? $name.'_remove' : preg_replace('/\]$/', '_remove]', $name) }}" value="1"> Remove image</label>
                        @endif
                    </div>
                </div>
                @break
            @case('video')
                <div class="flex flex-col gap-1.5">
                    @if($value)
                        <video src="{{ media($value) }}" class="w-40 rounded-xl bg-black" controls preload="metadata"></video>
                    @endif
                    <input type="file" name="{{ $name }}_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-primary file:text-on-primary file:text-xs">
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @if($value)<label class="text-xs text-error flex items-center gap-1"><input type="checkbox" name="{{ $name }}_remove" value="1"> Remove uploaded video</label>@endif
                    <p class="adm-help">Max upload size depends on your PHP settings (upload_max_filesize).</p>
                </div>
                @break
            @case('select')
                <select id="{{ $id }}" name="{{ $name }}" class="adm-input" @if($req) required @endif>
                    @if(! $req || isset($f['empty']))<option value="">{{ $f['empty'] ?? '—' }}</option>@endif
                    @foreach(\App\Http\Controllers\Admin\ResourceController::options($f['options']) as $k => $v)
                        <option value="{{ $k }}" @selected((string) $value === (string) $k)>{{ $v }}</option>
                    @endforeach
                </select>
                @break
            @case('icon')
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center shrink-0"><span class="material-symbols-outlined" data-icon-preview>{{ $value ?: 'help' }}</span></div>
                    <input id="{{ $id }}" type="text" name="{{ $name }}" value="{{ $value }}" class="adm-input" placeholder="e.g. cookie" oninput="this.closest('div').querySelector('[data-icon-preview]').textContent=this.value||'help'">
                </div>
                <p class="adm-help">Material Symbols name — browse at <a class="text-secondary underline" href="https://fonts.google.com/icons" target="_blank" rel="noopener">fonts.google.com/icons</a></p>
                @break
            @case('keyvalue')
                @php $pairs = is_array($value) ? $value : (json_decode((string) $value, true) ?: []); $kvIndex = 0; @endphp
                <div class="flex flex-col gap-2" data-kv data-name="{{ $name }}">
                    @foreach($pairs as $k => $v)
                        <div class="grid grid-cols-12 gap-2 items-start" data-kv-row>
                            <input name="{{ $name }}[{{ $kvIndex }}][key]" value="{{ $k }}" class="adm-input col-span-4 !text-xs font-mono" placeholder="key">
                            <textarea name="{{ $name }}[{{ $kvIndex }}][value]" rows="1" class="adm-input col-span-7 !text-sm" placeholder="value">{{ $v }}</textarea>
                            <button type="button" class="col-span-1 adm-btn adm-btn-danger !px-2 justify-center" onclick="this.closest('[data-kv-row]').remove()"><span class="material-symbols-outlined">close</span></button>
                        </div>
                        @php $kvIndex++; @endphp
                    @endforeach
                    <button type="button" class="adm-btn adm-btn-soft self-start !text-xs" onclick="addKv(this)"><span class="material-symbols-outlined">add</span>Add text</button>
                </div>
                @break
            @case('password')
                <input id="{{ $id }}" type="password" name="{{ $name }}" class="adm-input" autocomplete="new-password">
                @break
            @default
                <input id="{{ $id }}" type="{{ match ($type) { 'number' => 'number', 'decimal' => 'number', 'email' => 'email', 'date' => 'date', 'color' => 'color', 'url' => 'url', default => 'text' } }}"
                       @if($type === 'decimal') step="0.01" @endif
                       @if(isset($f['min'])) min="{{ $f['min'] }}" @endif @if(isset($f['max'])) max="{{ $f['max'] }}" @endif
                       name="{{ $name }}" value="{{ $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value }}" class="adm-input" @if($req) required @endif>
        @endswitch
        @if(! empty($f['help']))<p class="adm-help">{{ $f['help'] }}</p>@endif
    @endif
</div>
