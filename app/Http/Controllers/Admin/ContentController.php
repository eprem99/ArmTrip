<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    public function checkSlug(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer'],
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
     * List pages for content management.
     */
    public function index(): JsonResponse
    {
        $pages = Page::orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return response()->json($pages);
    }

    /**
     * Get a single page.
     */
    public function show(Page $page): JsonResponse
    {
        return response()->json($page);
    }

    /**
     * Store a new page.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_home' => ['boolean'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug'),
            ],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'status' => ['in:draft,published'],
            'sort_order' => ['integer'],
            'parent_id' => ['nullable', 'exists:pages,id'],
        ]);

        $validated['content'] = $validated['content'] ?? '';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_home'] = $validated['is_home'] ?? false;

        if ($validated['is_home']) {
            $validated['slug'] = '';
            Page::where('is_home', true)->update(['is_home' => false]);
        } else {
            $validated['slug'] = trim((string) ($validated['slug'] ?? ''));
            if ($validated['slug'] === '') {
                return response()->json(['message' => 'Slug is required'], 422);
            }
        }

        $page = Page::create($validated);

        return response()->json($page, 201);
    }

    /**
     * Update a page.
     */
    public function update(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_home' => ['boolean'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($page->id),
            ],
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

        $validated['is_home'] = $validated['is_home'] ?? false;
        if ($validated['is_home']) {
            $validated['slug'] = '';
            Page::where('id', '!=', $page->id)->where('is_home', true)->update(['is_home' => false]);
        } else {
            $validated['slug'] = trim((string) ($validated['slug'] ?? $page->slug ?? ''));
            if ($validated['slug'] === '') {
                return response()->json(['message' => 'Slug is required'], 422);
            }
        }

        $page->update($validated);

        return response()->json($page);
    }

    /**
     * Delete a page.
     */
    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json(null, 204);
    }
}
