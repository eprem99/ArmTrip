<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['terms.taxonomy'])
            ->published()
            ->latest('published_at')
            ->paginate(10);

        return view('front.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = Post::with(['terms.taxonomy'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('front.blog.show', compact('post'));
    }
}

