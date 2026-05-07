<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\View\View;

class RentalSeoController extends Controller
{
    public function show(string $typeSlug, string $locationSlug, string $slug): View
    {
        $rental = Rental::query()
            ->where('slug', $slug)
            ->whereHas('type', fn ($q) => $q->where('slug', $typeSlug))
            ->whereHas('location', fn ($q) => $q->where('slug', $locationSlug))
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with([
                'type:id,name,slug',
                'location:id,name,slug,kind,parent_id',
                'images' => fn ($q) => $q->select(['id', 'rental_id', 'path', 'alt', 'sort_order', 'is_primary'])->orderBy('sort_order'),
                'amenities:id,name,slug,icon',
            ])
            ->firstOrFail();

        return view('front.rental', [
            'rental' => $rental,
        ]);
    }
}
