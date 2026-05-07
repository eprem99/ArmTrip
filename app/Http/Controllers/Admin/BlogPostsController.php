<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Post;
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
        $post->load(['translation.language']);

        $payload = $post->toArray();
        $t = $post->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;

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
        ]);

        $languageId = (int) $validated['language_id'];
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

        $post = DB::transaction(function () use ($validated, $groupId, $languageId) {
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

            return $post;
        });

        $post->load(['translation.language']);

        $payload = $post->toArray();
        $t = $post->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;

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
        ]);

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

        $post->load(['translation.language']);

        $payload = $post->toArray();
        $t = $post->translation;
        $payload['language_id'] = $t?->language_id;
        $payload['translation_group_id'] = $t?->translation_group_id;
        $payload['language'] = $t?->language;

        return response()->json($payload);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'deleted']);
    }
}
