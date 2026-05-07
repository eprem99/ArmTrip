<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Rental;
use App\Models\RentalType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalsListController extends Controller
{
    public function index(Request $request): View
    {
        $query = Rental::query()
            ->with([
                'type:id,name,slug',
                'location:id,name,slug',
                'images:id,rental_id,path,alt,sort_order,is_primary',
                'amenities:id,name,slug,icon',
            ])
            ->active()
            ->when($request->filled('q'), function (Builder $q) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $q->where(function (Builder $inner) use ($term): void {
                    $inner->where('title', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->when($request->filled('type'), fn (Builder $q) => $q->whereHas('type', fn (Builder $t) => $t->where('slug', (string) $request->query('type'))))
            ->when($request->filled('type_id'), fn (Builder $q) => $q->where('rental_type_id', (int) $request->query('type_id')))
            ->when($request->filled('location_id'), fn (Builder $q) => $q->byLocation((int) $request->query('location_id')))
            ->when($request->filled('price_min') || $request->filled('price_max'), fn (Builder $q) => $q->byPriceRange($request->query('price_min'), $request->query('price_max')))
            ->when($request->filled('guests'), fn (Builder $q) => $q->where('max_guests', '>=', (int) $request->query('guests')))
            ->when($request->filled('rating_min'), fn (Builder $q) => $q->where('rating_average', '>=', (float) $request->query('rating_min')));

        $amenities = $request->query('amenities');
        if ($amenities !== null && $amenities !== '') {
            $list = is_array($amenities) ? $amenities : explode(',', (string) $amenities);
            foreach ($list as $raw) {
                $raw = trim((string) $raw);
                if ($raw === '') {
                    continue;
                }
                $query->whereHas('amenities', fn (Builder $a) => $a->where('amenities.slug', $raw)->orWhere('amenities.id', ctype_digit($raw) ? (int) $raw : 0));
            }
        }

        $sort = (string) $request->query('sort', 'popularity');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'rating' => $query->orderByDesc('rating_average')->orderByDesc('ratings_count'),
            default => $query->orderByDesc('is_featured')->orderByDesc('rating_average')->orderByDesc('published_at')->orderByDesc('id'),
        };

        $rentals = $query->paginate(9)->withQueryString();

        $types = RentalType::query()
            ->withCount([
                'rentals as rentals_count' => fn (Builder $q) => $q->active(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $locations = Location::query()
            ->withCount([
                'rentals as rentals_count' => fn (Builder $q) => $q->active(),
            ])
            ->orderByDesc('rentals_count')
            ->orderBy('name')
            ->limit(60)
            ->get(['id', 'name', 'slug', 'kind', 'parent_id']);
        $amenitiesAll = Amenity::query()->orderBy('name')->get(['id', 'name', 'slug', 'icon']);

        return view('front.rentals-index', [
            'rentals' => $rentals,
            'types' => $types,
            'locations' => $locations,
            'amenitiesAll' => $amenitiesAll,
        ]);
    }
}
