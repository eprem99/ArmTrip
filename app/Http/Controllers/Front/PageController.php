<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(Request $request): View
    {
        $page = null;
        if (Schema::hasTable('pages')) {
            $page = Page::where('is_home', true)->where('status', 'published')->first();
        }

        return view('front.home', [
            'page' => $page,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        return view('front.page', [
            'page' => $page,
        ]);
    }
}
