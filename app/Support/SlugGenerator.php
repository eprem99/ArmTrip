<?php

namespace App\Support;

use App\Models\Page;
use App\Models\Post;
use App\Models\Rental;
use Illuminate\Support\Str;

class SlugGenerator
{
    /**
     * Lowercase Latin slug from title (Str::slug transliterates e.g. Armenian), then ensure global uniqueness on pages.
     */
    public static function uniquePageSlug(?string $title, ?string $slugInput, ?int $ignorePageId = null): string
    {
        $slug = trim((string) $slugInput);
        if ($slug === '') {
            $slug = Str::slug((string) $title);
        } else {
            $slug = Str::slug($slug);
        }

        if ($slug === '') {
            $slug = 'page';
        }

        $base = Str::limit($slug, 240, '');
        $candidate = $base;
        $i = 2;

        while (Page::query()
            ->where('slug', $candidate)
            ->when($ignorePageId, fn ($q) => $q->where('id', '!=', $ignorePageId))
            ->exists()) {
            $candidate = Str::limit($base, 240, '').'-'.$i;
            $i++;
            if ($i > 10000) {
                break;
            }
        }

        return $candidate;
    }

    /**
     * Same for blog posts (posts.slug unique).
     */
    public static function uniquePostSlug(?string $title, ?string $slugInput, ?int $ignorePostId = null): string
    {
        $slug = trim((string) $slugInput);
        if ($slug === '') {
            $slug = Str::slug((string) $title);
        } else {
            $slug = Str::slug($slug);
        }

        if ($slug === '') {
            $slug = 'post';
        }

        $base = Str::limit($slug, 240, '');
        $candidate = $base;
        $i = 2;

        while (Post::query()
            ->where('slug', $candidate)
            ->when($ignorePostId, fn ($q) => $q->where('id', '!=', $ignorePostId))
            ->exists()) {
            $candidate = Str::limit($base, 240, '').'-'.$i;
            $i++;
            if ($i > 10000) {
                break;
            }
        }

        return $candidate;
    }

    public static function uniqueRentalSlug(?string $title, ?string $slugInput, ?int $ignoreRentalId = null): string
    {
        $slug = trim((string) $slugInput);
        if ($slug === '') {
            $slug = Str::slug((string) $title);
        } else {
            $slug = Str::slug($slug);
        }

        if ($slug === '') {
            $slug = 'rental';
        }

        $base = Str::limit($slug, 240, '');
        $candidate = $base;
        $i = 2;

        while (Rental::query()
            ->withTrashed()
            ->where('slug', $candidate)
            ->when($ignoreRentalId, fn ($q) => $q->where('id', '!=', $ignoreRentalId))
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
