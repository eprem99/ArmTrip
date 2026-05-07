<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'rental_type_id',
        'location_id',
        'title',
        'slug',
        'description',
        'base_price',
        'currency',
        'max_guests',
        'bedrooms',
        'beds',
        'bathrooms',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'rating_average',
        'ratings_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'max_guests' => 'integer',
            'bedrooms' => 'integer',
            'beds' => 'integer',
            'bathrooms' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'rating_average' => 'decimal:2',
            'ratings_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(RentalType::class, 'rental_type_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RentalImage::class)->orderBy('sort_order');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(RentalPrice::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'rental_amenity')->withTimestamps();
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLocation(Builder $query, Location|int $location): Builder
    {
        $locationModel = $location instanceof Location
            ? $location
            : Location::query()->findOrFail($location);

        $ids = $locationModel->subtreeLocationIds();

        return $query->whereIn('location_id', $ids);
    }

    public function scopeByPriceRange(Builder $query, ?string $min, ?string $max): Builder
    {
        if ($min !== null && $min !== '') {
            $query->where('base_price', '>=', $min);
        }

        if ($max !== null && $max !== '') {
            $query->where('base_price', '<=', $max);
        }

        return $query;
    }

    public function priceForDate(Carbon|string $date): string
    {
        $d = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        $override = $this->relationLoaded('prices')
            ? $this->prices->firstWhere('date', $d)
            : $this->prices()->where('date', $d)->first();

        if ($override !== null) {
            return (string) $override->price;
        }

        return (string) $this->base_price;
    }

    public function syncRatingAggregate(): void
    {
        $row = Review::query()
            ->where('rental_id', $this->id)
            ->selectRaw('AVG(rating) as rating_avg, COUNT(*) as rating_cnt')
            ->first();

        $avg = round((float) ($row->rating_avg ?? 0), 2);
        $cnt = (int) ($row->rating_cnt ?? 0);

        static::withoutEvents(function () use ($avg, $cnt): void {
            $this->forceFill([
                'rating_average' => $avg,
                'ratings_count' => $cnt,
            ])->saveQuietly();
        });
    }
}
