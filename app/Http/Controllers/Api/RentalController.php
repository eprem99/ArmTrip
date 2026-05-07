<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRentalRequest;
use App\Http\Requests\Api\UpdateRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use App\Models\RentalImage;
use App\Models\RentalPrice;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::query()
            ->with([
                'type:id,name,slug',
                'location:id,name,slug,kind,parent_id',
                'images' => fn ($q) => $q->select(['id', 'rental_id', 'path', 'alt', 'sort_order', 'is_primary'])->orderBy('sort_order'),
                'amenities:id,name,slug,icon',
            ])
            ->active();

        $this->applyFilters($query, $request);

        if ($user = $request->user()) {
            $query->withExists([
                'favoritedBy as is_favorited' => function (Builder $q) use ($user): void {
                    $q->where('users.id', $user->id);
                },
            ]);
        }

        $sort = (string) $request->query('sort', 'published_desc');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'rating_desc' => $query->orderByDesc('rating_average')->orderByDesc('ratings_count'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        return RentalResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(Request $request, string $slug)
    {
        $rental = Rental::query()
            ->where('slug', $slug)
            ->with([
                'type:id,name,slug',
                'location:id,name,slug,kind,parent_id',
                'images' => fn ($q) => $q->select(['id', 'rental_id', 'path', 'alt', 'sort_order', 'is_primary'])->orderBy('sort_order'),
                'amenities:id,name,slug,icon',
            ])
            ->where(function (Builder $q) use ($request): void {
                $q->where(function (Builder $inner): void {
                    $inner->where('is_active', true)
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now());
                });

                if ($user = $request->user()) {
                    $q->orWhere('user_id', $user->id);
                }
            })
            ->firstOrFail();

        if ($user = $request->user()) {
            $rental->loadExists([
                'favoritedBy as is_favorited' => function (Builder $q) use ($user): void {
                    $q->where('users.id', $user->id);
                },
            ]);
        }

        return new RentalResource($rental);
    }

    public function store(StoreRentalRequest $request)
    {
        $this->authorize('create', Rental::class);

        $data = $request->validated();

        $rental = DB::transaction(function () use ($request, $data) {
            $rental = Rental::query()->create([
                'user_id' => $request->user()->id,
                'rental_type_id' => $data['rental_type_id'],
                'location_id' => $data['location_id'],
                'title' => $data['title'],
                'slug' => SlugGenerator::uniqueRentalSlug($data['title'], $data['slug'] ?? null),
                'description' => $data['description'] ?? null,
                'base_price' => $data['base_price'],
                'currency' => strtoupper($data['currency'] ?? 'USD'),
                'max_guests' => $data['max_guests'],
                'bedrooms' => $data['bedrooms'] ?? 0,
                'beds' => $data['beds'] ?? 0,
                'bathrooms' => $data['bathrooms'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'meta_title' => $data['meta_title'] ?? $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
                'published_at' => $data['published_at'] ?? now(),
            ]);

            if (! empty($data['amenity_ids'])) {
                $rental->amenities()->sync($data['amenity_ids']);
            }

            $this->syncImages($rental, $data['images'] ?? []);
            $this->syncPrices($rental, $data['prices'] ?? []);

            return $rental;
        });

        $rental->load([
            'type:id,name,slug',
            'location:id,name,slug,kind,parent_id',
            'images',
            'amenities:id,name,slug,icon',
        ]);

        return (new RentalResource($rental))->response()->setStatusCode(201);
    }

    public function update(UpdateRentalRequest $request, Rental $rental)
    {
        $data = $request->validated();

        DB::transaction(function () use ($rental, $data): void {
            $payload = Arr::only($data, [
                'title', 'description', 'rental_type_id', 'location_id', 'base_price', 'currency',
                'max_guests', 'bedrooms', 'beds', 'bathrooms', 'is_active', 'is_featured',
                'meta_title', 'meta_description', 'published_at',
            ]);

            if (array_key_exists('currency', $payload) && $payload['currency'] !== null) {
                $payload['currency'] = strtoupper((string) $payload['currency']);
            }

            if ($payload !== []) {
                $rental->fill($payload);
            }

            if (array_key_exists('slug', $data)) {
                $rental->slug = SlugGenerator::uniqueRentalSlug(
                    $data['title'] ?? $rental->title,
                    $data['slug'],
                    $rental->id
                );
            }

            if (array_key_exists('meta_title', $data) && $data['meta_title'] === null && array_key_exists('title', $data)) {
                $rental->meta_title = $data['title'];
            }

            if ($rental->isDirty()) {
                $rental->save();
            }

            if (array_key_exists('amenity_ids', $data)) {
                $rental->amenities()->sync($data['amenity_ids'] ?? []);
            }

            if (array_key_exists('images', $data)) {
                $rental->images()->delete();
                $this->syncImages($rental, $data['images'] ?? []);
            }

            if (array_key_exists('prices', $data)) {
                RentalPrice::query()->where('rental_id', $rental->id)->delete();
                $this->syncPrices($rental, $data['prices'] ?? []);
            }
        });

        $rental->refresh()->load([
            'type:id,name,slug',
            'location:id,name,slug,kind,parent_id',
            'images',
            'amenities:id,name,slug,icon',
        ]);

        return new RentalResource($rental);
    }

    public function destroy(Request $request, Rental $rental)
    {
        $this->authorize('delete', $rental);

        $rental->delete();

        return response()->json(null, 204);
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('location_id')) {
            $query->byLocation((int) $request->query('location_id'));
        }

        if ($request->filled('rental_type_id')) {
            $query->where('rental_type_id', (int) $request->query('rental_type_id'));
        }

        if ($request->filled('rental_type_slug')) {
            $slug = (string) $request->query('rental_type_slug');
            $query->whereHas('type', fn (Builder $q) => $q->where('slug', $slug));
        }

        $query->byPriceRange(
            $request->query('price_min'),
            $request->query('price_max')
        );

        if ($request->filled('guests')) {
            $query->where('max_guests', '>=', (int) $request->query('guests'));
        }

        $amenities = $request->query('amenities');
        if ($amenities !== null && $amenities !== '') {
            $list = is_array($amenities) ? $amenities : explode(',', (string) $amenities);
            foreach ($list as $raw) {
                $raw = trim((string) $raw);
                if ($raw === '') {
                    continue;
                }

                if (ctype_digit($raw)) {
                    $query->whereHas('amenities', fn (Builder $q) => $q->where('amenities.id', (int) $raw));
                } else {
                    $query->whereHas('amenities', fn (Builder $q) => $q->where('amenities.slug', $raw));
                }
            }
        }

        if ($request->filled('rating_min')) {
            $query->where('rating_average', '>=', (float) $request->query('rating_min'));
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $images
     */
    private function syncImages(Rental $rental, array $images): void
    {
        foreach ($images as $row) {
            RentalImage::query()->create([
                'rental_id' => $rental->id,
                'path' => $row['path'],
                'alt' => $row['alt'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
                'is_primary' => $row['is_primary'] ?? false,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $prices
     */
    private function syncPrices(Rental $rental, array $prices): void
    {
        foreach ($prices as $row) {
            RentalPrice::query()->create([
                'rental_id' => $rental->id,
                'date' => $row['date'],
                'price' => $row['price'],
                'currency' => isset($row['currency']) ? strtoupper((string) $row['currency']) : null,
            ]);
        }
    }
}
