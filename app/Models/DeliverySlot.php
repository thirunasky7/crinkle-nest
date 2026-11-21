<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model
{
    protected $guarded = [];

    protected $casts = ['is_custom' => 'boolean', 'is_active' => 'boolean'];

    public function fullLabel(): string
    {
        return trim($this->day_label.' - '.$this->title.($this->time_range ? ' ('.$this->time_range.')' : ''));
    }
}
