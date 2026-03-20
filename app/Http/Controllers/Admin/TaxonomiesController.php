<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoContent;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TaxonomiesController extends Controller
{
    public function index(): JsonResponse
    {
        $taxonomies = Taxonomy::query()
            ->withCount('terms')
            ->orderBy('name')
            ->get();

        return response()->json($taxonomies);
    }

    public function show(Taxonomy $taxonomy): JsonResponse
    {
        $taxonomy->load([
            'terms' => function ($query) {
                $query->with('parent:id,name,slug')
                    ->orderBy('name');
            },
        ]);

        $payload = $taxonomy->toArray();
        $seo = SeoContent::query()
            ->where('type', 'taxonomy')
            ->where('post_id', $taxonomy->id)
            ->first();
        $payload['seo_title'] = $seo?->title ?? '';
        $payload['seo_description'] = $seo?->description ?? '';

        return response()->json($payload);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(Taxonomy::TYPES)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:64', 'regex:/^[a-z0-9-]+$/'],
            'image' => ['nullable', 'string', 'max:65535'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:65535'],
        ]);

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['name']);
        }
        $slug = $this->uniqueTaxonomySlug($slug);

        $taxonomy = Taxonomy::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'image' => isset($validated['image']) ? trim((string) $validated['image']) ?: null : null,
        ]);
        $taxonomy->loadCount('terms');

        $this->syncTaxonomySeo($taxonomy, $validated);

        $payload = $taxonomy->toArray();
        $seo = SeoContent::query()
            ->where('type', 'taxonomy')
            ->where('post_id', $taxonomy->id)
            ->first();
        $payload['seo_title'] = $seo?->title ?? '';
        $payload['seo_description'] = $seo?->description ?? '';

        return response()->json($payload, 201);
    }

    public function update(Request $request, Taxonomy $taxonomy): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('taxonomies', 'slug')->ignore($taxonomy->id),
            ],
            'type' => ['required', Rule::in(Taxonomy::TYPES)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:64', 'regex:/^[a-z0-9-]+$/'],
            'image' => ['nullable', 'string', 'max:65535'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:65535'],
        ]);

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['name']);
        }
        if (Taxonomy::query()->where('slug', $slug)->where('id', '!=', $taxonomy->id)->exists()) {
            $slug = $this->uniqueTaxonomySlug($slug, $taxonomy->id);
        }

        $taxonomy->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'icon' => array_key_exists('icon', $validated)
                ? (($validated['icon'] ?? '') !== '' ? $validated['icon'] : null)
                : $taxonomy->icon,
            'image' => array_key_exists('image', $validated)
                ? (trim((string) $validated['image']) ?: null)
                : $taxonomy->image,
        ]);
        $taxonomy->loadCount('terms');

        $this->syncTaxonomySeo($taxonomy, $validated);

        $payload = $taxonomy->toArray();
        $seo = SeoContent::query()
            ->where('type', 'taxonomy')
            ->where('post_id', $taxonomy->id)
            ->first();
        $payload['seo_title'] = $seo?->title ?? '';
        $payload['seo_description'] = $seo?->description ?? '';

        return response()->json($payload);
    }

    public function destroy(Taxonomy $taxonomy): JsonResponse
    {
        $taxonomy->delete();

        return response()->json(['ok' => true]);
    }

    private function uniqueTaxonomySlug(string $base, ?int $ignoreId = null): string
    {
        $candidate = $base;
        $i = 2;
        while (Taxonomy::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = Str::limit($base, 240, '').'-'.$i;
            $i++;
            if ($i > 1000) {
                break;
            }
        }

        return $candidate;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncTaxonomySeo(Taxonomy $taxonomy, array $validated): void
    {
        if (! array_key_exists('seo_title', $validated) && ! array_key_exists('seo_description', $validated)) {
            return;
        }

        $title = array_key_exists('seo_title', $validated)
            ? trim((string) ($validated['seo_title'] ?? ''))
            : '';
        $description = array_key_exists('seo_description', $validated)
            ? trim((string) ($validated['seo_description'] ?? ''))
            : '';

        if ($title === '' && $description === '') {
            SeoContent::query()
                ->where('type', 'taxonomy')
                ->where('post_id', $taxonomy->id)
                ->delete();

            return;
        }

        SeoContent::query()->updateOrCreate(
            [
                'type' => 'taxonomy',
                'post_id' => $taxonomy->id,
            ],
            [
                'title' => $title,
                'description' => $description === '' ? null : $description,
            ],
        );
    }
}
