<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\Translation;
use App\Support\SlugGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogPostsController extends Controller
{
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

        $query = Post::query()->where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        $exists = $query->exists();

        if (! $exists) {
            return response()->json([
                'available' => true,
                'slug' => $slug,
                'suggested' => $slug,
            ]);
        }

        $base = $slug;
        $i = 2;
        while ($i < 1000) {
            $candidate = Str::limit($base, 240, '').'-'.$i;
            $q = Post::query()->where('slug', $candidate);
            if ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            }
            if (! $q->exists()) {
                return response()->json([
                    'available' => false,
                    'slug' => $slug,
                    'suggested' => $candidate,
                ]);
            }
            $i++;
        }

        return response()->json([
            'available' => false,
            'slug' => $slug,
            'suggested' => '',
        ]);
    }

    /**
     * Suggest a unique post slug from title (Str::slug + numeric suffix).
     */
    public function suggestSlug(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        $slug = SlugGenerator::uniquePostSlug(
            $validated['title'] ?? '',
            $validated['slug'] ?? '',
            $validated['ignore_id'] ?? null,
        );

        return response()->json(['slug' => $slug]);
    }

    /**
     * All taxonomies with terms for the post editor (filtered by language).
     */
    public function taxonomyTerms(Request $request): JsonResponse
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
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'type', 'icon']);

        $termIdsForLang = Translation::query()
            ->where('type', Translation::TYPE_TERM)
            ->where('language_id', $lang->id)
            ->pluck('content_id');

        $termsByTaxonomy = collect();
        if ($termIdsForLang->isNotEmpty()) {
            $termsByTaxonomy = Term::query()
                ->whereIn('id', $termIdsForLang)
                ->whereIn('taxonomy_id', $taxonomyIds)
                ->with(['parent:id,name,slug'])
                ->orderBy('name')
                ->get(['id', 'taxonomy_id', 'name', 'slug', 'parent_id'])
                ->groupBy('taxonomy_id');
        }

        $result = $taxonomies->map(function (Taxonomy $taxonomy) use ($termsByTaxonomy) {
            $terms = $termsByTaxonomy->get($taxonomy->id, collect())->values();

            return [
                'id' => $taxonomy->id,
                'name' => $taxonomy->name,
                'slug' => $taxonomy->slug,
                'type' => $taxonomy->type,
                'icon' => $taxonomy->icon,
                'terms' => $terms->map(fn (Term $term) => [
                    'id' => $term->id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'parent_id' => $term->parent_id,
                    'parent' => $term->parent ? [
                        'id' => $term->parent->id,
                        'name' => $term->parent->name,
                        'slug' => $term->parent->slug,
                    ] : null,
                ])->all(),
            ];
        })->values();

        return response()->json($result);
    }

    /**
     * Grouped posts for the current UI language (same shape as pages list).
     */
    public function index(Request $request): JsonResponse
    {
        $lcode = (string) $request->get('lang', 'en');
        $lang = Language::query()->where('lcode', $lcode)->first()
            ?? Language::query()->orderBy('id')->firstOrFail();

        $postIds = Translation::query()
            ->where('type', Translation::TYPE_POST)
            ->where('language_id', $lang->id)
            ->pluck('content_id');

        if ($postIds->isEmpty()) {
            return response()->json([]);
        }

        $posts = Post::query()
            ->whereIn('id', $postIds)
            ->with(['translation.language'])
            ->orderBy('created_at', 'desc')
            ->get();

        $groupIds = $posts
            ->map(fn (Post $p) => $p->translation?->translation_group_id)
            ->filter()
            ->unique()
            ->values();

        if ($groupIds->isEmpty()) {
            return response()->json([]);
        }

        $allInGroups = Translation::query()
            ->where('type', Translation::TYPE_POST)
            ->whereIn('translation_group_id', $groupIds)
            ->with('language:id,lcode,native_name,name')
            ->get()
            ->groupBy('translation_group_id');

        $result = $posts->map(function (Post $post) use ($allInGroups) {
            $tg = $post->translation?->translation_group_id;
            if (! $tg) {
                return null;
            }
            $siblings = $allInGroups->get($tg, collect())->values();

            return [
                'translation_group_id' => $tg,
                'post' => $post,
                'translations' => $siblings->map(fn (Translation $tr) => [
                    'post_id' => $tr->content_id,
                    'language_id' => $tr->language_id,
                    'lcode' => $tr->language?->lcode ?? '',
                    'label' => $tr->language?->native_name ?? $tr->language?->name ?? $tr->language?->lcode ?? '',
                ])->values()->all(),
            ];
        })->filter()->values();

        return response()->json($result);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['translation.language', 'terms.taxonomy']);

        $payload = $post->toArray();
        $t = $post->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;
        $payload['term_ids'] = $post->terms->pluck('id')->values()->all();

        return response()->json($payload);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'translation_group_id' => ['nullable', 'uuid'],
            'term_ids' => ['nullable', 'array'],
            'term_ids.*' => ['integer', 'exists:terms,id'],
        ]);

        $languageId = (int) $validated['language_id'];
        $termIds = $validated['term_ids'] ?? [];
        unset($validated['term_ids']);
        $groupId = isset($validated['translation_group_id'])
            ? trim((string) $validated['translation_group_id'])
            : '';

        if ($groupId !== '') {
            $dup = Translation::query()
                ->where('translation_group_id', $groupId)
                ->where('language_id', $languageId)
                ->where('type', Translation::TYPE_POST)
                ->exists();
            if ($dup) {
                return response()->json(['message' => 'A translation for this language already exists in the group.'], 422);
            }
        } else {
            $groupId = (string) Str::uuid();
        }

        unset($validated['language_id'], $validated['translation_group_id']);

        $post = DB::transaction(function () use ($validated, $groupId, $languageId, $termIds) {
            $slug = SlugGenerator::uniquePostSlug(
                $validated['title'],
                $validated['slug'] ?? '',
                null,
            );

            $publishedAt = null;
            if (($validated['status'] ?? 'draft') === 'published') {
                $publishedAt = isset($validated['published_at']) && $validated['published_at']
                    ? Carbon::parse($validated['published_at'])
                    : now();
            }

            $post = Post::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'excerpt' => $validated['excerpt'] ?? null,
                'content' => $validated['content'] ?? null,
                'featured_image' => $validated['featured_image'] ?? null,
                'status' => $validated['status'],
                'published_at' => $publishedAt,
            ]);

            Translation::create([
                'translation_group_id' => $groupId,
                'content_id' => $post->id,
                'language_id' => $languageId,
                'type' => Translation::TYPE_POST,
            ]);

            $this->syncPostTerms($post, $termIds, $languageId);

            return $post;
        });

        $post->load(['translation.language', 'terms']);

        $payload = $post->toArray();
        $t = $post->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;
        $payload['term_ids'] = $post->terms->pluck('id')->values()->all();

        return response()->json($payload, 201);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($post->id),
            ],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'term_ids' => ['nullable', 'array'],
            'term_ids.*' => ['integer', 'exists:terms,id'],
        ]);

        $termIds = $validated['term_ids'] ?? [];
        unset($validated['term_ids']);

        $slug = SlugGenerator::uniquePostSlug(
            $validated['title'],
            $validated['slug'] ?? $post->slug,
            $post->id,
        );

        $publishedAt = $post->published_at;
        if (($validated['status'] ?? 'draft') === 'published') {
            $publishedAt = isset($validated['published_at']) && $validated['published_at']
                ? Carbon::parse($validated['published_at'])
                : ($post->published_at ?? now());
        } else {
            $publishedAt = null;
        }

        $post->fill([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'featured_image' => $validated['featured_image'] ?? null,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ]);
        $post->save();

        $post->load('translation');
        $this->syncPostTerms($post, $termIds, $post->translation?->language_id);

        $post->load(['translation.language', 'terms']);

        $payload = $post->toArray();
        $t = $post->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;
        $payload['term_ids'] = $post->terms->pluck('id')->values()->all();

        return response()->json($payload);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'deleted']);
    }

    /**
     * @param  list<int>  $termIds
     */
    private function syncPostTerms(Post $post, array $termIds, ?int $languageId = null): void
    {
        $termIds = array_values(array_unique(array_map('intval', $termIds)));

        if ($languageId !== null && $termIds !== []) {
            $validIds = Translation::query()
                ->where('type', Translation::TYPE_TERM)
                ->where('language_id', $languageId)
                ->whereIn('content_id', $termIds)
                ->pluck('content_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $termIds = array_values(array_intersect($termIds, $validIds));
        }

        $post->terms()->sync($termIds);
    }
}
