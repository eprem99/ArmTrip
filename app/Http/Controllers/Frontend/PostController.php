<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $location = trim((string) $request->query('location', ''));
        $duration = trim((string) $request->query('duration', ''));

        $postsQuery = Post::query()
            ->with(['terms.taxonomy'])
            ->published()
            ->latest('published_at');

        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';
            $postsQuery->where(function ($qq) use ($like) {
                $qq->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('content', 'like', $like);
            });
        }

        if ($category !== '') {
            $postsQuery->whereHas('terms', function ($tq) use ($category) {
                $tq->where('slug', $category)
                    ->whereHas('taxonomy', fn ($tx) => $tx->where('type', Taxonomy::TYPE_CATEGORY));
            });
        }

        if ($location !== '') {
            $postsQuery->whereHas('terms', function ($tq) use ($location) {
                $tq->where('slug', $location)
                    ->whereHas('taxonomy', fn ($tx) => $tx->whereIn('slug', ['location', 'locations']));
            });
        }

        if ($duration !== '') {
            $postsQuery->whereHas('terms', function ($tq) use ($duration) {
                $tq->where('slug', $duration)
                    ->whereHas('taxonomy', fn ($tx) => $tx->whereIn('slug', ['duration', 'durations']));
            });
        }

        $posts = $postsQuery->paginate(9)->withQueryString();

        $categories = Term::query()
            ->published()
            ->whereHas('taxonomy', fn ($tx) => $tx->where('type', Taxonomy::TYPE_CATEGORY))
            ->orderBy('name')
            ->get();

        $locations = Term::query()
            ->published()
            ->whereHas('taxonomy', fn ($tx) => $tx->whereIn('slug', ['location', 'locations']))
            ->orderBy('name')
            ->get();

        $durations = Term::query()
            ->published()
            ->whereHas('taxonomy', fn ($tx) => $tx->whereIn('slug', ['duration', 'durations']))
            ->orderBy('name')
            ->get();

        $popularPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('front.blog.index', compact(
            'posts',
            'popularPosts',
            'categories',
            'locations',
            'durations',
            'q',
            'category',
            'location',
            'duration',
        ));
    }

    public function show(string $slug): View
    {
        $post = Post::with(['terms.taxonomy'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Build TOC from HTML content and ensure headings have stable ids
        $contentHtml = (string) ($post->content ?? '');
        $toc = [];
        $usedIds = [];

        if (trim($contentHtml) !== '') {
            $wrapped = '<div>'.$contentHtml.'</div>';
            $previous = libxml_use_internal_errors(true);
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML(mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'));

            $xpath = new \DOMXPath($dom);
            $headings = $xpath->query('//h2|//h3');

            if ($headings instanceof \DOMNodeList) {
                foreach ($headings as $node) {
                    if (!($node instanceof \DOMElement)) {
                        continue;
                    }

                    $text = trim((string) $node->textContent);
                    if ($text === '') {
                        continue;
                    }

                    $level = $node->tagName === 'h3' ? 3 : 2;
                    $id = trim((string) $node->getAttribute('id'));
                    if ($id === '') {
                        $base = Str::slug($text);
                        $base = $base !== '' ? $base : ('section-'.$level);
                        $candidate = $base;
                        $i = 2;
                        while (isset($usedIds[$candidate])) {
                            $candidate = $base.'-'.$i;
                            $i++;
                        }
                        $id = $candidate;
                        $node->setAttribute('id', $id);
                    }

                    $usedIds[$id] = true;
                    $toc[] = ['id' => $id, 'title' => $text, 'level' => $level];
                }
            }

            $wrapperDiv = $dom->getElementsByTagName('div')->item(0);
            if ($wrapperDiv instanceof \DOMElement) {
                $inner = '';
                foreach ($wrapperDiv->childNodes as $child) {
                    $inner .= $dom->saveHTML($child);
                }
                $contentHtml = $inner;
            }

            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $related = Post::query()
            ->with(['terms.taxonomy'])
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->terms->isNotEmpty(), function ($q) use ($post) {
                $termIds = $post->terms->pluck('id')->all();
                $q->whereHas('terms', fn ($tq) => $tq->whereIn('terms.id', $termIds));
            })
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('front.blog.show', compact('post', 'contentHtml', 'toc', 'related'));
    }
}
