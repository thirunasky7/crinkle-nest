<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    protected function config(string $resource): array
    {
        $cfg = config("admin.resources.$resource");
        abort_unless($cfg, 404);

        return $cfg + ['slug_key' => $resource, 'can_create' => true, 'can_delete' => true];
    }

    public static function options($spec): array
    {
        if (is_array($spec) && isset($spec[0]) && is_string($spec[0]) && class_exists($spec[0])) {
            [$model, $label] = $spec;

            return $model::query()->orderBy($label)->pluck($label, 'id')->all();
        }

        return (array) $spec;
    }

    public function index(Request $request, string $resource)
    {
        $cfg = $this->config($resource);
        $query = $cfg['model']::query();

        if (($q = trim((string) $request->query('q'))) !== '' && ! empty($cfg['search'])) {
            $query->where(function ($w) use ($cfg, $q) {
                foreach ($cfg['search'] as $col) {
                    $w->orWhere($col, 'like', "%{$q}%");
                }
            });
        }
        if (! empty($cfg['filter']) && $request->filled('filter')) {
            $query->where($cfg['filter']['field'], $request->query('filter'));
        }
        $with = collect(array_keys($cfg['columns']))->filter(fn ($c) => str_contains($c, '.'))->map(fn ($c) => Str::before($c, '.'))->unique()->all();
        if ($with) {
            $query->with($with);
        }
        [$col, $dir] = $cfg['order'] ?? ['id', 'desc'];
        $records = $query->orderBy($col, $dir)->orderBy('id')->paginate(25)->withQueryString();

        return view('admin.resource.index', compact('cfg', 'records', 'resource'));
    }

    public function create(string $resource)
    {
        $cfg = $this->config($resource);
        abort_unless($cfg['can_create'], 403);
        $record = new $cfg['model']();
        foreach ($cfg['fields'] as $f) {
            if (array_key_exists('default', $f)) {
                $record->{$f['name']} = $f['default'];
            }
        }

        return view('admin.resource.form', compact('cfg', 'record', 'resource'));
    }

    public function store(Request $request, string $resource)
    {
        $cfg = $this->config($resource);
        abort_unless($cfg['can_create'], 403);
        $record = new $cfg['model']();
        $this->save($request, $cfg, $record);

        return redirect()->route('admin.resource.edit', [$resource, $record->getKey()])->with('success', $cfg['singular'].' created.');
    }

    public function edit(string $resource, $id)
    {
        $cfg = $this->config($resource);
        $record = $cfg['model']::findOrFail($id);

        return view('admin.resource.form', compact('cfg', 'record', 'resource'));
    }

    public function update(Request $request, string $resource, $id)
    {
        $cfg = $this->config($resource);
        $record = $cfg['model']::findOrFail($id);
        $this->save($request, $cfg, $record);

        return redirect()->route('admin.resource.edit', [$resource, $record->getKey()])->with('success', $cfg['singular'].' saved.');
    }

    public function destroy(Request $request, string $resource, $id)
    {
        $cfg = $this->config($resource);
        abort_unless($cfg['can_delete'], 403);
        $record = $cfg['model']::findOrFail($id);
        if ($record->getKey() === optional($request->user())->getKey()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        foreach ($cfg['fields'] as $f) {
            if (in_array($f['type'], ['image', 'video'], true)) {
                $this->deleteFile($record->{$f['name']});
            }
        }
        $record->delete();

        return redirect()->route('admin.resource.index', $resource)->with('success', $cfg['singular'].' deleted.');
    }

    protected function rulesFor(array $fields, ?Model $record, string $table, string $prefix = ''): array
    {
        $rules = [];
        foreach ($fields as $f) {
            $name = $prefix.$f['name'];
            $base = ! empty($f['required']) && $f['type'] !== 'password' ? ['required'] : ['nullable'];
            $rules[$name] = array_merge($base, match ($f['type']) {
                'number' => ['integer'],
                'decimal' => ['numeric'],
                'email' => ['email', 'max:190'],
                'date' => ['date'],
                'checkbox' => ['boolean'],
                'password' => ['string', 'min:8'],
                'keyvalue' => ['array'],
                'image', 'video' => ['string', 'max:2048'],
                'html', 'textarea' => ['string'],
                default => ['string', 'max:2048'],
            });
            if (isset($f['min'])) {
                $rules[$name][] = 'min:'.$f['min'];
            }
            if (isset($f['max'])) {
                $rules[$name][] = 'max:'.$f['max'];
            }
            if (! empty($f['unique'])) {
                $rules[$name][] = 'unique:'.$table.','.$f['name'].($record && $record->exists ? ','.$record->getKey() : '');
            }
            if ($f['type'] === 'image') {
                $rules[$prefix.$f['name'].'_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg,avif,ico', 'max:8192'];
            }
            if ($f['type'] === 'video') {
                $rules[$prefix.$f['name'].'_file'] = ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime', 'max:102400'];
            }
        }

        return $rules;
    }

    protected function save(Request $request, array $cfg, Model $record): void
    {
        $table = $record->getTable();
        $rules = $this->rulesFor($cfg['fields'], $record, $table);
        if (! empty($cfg['repeater'])) {
            $rules += $this->rulesFor($cfg['repeater']['fields'], null, 'x', 'rows.*.');
            foreach ($cfg['repeater']['fields'] as $f) {
                if (! empty($f['required'])) {
                    $rules['rows.*.'.$f['name']] = array_values(array_diff($rules['rows.*.'.$f['name']], ['required']));
                    $rules['rows.*.'.$f['name']][] = 'required_unless:rows.*._delete,1';
                }
            }
        }
        $request->validate($rules);

        $this->fill($request, $cfg['fields'], $record, $request->all(), $request->allFiles(), $cfg['slug_key']);

        if (! empty($cfg['slug']) && in_array('slug', $this->columns($record), true)) {
            $record->slug = Str::slug($record->slug ?: $record->{$cfg['slug']}) ?: Str::random(6);
            $base = $record->slug;
            $i = 2;
            while ($cfg['model']::where('slug', $record->slug)->where($record->getKeyName(), '!=', $record->getKey() ?? 0)->exists()) {
                $record->slug = $base.'-'.$i++;
            }
        }
        $record->save();

        if (! empty($cfg['repeater'])) {
            $this->saveRepeater($request, $cfg['repeater'], $record, $cfg['slug_key']);
        }

        Setting::flush();
    }

    protected function columns(Model $record): array
    {
        static $cache = [];

        return $cache[$record->getTable()] ??= \Illuminate\Support\Facades\Schema::getColumnListing($record->getTable());
    }

    protected function fill(Request $request, array $fields, Model $record, array $input, array $files, string $folder): void
    {
        foreach ($fields as $f) {
            $name = $f['name'];
            switch ($f['type']) {
                case 'checkbox':
                    $value = ! empty($input[$name]);
                    if ($name === 'is_admin') {
                        $record->forceFill([$name => $value]);
                        continue 2;
                    }
                    $record->{$name} = $value;
                    break;
                case 'password':
                    if (! empty($input[$name])) {
                        $record->{$name} = Hash::make($input[$name]);
                    } elseif (! $record->exists) {
                        $record->{$name} = Hash::make(Str::random(16));
                    }
                    break;
                case 'image':
                case 'video':
                    $file = $files[$name.'_file'] ?? null;
                    if ($file) {
                        $this->deleteFile($record->{$name});
                        $record->{$name} = $file->store($folder, 'public');
                    } elseif (! empty($input[$name.'_remove'])) {
                        $this->deleteFile($record->{$name});
                        $record->{$name} = null;
                    } elseif (array_key_exists($name, $input) && $input[$name] !== $record->{$name}) {
                        $record->{$name} = $input[$name] ?: null;
                    }
                    break;
                case 'keyvalue':
                    $pairs = [];
                    foreach ((array) ($input[$name] ?? []) as $row) {
                        $k = trim((string) ($row['key'] ?? ''));
                        if ($k !== '') {
                            $pairs[$k] = (string) ($row['value'] ?? '');
                        }
                    }
                    $record->{$name} = $pairs;
                    break;
                case 'number':
                    $record->{$name} = ($input[$name] ?? '') === '' ? ($f['default'] ?? null) : (int) $input[$name];
                    break;
                case 'decimal':
                    $record->{$name} = ($input[$name] ?? '') === '' ? ($f['default'] ?? 0) : $input[$name];
                    break;
                default:
                    if (! array_key_exists($name, $input)) {
                        continue 2;
                    }
                    $value = $input[$name];
                    if (! empty($f['uppercase']) && $value !== null) {
                        $value = strtoupper($value);
                    }
                    $record->{$name} = $value === '' ? null : $value;
            }
        }
    }

    protected function saveRepeater(Request $request, array $rep, Model $record, string $folder): void
    {
        $relation = $record->{$rep['relation']}();
        $existing = $relation->get()->keyBy('id');
        $rows = (array) $request->input('rows', []);
        $files = (array) Arr::get($request->allFiles(), 'rows', []);
        $sort = 0;

        foreach ($rows as $i => $row) {
            $item = ! empty($row['id']) ? $existing->get((int) $row['id']) : null;
            if (! empty($row['_delete'])) {
                if ($item) {
                    foreach ($rep['fields'] as $f) {
                        if ($f['type'] === 'image') {
                            $this->deleteFile($item->{$f['name']});
                        }
                    }
                    $item->delete();
                }
                continue;
            }
            $item ??= $relation->make();
            $this->fill($request, $rep['fields'], $item, $row, (array) ($files[$i] ?? []), $folder);
            if (($row['sort'] ?? '') === '') {
                $item->sort = $sort;
            }
            $sort++;
            $item->save();
        }
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && ! preg_match('~^(https?:)?//~', $path) && ! str_starts_with($path, '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
