<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    public function index(Request $request): JsonResponse
    {
        $perPage = max(5, min(100, (int) $request->get('per_page', 15)));

        $query = Post::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $term = '%'.$request->get('search').'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        $posts = $query->paginate($perPage);

        return response()->json($posts);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json($post);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['title']);
        } else {
            $slug = Str::slug($slug);
        }

        $slugBase = $slug;
        $i = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$i;
            $i++;
        }

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

        return response()->json($post, 201);
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

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($validated['title']);
        } else {
            $slug = Str::slug($slug);
        }

        $slugBase = $slug;
        $i = 2;
        while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
            $slug = $slugBase.'-'.$i;
            $i++;
        }

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

        return response()->json($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'deleted']);
    }
}
