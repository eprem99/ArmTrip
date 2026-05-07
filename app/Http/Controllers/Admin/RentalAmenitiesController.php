<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RentalAmenitiesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->assertAdmin($request);

        $perPage = max(5, min(100, (int) $request->get('per_page', 50)));

        $query = Amenity::query()->orderBy('name');

        if ($request->filled('search')) {
            $term = '%'.$request->get('search').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $this->assertAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:amenities,slug'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = $this->uniqueAmenitySlug($data['name'], $data['slug'] ?? null);

        $amenity = Amenity::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'icon' => $data['icon'] ?? null,
        ]);

        return response()->json($amenity, 201);
    }

    public function update(Request $request, Amenity $amenity): JsonResponse
    {
        $this->assertAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('amenities', 'slug')->ignore($amenity->id)],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = $this->uniqueAmenitySlug($data['name'], $data['slug'] ?? null, $amenity->id);

        $amenity->fill([
            'name' => $data['name'],
            'slug' => $slug,
            'icon' => $data['icon'] ?? null,
        ]);
        $amenity->save();

        return response()->json($amenity);
    }

    public function destroy(Request $request, Amenity $amenity): JsonResponse
    {
        $this->assertAdmin($request);

        $amenity->delete();

        return response()->json(null, 204);
    }

    private function assertAdmin(Request $request): void
    {
        if ($request->user()->type !== 'admin') {
            abort(403);
        }
    }

    private function uniqueAmenitySlug(string $name, ?string $slugInput, ?int $ignoreId = null): string
    {
        $slug = Str::slug(trim((string) $slugInput));
        if ($slug === '') {
            $slug = Str::slug($name);
        }
        if ($slug === '') {
            $slug = 'amenity';
        }

        $base = Str::limit($slug, 240, '');
        $candidate = $base;
        $i = 2;

        while (Amenity::query()
            ->withTrashed()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = Str::limit($base, 240, '').'-'.$i;
            $i++;
            if ($i > 10000) {
                break;
            }
        }

        return $candidate;
    }
}
