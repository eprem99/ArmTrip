<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Post $post) {
            $post->terms()->detach();

            Translation::query()
                ->where('type', Translation::TYPE_POST)
                ->where('content_id', $post->id)
                ->delete();
        });

        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function (Post $post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'termable');
    }

    /**
     * Translation row for this post (type=post, content_id = posts.id).
     *
     * @return HasOne<Translation, Post>
     */
    public function translation(): HasOne
    {
        return $this->hasOne(Translation::class, 'content_id')
            ->where('type', Translation::TYPE_POST);
    }
}
