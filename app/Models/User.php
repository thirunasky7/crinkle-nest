<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
        'is_admin' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class)->orderByDesc('is_default')->orderBy('id');
    }

    public function fullName(): string
    {
        return trim($this->name.' '.$this->last_name);
    }

    public function pref(string $key, $default = null)
    {
        return data_get($this->preferences ?? [], $key, $default);
    }
}
