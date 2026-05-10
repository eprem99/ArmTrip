@php
    /** @var \App\Models\Post $post */
    /** @var string $contentHtml */
    /** @var array<int, array{id:string,title:string,level:int}> $toc */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Post> $related */

    $featuredImage = $post->featured_image ?: 'https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1600&h=900&fit=crop';
    $publishedAt = $post->published_at;

    $categoryTerm = $post->terms->first(fn ($t) => $t->taxonomy?->type === \App\Models\Taxonomy::TYPE_CATEGORY);
    $categoryName = $categoryTerm?->name;
    $categorySlug = $categoryTerm?->slug;

    $categoryColors = [
        'guides' => 'bg-sky-100 text-sky-700',
        'food' => 'bg-amber-100 text-amber-700',
        'culture' => 'bg-rose-100 text-rose-700',
        'nature' => 'bg-green-100 text-green-700',
    ];
    $categoryClass = $categorySlug && isset($categoryColors[$categorySlug]) ? $categoryColors[$categorySlug] : 'bg-gray-100 text-gray-700';

    $locationTerm = $post->terms->first(fn ($t) => in_array($t->taxonomy?->slug, ['location', 'locations'], true));
@endphp

@extends('front.layouts.app')

@section('title', $post->title.' — '.config('app.name'))

@section('content')
        {{-- Post Hero --}}
        <section class="relative overflow-hidden" style="min-height: 520px;" aria-label="Post hero">
            <img src="{{ $featuredImage }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover" />
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/40 to-black/20"></div>
            <div class="relative z-10 flex flex-col justify-end h-full max-w-4xl mx-auto px-6 pb-12 pt-28">
                <nav class="flex items-center gap-2 text-sm text-white/60 mb-5" aria-label="Breadcrumb">
                    <a href="{{ route('front.home') }}" class="hover:text-white transition-colors">Home</a>
                    <span>/</span>
                    <a href="{{ route('front.blog.index') }}" class="hover:text-white transition-colors">Blog</a>
                    @if($categoryName)
                        <span>/</span>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $categoryClass }}">{{ $categoryName }}</span>
                    @endif
                    <span>/</span>
                    <span class="text-white/80 truncate max-w-xs">{{ $post->title }}</span>
                </nav>

                @if($categoryName)
                    <span class="self-start text-xs font-semibold px-3 py-1 rounded-full mb-4 {{ $categoryClass }}">
                        {{ $categoryName }}
                    </span>
                @endif

                <h1 class="font-bold text-white text-3xl md:text-4xl lg:text-5xl leading-tight mb-5 max-w-3xl">
                    {{ $post->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-sm text-white/75">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white font-bold text-xs border-2 border-white/30 shrink-0">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(config('app.name'), 0, 2)) }}
                        </span>
                        <span class="text-white font-medium">{{ config('app.name') }}</span>
                    </div>
                    @if($publishedAt)
                        <div class="flex items-center gap-1.5">
                            <span>📅</span>
                            <span>{{ $publishedAt->format('F j, Y') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-1.5">
                        <span>⏱</span>
                        <span>{{ max(3, (int) ceil(str_word_count(strip_tags((string) $post->content)) / 180)) }} min read</span>
                    </div>
                    @if($locationTerm)
                        <div class="flex items-center gap-1.5">
                            <span>📍</span>
                            <span>{{ $locationTerm->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Body --}}
        <main class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex gap-10 items-start">
                <article class="flex-1 min-w-0 max-w-3xl">
                    <a
                        href="{{ route('front.blog.index') }}"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition-colors mb-8 cursor-pointer"
                    >
                        <span>←</span>
                        Back to Blog
                    </a>

                    @if($post->excerpt)
                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 mb-8">
                            <h2 class="font-semibold text-[15px] text-amber-800 mb-3">⚡ Quick Summary</h2>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ $post->excerpt }}
                            </p>
                        </div>
                    @endif

                    <div class="prose prose-slate max-w-none">
                        {!! $contentHtml !!}
                    </div>

                    <div class="flex flex-wrap gap-2 mt-10 pt-8 border-t border-gray-100">
                        <span class="text-gray-400 text-sm mt-0.5">🏷</span>
                        @foreach($post->terms as $term)
                            <a
                                href="{{ route('front.blog.index', ['q' => $term->name]) }}"
                                class="text-xs bg-gray-100 text-gray-600 hover:bg-primary hover:text-white px-3 py-1 rounded-full transition-colors duration-200 cursor-pointer"
                            >
                                {{ $term->name }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        <span class="text-sm text-gray-500 font-medium">Share:</span>
                        <button
                            type="button"
                            class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary transition-colors cursor-pointer"
                            data-copy-link
                        >
                            <span>🔗</span>
                            <span class="text-xs" data-copy-link-label>Copy link</span>
                        </button>
                    </div>
                </article>

                <aside class="hidden xl:block w-64 shrink-0 sticky top-28 self-start" aria-label="Table of contents">
                    @if(!empty($toc))
                        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 mb-6">
                            <h2 class="font-semibold text-[13px] uppercase tracking-widest text-gray-400 mb-4">Contents</h2>
                            <nav>
                                <ul class="space-y-1">
                                    @foreach($toc as $item)
                                        <li>
                                            <a
                                                href="#{{ $item['id'] }}"
                                                class="block text-sm leading-snug py-1 px-3 rounded-lg transition-all duration-200 cursor-pointer border-l-2 border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-200 {{ (int) ($item['level'] ?? 2) === 3 ? 'ml-3 text-xs' : '' }}"
                                                data-toc-link="{{ $item['id'] }}"
                                            >
                                                {{ $item['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                    @endif

                    <button class="w-full flex items-center justify-center gap-2 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-primary hover:text-primary transition-colors cursor-pointer shadow-sm">
                        <span>🔖</span>
                        Save article
                    </button>
                </aside>
            </div>

            @if($related->isNotEmpty())
                <section class="mt-16 pt-10 border-t border-gray-100" aria-label="Related articles">
                    <h2 class="font-semibold text-2xl text-gray-900 mb-8">Related Articles</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($related as $p)
                            @include('front.blog._card', ['post' => $p])
                        @endforeach
                    </div>
                </section>
            @endif
        </main>
@endsection

@push('scripts')
        <script>
        (() => {
            const copyBtn = document.querySelector('[data-copy-link]');
            const label = document.querySelector('[data-copy-link-label]');
            if (copyBtn && label) {
                copyBtn.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(window.location.href);
                        label.textContent = 'Copied!';
                        setTimeout(() => (label.textContent = 'Copy link'), 2000);
                    } catch (e) {
                        label.textContent = 'Copy failed';
                        setTimeout(() => (label.textContent = 'Copy link'), 2000);
                    }
                });
            }

            const links = Array.from(document.querySelectorAll('[data-toc-link]'));
            if (links.length) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        links.forEach((a) => {
                            a.classList.remove('border-primary', 'text-primary', 'font-medium', 'bg-primary/5');
                            a.classList.add('border-transparent', 'text-gray-500');
                        });
                        const a = document.querySelector(`[data-toc-link="${entry.target.id}"]`);
                        if (a) {
                            a.classList.remove('border-transparent', 'text-gray-500');
                            a.classList.add('border-primary', 'text-primary', 'font-medium', 'bg-primary/5');
                        }
                    });
                }, { rootMargin: "-20% 0px -70% 0px" });

                links.forEach((a) => {
                    const id = a.getAttribute('data-toc-link');
                    const el = id ? document.getElementById(id) : null;
                    if (el) observer.observe(el);
                });
            }
        })();
        </script>
@endpush

