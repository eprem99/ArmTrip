<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Favorite;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::query()
            ->whereHas('favoritedBy', fn (Builder $q) => $q->where('users.id', $request->user()->id))
            ->with([
                'type:id,name,slug',
                'location:id,name,slug,kind,parent_id',
                'images' => fn ($q) => $q->select(['id', 'rental_id', 'path', 'alt', 'sort_order', 'is_primary'])->orderBy('sort_order'),
                'amenities:id,name,slug,icon',
            ])
            ->active()
            ->orderByDesc('published_at');

        $query->withExists([
            'favoritedBy as is_favorited' => function (Builder $q) use ($request): void {
                $q->where('users.id', $request->user()->id);
            },
        ]);

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        return RentalResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rental_id' => ['required', 'integer', Rule::exists('rentals', 'id')],
        ]);

        $rental = Rental::query()->findOrFail($data['rental_id']);

        Favorite::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'rental_id' => $rental->id,
        ]);

        return response()->json(['ok' => true], 201);
    }

    public function destroy(Request $request, Rental $rental)
    {
        Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('rental_id', $rental->id)
            ->delete();

        return response()->json(null, 204);
    }
}
