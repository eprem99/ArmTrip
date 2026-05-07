<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->type !== 'admin') {
            abort(403);
        }

        $perPage = max(5, min(100, (int) $request->get('per_page', 15)));

        $query = Rental::query()
            ->with([
                'type:id,name,slug',
                'location:id,name,slug,kind,parent_id',
                'owner:id,name,email',
            ])
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = '%'.$request->get('search').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        return RentalResource::collection($query->paginate($perPage));
    }
}
