<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use App\Models\Translation;
use App\Support\SlugGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function checkSlug(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer'],
            'language_id' => ['nullable', 'integer', 'exists:languages,id'],
        ]);

        $slug = trim($validated['slug']);
        $ignoreId = $validated['ignore_id'] ?? null;

        if ($slug === '') {
            return response()->json([
                'available' => false,
                'slug' => '',
                'suggested' => '',
            ]);
        }

        $query = Page::query()->where('slug', $slug);
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
            $q = Page::query()->where('slug', $candidate);
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
     * Suggest a unique page slug from title (Str::slug + numeric suffix). Used by the admin form.
     */
    public function suggestSlug(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        $slug = SlugGenerator::uniquePageSlug(
            $validated['title'] ?? '',
            $validated['slug'] ?? '',
            $validated['ignore_id'] ?? null,
        );

        return response()->json(['slug' => $slug]);
    }

    /**
     * List pages for content management.
     * - Default: grouped rows with translations (for admin list).
     * - flat=1: plain list for one language (parent dropdowns, etc.).
     */
    public function index(Request $request): JsonResponse
    {
        $lcode = (string) $request->get('lang', 'en');
        $lang = Language::query()->where('lcode', $lcode)->first()
            ?? Language::query()->orderBy('id')->firstOrFail();

        $pageIds = Translation::query()
            ->where('type', Translation::TYPE_PAGE)
            ->where('language_id', $lang->id)
            ->pluck('content_id');

        if ($pageIds->isEmpty()) {
            return response()->json([]);
        }

        $pages = Page::query()
            ->whereIn('id', $pageIds)
            ->with(['translation.language'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        if ($request->boolean('flat')) {
            return response()->json($pages->map(function (Page $p) {
                $a = $p->toArray();
                $a['language_id'] = $p->translation?->language_id;
                $a['translation_group_id'] = $p->translation?->translation_group_id;

                return $a;
            }));
        }

        $groupIds = $pages
            ->map(fn (Page $p) => $p->translation?->translation_group_id)
            ->filter()
            ->unique()
            ->values();

        if ($groupIds->isEmpty()) {
            return response()->json([]);
        }

        $allInGroups = Translation::query()
            ->where('type', Translation::TYPE_PAGE)
            ->whereIn('translation_group_id', $groupIds)
            ->with('language:id,lcode,native_name,name')
            ->get()
            ->groupBy('translation_group_id');

        $result = $pages->map(function (Page $page) use ($allInGroups) {
            $tg = $page->translation?->translation_group_id;
            if (! $tg) {
                return null;
            }
            $siblings = $allInGroups->get($tg, collect())->values();

            return [
                'translation_group_id' => $tg,
                'page' => $page,
                'translations' => $siblings->map(fn (Translation $tr) => [
                    'page_id' => $tr->content_id,
                    'language_id' => $tr->language_id,
                    'lcode' => $tr->language?->lcode ?? '',
                    'label' => $tr->language?->native_name ?? $tr->language?->name ?? $tr->language?->lcode ?? '',
                ])->values()->all(),
            ];
        })->filter()->values();

        return response()->json($result);
    }

    /**
     * Get a single page.
     */
    public function show(Page $page): JsonResponse
    {
        $page->load(['translation.language']);

        $payload = $page->toArray();
        $t = $page->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;

        return response()->json($payload);
    }

    /**
     * Store a new page.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_home' => ['boolean'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'status' => ['in:draft,published'],
            'sort_order' => ['integer'],
            'parent_id' => ['nullable', 'exists:pages,id'],
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
                ->where('type', Translation::TYPE_PAGE)
                ->exists();
            if ($dup) {
                return response()->json(['message' => 'A translation for this language already exists in the group.'], 422);
            }
        } else {
            $groupId = (string) Str::uuid();
        }

        unset($validated['language_id'], $validated['translation_group_id']);

        $validated['content'] = $validated['content'] ?? '';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_home'] = $validated['is_home'] ?? false;

        if (! empty($validated['parent_id'])) {
            $parentT = Translation::query()
                ->where('type', Translation::TYPE_PAGE)
                ->where('content_id', $validated['parent_id'])
                ->first();
            if (! $parentT || (int) $parentT->language_id !== $languageId) {
                return response()->json(['message' => 'Parent page must use the same language.'], 422);
            }
        }

        if ($validated['is_home']) {
            $validated['slug'] = '';
            $this->clearHomeFlagForLanguage($languageId);
        } else {
            $validated['slug'] = SlugGenerator::uniquePageSlug(
                $validated['title'],
                $validated['slug'] ?? '',
                null,
            );
        }

        $page = DB::transaction(function () use ($validated, $groupId, $languageId) {
            $page = Page::create($validated);

            Translation::create([
                'translation_group_id' => $groupId,
                'content_id' => $page->id,
                'language_id' => $languageId,
                'type' => Translation::TYPE_PAGE,
            ]);

            return $page;
        });

        $page->load(['translation.language']);

        $payload = $page->toArray();
        $t = $page->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;

        return response()->json($payload, 201);
    }

    /**
     * Update a page.
     */
    public function update(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_home' => ['boolean'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'status' => ['in:draft,published'],
            'sort_order' => ['integer'],
            'parent_id' => ['nullable', 'exists:pages,id'],
        ]);

        if (isset($validated['parent_id']) && (int) $validated['parent_id'] === (int) $page->id) {
            $validated['parent_id'] = null;
        }

        $page->load('translation');
        $languageId = (int) ($page->translation?->language_id ?? 0);

        if (! empty($validated['parent_id'])) {
            $parentT = Translation::query()
                ->where('type', Translation::TYPE_PAGE)
                ->where('content_id', $validated['parent_id'])
                ->first();
            if (! $parentT || (int) $parentT->language_id !== $languageId) {
                return response()->json(['message' => 'Parent page must use the same language.'], 422);
            }
        }

        $validated['is_home'] = $validated['is_home'] ?? false;

        if ($validated['is_home']) {
            $validated['slug'] = '';
            $this->clearHomeFlagForLanguage($languageId, $page->id);
        } else {
            $validated['slug'] = SlugGenerator::uniquePageSlug(
                $validated['title'],
                $validated['slug'] ?? $page->slug ?? '',
                $page->id,
            );
        }

        $page->update($validated);

        $page->load(['translation.language']);

        $payload = $page->toArray();
        $t = $page->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;

        return response()->json($payload);
    }

    /**
     * Delete a page.
     */
    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json(null, 204);
    }

    private function clearHomeFlagForLanguage(int $languageId, ?int $exceptPageId = null): void
    {
        $ids = Translation::query()
            ->where('type', Translation::TYPE_PAGE)
            ->where('language_id', $languageId)
            ->pluck('content_id');

        $q = Page::query()->whereIn('id', $ids)->where('is_home', true);
        if ($exceptPageId) {
            $q->where('id', '!=', $exceptPageId);
        }
        $q->update(['is_home' => false]);
    }
}
