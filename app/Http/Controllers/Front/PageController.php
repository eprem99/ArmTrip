<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use App\Models\Term;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'destinations' => $this->locationTermsForLocale(app()->getLocale()),
        ]);
    }

    /**
     * Published location taxonomy terms for the current (or fallback) locale.
     *
     * @return Collection<int, Term>
     */
    private function locationTermsForLocale(string $lcode): Collection
    {
        $terms = $this->queryLocationTermsForLcode($lcode);
        if ($terms->isNotEmpty()) {
            return $terms;
        }

        $fallback = (string) config('app.fallback_locale', 'en');
        if ($fallback !== $lcode) {
            return $this->queryLocationTermsForLcode($fallback);
        }

        return $terms;
    }

    /**
     * Published location terms for the current locale.
     * Prefers child terms (subcategories); falls back to top-level terms.
     *
     * @return Collection<int, Term>
     */
    private function queryLocationTermsForLcode(string $lcode): Collection
    {
        $lang = Language::query()->where('lcode', $lcode)->first();
        if (! $lang) {
            return collect();
        }

        $termIds = Translation::query()
            ->where('type', Translation::TYPE_TERM)
            ->where('language_id', $lang->id)
            ->pluck('content_id');

        if ($termIds->isEmpty()) {
            return collect();
        }

        $base = Term::query()
            ->published()
            ->with(['taxonomy:id,slug', 'parent:id,slug'])
            ->whereIn('id', $termIds)
            ->whereHas('taxonomy', fn ($q) => $q->whereIn('slug', ['location', 'locations']));

        $children = (clone $base)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->limit(8)
            ->get();

        if ($children->isNotEmpty()) {
            return $children;
        }

        return $base
            ->whereNull('parent_id')
            ->orderBy('name')
            ->limit(8)
            ->get();
    }

    public function show(Request $request, string $slug): View
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        return view('front.page', [
            'page' => $page,
        ]);
    }
}
