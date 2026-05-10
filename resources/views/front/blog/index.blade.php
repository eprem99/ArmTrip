@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Post> $posts */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Post> $popularPosts */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Term> $categories */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Term> $locations */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Term> $durations */
@endphp
@extends('front.layouts.app')

@section('title', __('Blog').' — '.config('app.name'))

@section('content')
        {{-- Hero --}}
        <section class="relative flex items-center justify-center overflow-hidden" style="min-height: 420px;" aria-label="Blog hero">
            <img
                src="https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1600&h=600&fit=crop"
                alt="Armenian landscape"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-linear-to-b from-black/50 via-black/40 to-black/70"></div>
            <div class="relative z-10 text-center text-white px-6 max-w-3xl mx-auto pt-16">
                <nav class="flex items-center justify-center gap-2 text-sm text-white/70 mb-6" aria-label="Breadcrumb">
                    <a href="{{ route('front.home') }}" class="hover:text-white transition-colors">Home</a>
                    <span>/</span>
                    <span class="text-white">Blog</span>
                </nav>
                <h1 class="font-semibold text-4xl md:text-5xl leading-tight mb-4">
                    Travel Blog about Armenia
                </h1>
                <p class="text-lg text-white/80 leading-relaxed">
                    Tips, guides, itineraries, and stories from the Land of Mountains
                </p>
            </div>
        </section>

        {{-- Filters bar --}}
        @php
            $hasActiveFilters = ($q ?? '') !== '' || ($category ?? '') !== '' || ($location ?? '') !== '' || ($duration ?? '') !== '';

            $categoryColors = [
                'guides' => 'bg-sky-50 text-sky-700 border-sky-200 hover:bg-sky-100',
                'food' => 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                'culture' => 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100',
                'nature' => 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100',
            ];
        @endphp

        <section class="sticky top-16 z-40 bg-white border-b border-gray-100 shadow-sm" aria-label="Blog filters">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <form method="get" action="{{ route('front.blog.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px] max-w-xs">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔎</span>
                        <input
                            type="search"
                            name="q"
                            placeholder="Search articles..."
                            value="{{ $q }}"
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            aria-label="Search blog posts"
                        />
                    </div>

                    <div class="flex flex-wrap gap-2" role="group" aria-label="Category filters">
                        @foreach($categories as $cat)
                            @php
                                $slug = (string) $cat->slug;
                                $isActive = ($category ?? '') === $slug;
                                $color = $categoryColors[$slug] ?? 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100';
                            @endphp
                            <button
                                type="submit"
                                name="category"
                                value="{{ $isActive ? '' : $slug }}"
                                class="text-xs font-medium px-3 py-1.5 rounded-full border transition-all duration-200 cursor-pointer {{ $isActive ? 'bg-primary text-white border-primary' : $color }}"
                                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    @if($locations->isNotEmpty())
                        <select
                            name="location"
                            class="text-sm border border-gray-200 rounded-full px-3 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 cursor-pointer"
                            aria-label="Filter by location"
                            onchange="this.form.submit()"
                        >
                            <option value="">All Locations</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->slug }}" @selected(($location ?? '') === (string) $loc->slug)>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    @if($durations->isNotEmpty())
                        <select
                            name="duration"
                            class="text-sm border border-gray-200 rounded-full px-3 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 cursor-pointer"
                            aria-label="Filter by duration"
                            onchange="this.form.submit()"
                        >
                            <option value="">All Durations</option>
                            @foreach($durations as $d)
                                <option value="{{ $d->slug }}" @selected(($duration ?? '') === (string) $d->slug)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    @if(($category ?? '') !== '')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    @if(($location ?? '') !== '')
                        <input type="hidden" name="location" value="{{ $location }}">
                    @endif
                    @if(($duration ?? '') !== '')
                        <input type="hidden" name="duration" value="{{ $duration }}">
                    @endif

                    @if($hasActiveFilters)
                        <a
                            href="{{ route('front.blog.index') }}"
                            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-primary transition-colors cursor-pointer"
                            aria-label="Clear all filters"
                        >
                            <span class="text-sm">✕</span>
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </section>

        {{-- Main content --}}
        <main class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex gap-10">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-8">
                        <span class="text-gray-400">⎇</span>
                        <span class="text-sm text-gray-500">
                            {{ $posts->total() }} article{{ $posts->total() !== 1 ? 's' : '' }}{{ $hasActiveFilters ? ' found' : ' total' }}
                        </span>
                    </div>

                    @if($posts->count() === 0)
                        <div class="text-center py-24">
                            <p class="text-3xl mb-3">🏔️</p>
                            <p class="text-gray-500 text-lg font-medium">No articles match your filters</p>
                            <a href="{{ route('front.blog.index') }}" class="mt-4 inline-block text-primary text-sm hover:underline cursor-pointer">
                                Clear filters
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($posts as $post)
                                @include('front.blog._card', ['post' => $post])
                            @endforeach
                        </div>

                        <div class="mt-12">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>

                <aside class="hidden xl:flex flex-col gap-8 w-72 shrink-0" aria-label="Blog sidebar">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-[15px] text-gray-800 mb-4">Popular Posts</h2>
                        <ul class="space-y-4">
                            @foreach($popularPosts as $i => $p)
                                <li>
                                    <a href="{{ route('front.blog.show', ['slug' => $p->slug]) }}" class="flex items-start gap-3 group" aria-label="{{ $p->title }}">
                                        <span class="text-2xl font-bold text-gray-100 group-hover:text-primary/30 transition-colors w-6 text-center leading-none mt-0.5 shrink-0">
                                            {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-sm text-gray-600 leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                            {{ $p->title }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-[15px] text-gray-800 mb-4">Categories</h2>
                        <ul class="space-y-2">
                            @foreach($categories as $cat)
                                @php
                                    $count = (int) \App\Models\Post::query()
                                        ->published()
                                        ->whereHas('terms', fn ($tq) => $tq->where('terms.id', $cat->id))
                                        ->count();
                                    $isActive = ($category ?? '') === (string) $cat->slug;
                                @endphp
                                <li>
                                    <a
                                        href="{{ route('front.blog.index', array_filter(['q' => $q, 'category' => $isActive ? null : $cat->slug, 'location' => $location, 'duration' => $duration], fn ($v) => $v !== null && $v !== '')) }}"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors cursor-pointer {{ $isActive ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}"
                                        aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                    >
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full shrink-0">{{ $count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-linear-to-br from-primary to-orange-600 rounded-2xl p-6 text-white">
                        <h2 class="font-semibold text-[15px] mb-2">Stay in the loop</h2>
                        <p class="text-sm text-white/80 mb-4 leading-relaxed">
                            Get the latest travel guides and stories about Armenia delivered to your inbox.
                        </p>
                        <input
                            type="email"
                            placeholder="your@email.com"
                            class="w-full px-4 py-2.5 rounded-xl text-sm text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50 mb-3"
                            aria-label="Email for newsletter"
                        />
                        <button class="w-full py-2.5 bg-white text-primary font-semibold text-sm rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                            Subscribe
                        </button>
                    </div>
                </aside>
            </div>
        </main>
@endsection

