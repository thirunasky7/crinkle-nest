<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $guarded = [];

    protected $casts = ['whatsapp_copy' => 'boolean', 'is_read' => 'boolean', 'event_date' => 'date'];
}
