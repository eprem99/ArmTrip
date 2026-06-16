<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'hero_title',
        'slug',
        'parent_id',
        'description',
        'short_description',
        'status',
        'image',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Term $term) {
            Translation::query()
                ->where('type', Translation::TYPE_TERM)
                ->where('content_id', $term->id)
                ->delete();
        });

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

        static::deleting(function (Term $term) {
            SeoContent::query()
                ->where('type', 'term')
                ->where('post_id', $term->id)
                ->delete();
        });
    }

    /**
     * SEO meta (type=term in seo_content, post_id = term id).
     *
     * @return HasOne<SeoContent, Term>
     */
    public function seoContent(): HasOne
    {
        return $this->hasOne(SeoContent::class, 'post_id')
            ->where('type', 'term');
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
     * Translation row for this term (type=term, content_id = terms.id).
     *
     * @return HasOne<Translation, Term>
     */
    public function translation(): HasOne
    {
        return $this->hasOne(Translation::class, 'content_id')
            ->where('type', Translation::TYPE_TERM);
    }

    /**
     * @param  Builder<Term>  $query
     * @return Builder<Term>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function heroHeading(): string
    {
        $title = trim((string) ($this->hero_title ?? ''));

        return $title !== '' ? $title : (string) $this->name;
    }

    public function frontendUrl(): string
    {
        $this->loadMissing(['taxonomy', 'parent']);

        if ($this->parent_id && $this->parent) {
            return route('front.taxonomy.term.nested', [
                'taxonomySlug' => $this->taxonomy->slug,
                'parentSlug' => $this->parent->slug,
                'termSlug' => $this->slug,
            ]);
        }

        return route('front.taxonomy.term', [
            'taxonomySlug' => $this->taxonomy->slug,
            'termSlug' => $this->slug,
        ]);
    }
}
