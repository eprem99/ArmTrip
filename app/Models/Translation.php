<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    public const TYPE_PAGE = 'page';

    public const TYPE_POST = 'post';

    public const TYPE_TERM = 'term';

    public const TYPE_TAXONOMY = 'taxonomy';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_PAGE,
        self::TYPE_POST,
        self::TYPE_TERM,
        self::TYPE_TAXONOMY,
    ];

    protected $fillable = [
        'translation_group_id',
        'content_id',
        'language_id',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'content_id');
    }
}
