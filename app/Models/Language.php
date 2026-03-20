<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'lcode',
        'name',
        'native_name',
        'locale',
        'direction',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
