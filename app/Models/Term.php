<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Term extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    /** @var list<string> */
    public const STATUSES = [self::STATUS_DRAFT, self::STATUS_PUBLISHED];

    protected $fillable = [
        'taxonomy_id',
        'name',
        'slug',
        'parent_id',
        'description',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Term $term) {
            if (empty($term->slug)) {
                $term->slug = Str::slug($term->name);
            }
        });

        static::updating(function (Term $term) {
            if ($term->isDirty('name') && empty($term->slug)) {
                $term->slug = Str::slug($term->name);
            }
        });
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id');
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'termable');
    }

    /**
     * @param  Builder<Term>  $query
     * @return Builder<Term>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }
}
