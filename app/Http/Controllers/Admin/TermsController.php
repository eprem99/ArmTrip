<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoContent;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TermsController extends Controller
{
    public function show(Taxonomy $taxonomy, Term $term): JsonResponse
    {
        abort_if((int) $term->taxonomy_id !== (int) $taxonomy->id, 404);

        return response()->json($this->termToApiArray($term));
    }

    public function checkSlug(Request $request, Taxonomy $taxonomy): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        $slug = trim((string) $validated['slug']);
        $ignoreId = $validated['ignore_id'] ?? null;

        if ($slug === '') {
            return response()->json([
                'available' => false,
                'slug' => '',
                'suggested' => '',
            ]);
        }

        $taken = $this->slugTakenInTaxonomy($taxonomy->id, $slug, $ignoreId);

        if (! $taken) {
            return response()->json([
                'available' => true,
                'slug' => $slug,
                'suggested' => $slug,
            ]);
        }

        $suggested = $this->uniqueSlugInTaxonomy($taxonomy->id, $slug, $ignoreId);

        return response()->json([
            'available' => false,
            'slug' => $slug,
            'suggested' => $suggested,
        ]);
    }

    public function store(Request $request, Taxonomy $taxonomy): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:terms,id'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'string', 'max:65535'],
            'status' => ['nullable', 'string', Rule::in(Term::STATUSES)],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:65535'],
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'translation_group_id' => ['nullable', 'uuid'],
        ]);

        $languageId = (int) $validated['language_id'];
        $groupId = isset($validated['translation_group_id'])
            ? trim((string) $validated['translation_group_id'])
            : '';

        if ($groupId !== '') {
            $dup = Translation::query()
                ->where('translation_group_id', $groupId)
                ->where('language_id', $languageId)
                ->where('type', Translation::TYPE_TERM)
                ->exists();
            if ($dup) {
                return response()->json(['message' => 'A translation for this language already exists in the group.'], 422);
            }
        } else {
            $groupId = (string) Str::uuid();
        }

        if (! empty($validated['parent_id'])) {
            $parent = Term::query()->where('id', $validated['parent_id'])->firstOrFail();
            abort_if((int) $parent->taxonomy_id !== (int) $taxonomy->id, 422, 'Parent term must belong to this taxonomy.');
            $parentT = $parent->translation;
            if (! $parentT || (int) $parentT->language_id !== $languageId) {
                return response()->json(['message' => 'Parent term must use the same language.'], 422);
            }
        }

        unset($validated['language_id'], $validated['translation_group_id']);

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['name']);
        }

        $slug = $this->uniqueSlugInTaxonomy($taxonomy->id, $slug);

        $term = DB::transaction(function () use ($taxonomy, $validated, $slug, $groupId, $languageId) {
            $term = $taxonomy->terms()->create([
                'name' => $validated['name'],
                'slug' => $slug,
                'parent_id' => $validated['parent_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'short_description' => isset($validated['short_description'])
                    ? (trim((string) ($validated['short_description'] ?? '')) ?: null)
                    : null,
                'image' => isset($validated['image']) ? trim((string) $validated['image']) ?: null : null,
                'status' => $validated['status'] ?? Term::STATUS_PUBLISHED,
            ]);

            Translation::create([
                'translation_group_id' => $groupId,
                'content_id' => $term->id,
                'language_id' => $languageId,
                'type' => Translation::TYPE_TERM,
            ]);

            return $term;
        });

        $this->syncTermSeo($term, $validated);

        return response()->json($this->termToApiArray($term->fresh()), 201);
    }

    public function update(Request $request, Taxonomy $taxonomy, Term $term): JsonResponse
    {
        abort_if((int) $term->taxonomy_id !== (int) $taxonomy->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:terms,id'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'string', 'max:65535'],
            'status' => ['nullable', 'string', Rule::in(Term::STATUSES)],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:65535'],
        ]);

        $term->load('translation');
        $languageId = (int) ($term->translation?->language_id ?? 0);

        if (! empty($validated['parent_id'])) {
            abort_if((int) $validated['parent_id'] === (int) $term->id, 422, 'A term cannot be its own parent.');
            $parent = Term::query()->where('id', $validated['parent_id'])->firstOrFail();
            abort_if((int) $parent->taxonomy_id !== (int) $taxonomy->id, 422, 'Parent term must belong to this taxonomy.');
            $parentT = $parent->translation;
            if (! $parentT || (int) $parentT->language_id !== $languageId) {
                return response()->json(['message' => 'Parent term must use the same language.'], 422);
            }
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
            'short_description' => array_key_exists('short_description', $validated)
                ? (trim((string) ($validated['short_description'] ?? '')) ?: null)
                : $term->short_description,
            'image' => array_key_exists('image', $validated)
                ? (trim((string) ($validated['image'] ?? '')) ?: null)
                : $term->image,
            'status' => $validated['status'] ?? $term->status,
        ]);

        $this->syncTermSeo($term, $validated);

        return response()->json($this->termToApiArray($term->fresh()));
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

    /**
     * @return array<string, mixed>
     */
    private function termToApiArray(Term $term): array
    {
        $term->load(['parent:id,name,slug', 'translation.language']);
        $payload = $term->toArray();
        $seo = SeoContent::query()
            ->where('type', 'term')
            ->where('post_id', $term->id)
            ->first();
        $payload['seo_title'] = $seo?->title ?? '';
        $payload['seo_description'] = $seo?->description ?? '';
        $tr = $term->translation;
        $payload['language_id'] = $tr?->language_id;
        $payload['translation_group_id'] = $tr?->translation_group_id;
        $payload['language'] = $tr?->language;

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncTermSeo(Term $term, array $validated): void
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
                ->where('type', 'term')
                ->where('post_id', $term->id)
                ->delete();

            return;
        }

        SeoContent::query()->updateOrCreate(
            [
                'type' => 'term',
                'post_id' => $term->id,
            ],
            [
                'title' => $title,
                'description' => $description === '' ? null : $description,
            ],
        );
    }
}
