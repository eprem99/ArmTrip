<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    protected $fillable = [
        'parent_id',
        'slug',
        'title',
        'content',
        'excerpt',
        'template',
        'featured_image',
        'status',
        'is_home',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_home' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Page $page) {
            Translation::query()
                ->where('type', Translation::TYPE_PAGE)
                ->where('content_id', $page->id)
                ->delete();
        });
    }

    /**
     * Translation row for this page (type=page, content_id = pages.id).
     */
    public function translation(): HasOne
    {
        return $this->hasOne(Translation::class, 'content_id')
            ->where('type', Translation::TYPE_PAGE);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
