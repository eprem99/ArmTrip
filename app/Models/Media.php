<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'filename',
        'title',
        'mime_type',
        'size',
        'alt',
        'caption',
        'created_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        $disk = $this->disk ?: 'public';
        $path = $this->path ?? '';
        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            return rtrim(config('app.url', ''), '/').'/storage/uploads/'.ltrim($path, '/');
        }
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }
}
