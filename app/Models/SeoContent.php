<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoContent extends Model
{
    protected $table = 'seo_content';

    public $timestamps = true;

    protected $fillable = [
        'post_id',
        'type',
        'title',
        'description',
    ];

    /** When type=taxonomy, post_id references taxonomies.id */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'post_id');
    }
}
