<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
