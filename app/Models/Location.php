<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    public const KIND_COUNTRY = 'country';

    public const KIND_CITY = 'city';

    public const KIND_DISTRICT = 'district';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'kind',
        'path',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Location $model): void {
            $model->rebuildMaterializedPath();
        });

        static::updated(function (Location $model): void {
            if ($model->wasChanged('parent_id')) {
                $model->rebuildMaterializedPath();
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function rebuildMaterializedPath(): void
    {
        $this->loadMissing('parent');
        $parent = $this->parent;
        $parentPath = trim((string) ($parent?->path ?? ''), '/');
        $newPath = '/'.($parentPath === '' ? (string) $this->id : $parentPath.'/'.(string) $this->id).'/';

        if ($this->path === $newPath) {
            foreach ($this->children as $child) {
                $child->rebuildMaterializedPath();
            }

            return;
        }

        static::withoutEvents(function () use ($newPath): void {
            $this->forceFill(['path' => $newPath])->saveQuietly();
        });

        foreach ($this->children as $child) {
            $child->rebuildMaterializedPath();
        }
    }

    /** @return array<int, int> */
    public function subtreeLocationIds(): array
    {
        if ($this->path === null || $this->path === '') {
            return [$this->id];
        }

        return static::query()
            ->where('path', 'like', $this->path.'%')
            ->pluck('id')
            ->all();
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
