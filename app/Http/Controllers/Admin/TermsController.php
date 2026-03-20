<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TermsController extends Controller
{
    public function show(Taxonomy $taxonomy, Term $term): JsonResponse
    {
        abort_if((int) $term->taxonomy_id !== (int) $taxonomy->id, 404);

        $term->load('parent:id,name,slug');

        return response()->json($term);
    }

    public function store(Request $request, Taxonomy $taxonomy): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:terms,id'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in(Term::STATUSES)],
        ]);

        if (! empty($validated['parent_id'])) {
            $parent = Term::query()->where('id', $validated['parent_id'])->firstOrFail();
            abort_if((int) $parent->taxonomy_id !== (int) $taxonomy->id, 422, 'Parent term must belong to this taxonomy.');
        }

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['name']);
        }

        $slug = $this->uniqueSlugInTaxonomy($taxonomy->id, $slug);

        $term = $taxonomy->terms()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? Term::STATUS_PUBLISHED,
        ]);

        $term->load('parent:id,name,slug');

        return response()->json($term, 201);
    }

    public function update(Request $request, Taxonomy $taxonomy, Term $term): JsonResponse
    {
        abort_if((int) $term->taxonomy_id !== (int) $taxonomy->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:terms,id'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in(Term::STATUSES)],
        ]);

        if (! empty($validated['parent_id'])) {
            abort_if((int) $validated['parent_id'] === (int) $term->id, 422, 'A term cannot be its own parent.');
            $parent = Term::query()->where('id', $validated['parent_id'])->firstOrFail();
            abort_if((int) $parent->taxonomy_id !== (int) $taxonomy->id, 422, 'Parent term must belong to this taxonomy.');
        }

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['name']);
        }

        $slug = $this->uniqueSlugInTaxonomy($taxonomy->id, $slug, $term->id);

        $term->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? $term->status,
        ]);

        $term->load('parent:id,name,slug');

        return response()->json($term);
    }

    public function destroy(Taxonomy $taxonomy, Term $term): JsonResponse
    {
        abort_if((int) $term->taxonomy_id !== (int) $taxonomy->id, 404);

        $term->delete();

        return response()->json(['ok' => true]);
    }

    private function uniqueSlugInTaxonomy(int $taxonomyId, string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'term';
        $candidate = $base;
        $i = 2;
        while ($this->slugTakenInTaxonomy($taxonomyId, $candidate, $ignoreId)) {
            $candidate = Str::limit($base, 240, '').'-'.$i;
            $i++;
            if ($i > 1000) {
                break;
            }
        }

        return $candidate;
    }

    private function slugTakenInTaxonomy(int $taxonomyId, string $slug, ?int $ignoreId = null): bool
    {
        $q = Term::query()->where('taxonomy_id', $taxonomyId)->where('slug', $slug);
        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        }

        return $q->exists();
    }
}
