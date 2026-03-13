<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Option extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'option_name',
        'option_value',
    ];

    /**
     * Get option value by name.
     */
    public static function get(string $name, mixed $default = null): mixed
    {
        $option = static::where('option_name', $name)->first();

        return $option !== null ? $option->option_value : $default;
    }

    /**
     * Set option value by name.
     */
    public static function set(string $name, mixed $value): void
    {
        static::updateOrInsert(
            ['option_name' => $name],
            ['option_value' => is_string($value) ? $value : json_encode($value)]
        );
    }
}
