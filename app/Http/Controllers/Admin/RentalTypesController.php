<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RentalTypesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->assertAdmin($request);

        $perPage = max(5, min(100, (int) $request->get('per_page', 50)));

        $query = RentalType::query()->orderBy('sort_order')->orderBy('name');

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
            'slug' => ['nullable', 'string', 'max:255', 'unique:rental_types,slug'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $this->uniqueRentalTypeSlug($data['name'], $data['slug'] ?? null);

        $type = RentalType::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return response()->json($type, 201);
    }

    public function update(Request $request, RentalType $type): JsonResponse
    {
        $this->assertAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('rental_types', 'slug')->ignore($type->id)],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $this->uniqueRentalTypeSlug($data['name'], $data['slug'] ?? null, $type->id);

        $type->fill([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        $type->save();

        return response()->json($type);
    }

    public function destroy(Request $request, RentalType $type): JsonResponse
    {
        $this->assertAdmin($request);

        if ($type->rentals()->exists()) {
            return response()->json([
                'message' => __('admin.rentals_types.delete_in_use'),
            ], 422);
        }

        $type->delete();

        return response()->json(null, 204);
    }

    private function assertAdmin(Request $request): void
    {
        if ($request->user()->type !== 'admin') {
            abort(403);
        }
    }

    private function uniqueRentalTypeSlug(string $name, ?string $slugInput, ?int $ignoreId = null): string
    {
        $slug = Str::slug(trim((string) $slugInput));
        if ($slug === '') {
            $slug = Str::slug($name);
        }
        if ($slug === '') {
            $slug = 'type';
        }

        $base = Str::limit($slug, 240, '');
        $candidate = $base;
        $i = 2;

        while (RentalType::query()
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
