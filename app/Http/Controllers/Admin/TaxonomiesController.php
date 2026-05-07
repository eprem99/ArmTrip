<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\SeoContent;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TaxonomiesController extends Controller
{
    /**
     * Grouped taxonomies for the current UI language (settings list + translation shortcuts).
     */
    public function index(Request $request): JsonResponse
    {
        $lcode = (string) $request->get('lang', 'en');
        $lang = Language::query()->where('lcode', $lcode)->first()
            ?? Language::query()->orderBy('id')->firstOrFail();

        $taxonomyIds = Translation::query()
            ->where('type', Translation::TYPE_TAXONOMY)
            ->where('language_id', $lang->id)
            ->pluck('content_id');

        if ($taxonomyIds->isEmpty()) {
            return response()->json([]);
        }

        $taxonomies = Taxonomy::query()
            ->whereIn('id', $taxonomyIds)
            ->with(['translation.language'])
            ->withCount('terms')
            ->orderBy('name')
            ->get();

        $groupIds = $taxonomies
            ->map(fn (Taxonomy $t) => $t->translation?->translation_group_id)
            ->filter()
            ->unique()
            ->values();

        if ($groupIds->isEmpty()) {
            return response()->json([]);
        }

        $allInGroups = Translation::query()
            ->where('type', Translation::TYPE_TAXONOMY)
            ->whereIn('translation_group_id', $groupIds)
            ->with('language:id,lcode,native_name,name')
            ->get()
            ->groupBy('translation_group_id');

        $allContentIds = $allInGroups->flatten()->pluck('content_id')->unique()->values();
        $slugById = Taxonomy::query()->whereIn('id', $allContentIds)->pluck('slug', 'id');

        $result = $taxonomies->map(function (Taxonomy $taxonomy) use ($allInGroups, $slugById) {
            $tg = $taxonomy->translation?->translation_group_id;
            if (! $tg) {
                return null;
            }
            $siblings = $allInGroups->get($tg, collect())->values();

            return [
                'translation_group_id' => $tg,
                'taxonomy' => $taxonomy,
                'translations' => $siblings->map(fn (Translation $tr) => [
                    'taxonomy_id' => $tr->content_id,
                    'slug' => (string) ($slugById[$tr->content_id] ?? ''),
                    'language_id' => $tr->language_id,
                    'lcode' => $tr->language?->lcode ?? '',
                    'label' => $tr->language?->native_name ?? $tr->language?->name ?? $tr->language?->lcode ?? '',
                ])->values()->all(),
            ];
        })->filter()->values();

        return response()->json($result);
    }

    public function show(Request $request, Taxonomy $taxonomy): JsonResponse
    {
        $lcode = (string) $request->get('lang', 'en');
        $lang = Language::query()->where('lcode', $lcode)->first()
            ?? Language::query()->orderBy('id')->firstOrFail();

        $taxonomy->load([
            'terms' => function ($query) {
                $query->with(['parent:id,name,slug', 'translation.language'])
                    ->orderBy('name');
            },
        ]);

        /** @var Collection<int, Term> $allTerms */
        $allTerms = $taxonomy->terms;

        $termsForLang = $allTerms->filter(
            fn (Term $t) => (int) ($t->translation?->language_id ?? 0) === (int) $lang->id
        )->values();

        $groupIds = $termsForLang
            ->map(fn (Term $t) => $t->translation?->translation_group_id)
            ->filter()
            ->unique()
            ->values();

        $allInGroups = collect();
        if ($groupIds->isNotEmpty()) {
            $allInGroups = Translation::query()
                ->where('type', Translation::TYPE_TERM)
                ->whereIn('translation_group_id', $groupIds)
                ->with('language:id,lcode,native_name,name')
                ->get()
                ->groupBy('translation_group_id');
        }

        $termGroups = $termsForLang->map(function (Term $term) use ($allInGroups) {
            $tg = $term->translation?->translation_group_id;
            if (! $tg) {
                return null;
            }
            $siblings = $allInGroups->get($tg, collect())->values();

            return [
                'translation_group_id' => $tg,
                'term' => $term,
                'translations' => $siblings->map(fn (Translation $tr) => [
                    'term_id' => $tr->content_id,
                    'language_id' => $tr->language_id,
                    'lcode' => $tr->language?->lcode ?? '',
                    'label' => $tr->language?->native_name ?? $tr->language?->name ?? $tr->language?->lcode ?? '',
                ])->values()->all(),
            ];
        })->filter()->values();

        $payload = $taxonomy->toArray();
        $payload['terms'] = $termsForLang->map(fn (Term $t) => $t->toArray())->all();
        $payload['term_groups'] = $termGroups->all();

        $seo = SeoContent::query()
            ->where('type', 'taxonomy')
            ->where('post_id', $taxonomy->id)
            ->first();
        $payload['seo_title'] = $seo?->title ?? '';
        $payload['seo_description'] = $seo?->description ?? '';

        $taxonomy->loadMissing('translation.language');
        $tr = $taxonomy->translation;
        $payload['language_id'] = $tr?->language_id;
        $payload['translation_group_id'] = $tr?->translation_group_id;
        $payload['language'] = $tr?->language;

        return response()->json($payload);
    }

    public function checkSlug(Request $request): JsonResponse
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

        $taken = Taxonomy::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();

        if (! $taken) {
            return response()->json([
                'available' => true,
                'slug' => $slug,
                'suggested' => $slug,
            ]);
        }

        $suggested = $this->uniqueTaxonomySlug($slug, $ignoreId);

        return response()->json([
            'available' => false,
            'slug' => $slug,
            'suggested' => $suggested,
        ]);
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
                ->where('type', Translation::TYPE_TAXONOMY)
                ->exists();
            if ($dup) {
                return response()->json(['message' => 'A translation for this language already exists in the group.'], 422);
            }
        } else {
            $groupId = (string) Str::uuid();
        }

        unset($validated['language_id'], $validated['translation_group_id']);

        $taxonomy = DB::transaction(function () use ($validated, $groupId, $languageId) {
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

            Translation::create([
                'translation_group_id' => $groupId,
                'content_id' => $taxonomy->id,
                'language_id' => $languageId,
                'type' => Translation::TYPE_TAXONOMY,
            ]);

            return $taxonomy;
        });

        $taxonomy->loadCount('terms');

        $this->syncTaxonomySeo($taxonomy, $validated);

        $payload = $taxonomy->toArray();
        $seo = SeoContent::query()
            ->where('type', 'taxonomy')
            ->where('post_id', $taxonomy->id)
            ->first();
        $payload['seo_title'] = $seo?->title ?? '';
        $payload['seo_description'] = $seo?->description ?? '';

        $taxonomy->load(['translation.language']);
        $tr = $taxonomy->translation;
        $payload['language_id'] = $tr?->language_id;
        $payload['translation_group_id'] = $tr?->translation_group_id;
        $payload['language'] = $tr?->language;

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

        $taxonomy->load(['translation.language']);
        $tr = $taxonomy->translation;
        $payload['language_id'] = $tr?->language_id;
        $payload['translation_group_id'] = $tr?->translation_group_id;
        $payload['language'] = $tr?->language;

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
