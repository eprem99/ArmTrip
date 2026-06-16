<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function showTaxonomy(string $taxonomySlug): View
    {
        $taxonomy = Taxonomy::query()
            ->where('slug', $taxonomySlug)
            ->with([
                'terms' => function ($query) {
                    $query->published()
                        ->whereNull('parent_id')
                        ->with(['children' => function ($q) {
                            $q->published()->orderBy('name');
                        }])
                        ->orderBy('name');
                },
            ])
            ->firstOrFail();

        return view('front.taxonomy.index', compact('taxonomy'));
    }

    public function showTerm(string $taxonomySlug, string $termSlug): View|RedirectResponse
    {
        $taxonomy = Taxonomy::query()->where('slug', $taxonomySlug)->firstOrFail();

        $term = Term::query()
            ->published()
            ->with(['taxonomy', 'parent'])
            ->where('taxonomy_id', $taxonomy->id)
            ->where('slug', $termSlug)
            ->firstOrFail();

        if ($term->parent_id && $term->parent) {
            return redirect()->route('front.taxonomy.term.nested', [
                'taxonomySlug' => $taxonomySlug,
                'parentSlug' => $term->parent->slug,
                'termSlug' => $term->slug,
            ], 301);
        }

        return $this->termView($taxonomy, $term);
    }

    public function showNestedTerm(string $taxonomySlug, string $parentSlug, string $termSlug): View
    {
        $taxonomy = Taxonomy::query()->where('slug', $taxonomySlug)->firstOrFail();

        $parent = Term::query()
            ->published()
            ->where('taxonomy_id', $taxonomy->id)
            ->whereNull('parent_id')
            ->where('slug', $parentSlug)
            ->firstOrFail();

        $term = Term::query()
            ->published()
            ->with(['taxonomy', 'parent'])
            ->where('taxonomy_id', $taxonomy->id)
            ->where('parent_id', $parent->id)
            ->where('slug', $termSlug)
            ->firstOrFail();

        return $this->termView($taxonomy, $term);
    }

    private function termView(Taxonomy $taxonomy, Term $term): View
    {
        $posts = $term->posts()
            ->published()
            ->with('terms.taxonomy')
            ->latest('published_at')
            ->paginate(9);

        return view('front.taxonomy.term', compact('taxonomy', 'term', 'posts'));
    }
}
