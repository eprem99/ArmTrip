<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function showTaxonomy(string $taxonomySlug): View
    {
        $taxonomy = Taxonomy::with('terms.children')
            ->where('slug', $taxonomySlug)
            ->firstOrFail();

        return view('front.taxonomy.index', compact('taxonomy'));
    }

    public function showTerm(string $taxonomySlug, string $termSlug): View
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->firstOrFail();

        $term = Term::with(['posts' => function ($query) {
            $query->published()->with('terms.taxonomy');
        }])
            ->where('taxonomy_id', $taxonomy->id)
            ->where('slug', $termSlug)
            ->firstOrFail();

        $posts = $term->posts()->published()->with('terms.taxonomy')->paginate(10);

        return view('front.taxonomy.term', compact('taxonomy', 'term', 'posts'));
    }
}

