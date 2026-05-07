<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Taxonomy extends Model
{
    use HasFactory;

    public const TYPE_CATEGORY = 'category';

    public const TYPE_TAG = 'tag';

    /** @var list<string> */
    public const TYPES = [self::TYPE_CATEGORY, self::TYPE_TAG];

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'icon',
        'image',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Taxonomy>  $query
     * @return Builder<Taxonomy>
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }

    /**
     * SEO row for this taxonomy (type=taxonomy in seo_content).
     *
     * @return HasOne<SeoContent, Taxonomy>
     */
    public function seoContent(): HasOne
    {
        return $this->hasOne(SeoContent::class, 'post_id')
            ->where('type', 'taxonomy');
    }

    protected static function booted(): void
    {
        static::creating(function (Taxonomy $taxonomy) {
            if (empty($taxonomy->slug)) {
                $taxonomy->slug = Str::slug($taxonomy->name);
            }
        });

        static::updating(function (Taxonomy $taxonomy) {
            if ($taxonomy->isDirty('name') && empty($taxonomy->slug)) {
                $taxonomy->slug = Str::slug($taxonomy->name);
            }
        });

        static::deleting(function (Taxonomy $taxonomy) {
            Translation::query()
                ->where('type', Translation::TYPE_TAXONOMY)
                ->where('content_id', $taxonomy->id)
                ->delete();

            SeoContent::query()
                ->where('type', 'taxonomy')
                ->where('post_id', $taxonomy->id)
                ->delete();
        });
    }

    /**
     * Translation row for this taxonomy (type=taxonomy, content_id = taxonomies.id).
     *
     * @return HasOne<Translation, Taxonomy>
     */
    public function translation(): HasOne
    {
        return $this->hasOne(Translation::class, 'content_id')
            ->where('type', Translation::TYPE_TAXONOMY);
    }
}
