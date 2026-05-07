@php
    /** @var \App\Models\Post $post */

    $featuredImage = $post->featured_image ?: 'https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1200&h=700&fit=crop';
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
    $durationTerm = $post->terms->first(fn ($t) => in_array($t->taxonomy?->slug, ['duration', 'durations'], true));
@endphp

<a
    href="{{ route('front.blog.show', ['slug' => $post->slug]) }}"
    class="group flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 border border-gray-100 h-full"
    aria-label="{{ $post->title }}"
>
    <div class="relative overflow-hidden" style="height: 200px;">
        <img
            src="{{ $featuredImage }}"
            alt="{{ $post->title }}"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
        />
        @if($categoryName)
            <span class="absolute top-3 left-3 text-xs font-medium px-2.5 py-1 rounded-full {{ $categoryClass }}">
                {{ $categoryName }}
            </span>
        @endif
    </div>

    <div class="flex flex-col flex-1 p-5 gap-3">
        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
            @if($locationTerm)
                <span class="inline-flex items-center gap-1">
                    <span class="text-[11px]">📍</span>
                    {{ $locationTerm->name }}
                </span>
            @endif
            @if($locationTerm && $durationTerm)
                <span class="text-gray-300">·</span>
            @endif
            @if($durationTerm)
                <span class="inline-flex items-center gap-1">
                    <span class="text-[11px]">⏱</span>
                    {{ $durationTerm->name }}
                </span>
            @endif
        </div>

        <h3 class="font-semibold text-gray-800 text-[16px] leading-snug group-hover:text-primary transition-colors duration-200 line-clamp-2">
            {{ $post->title }}
        </h3>

        @if($post->excerpt)
            <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 flex-1">
                {{ $post->excerpt }}
            </p>
        @else
            <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 flex-1">
                {{ \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 140) }}
            </p>
        @endif

        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-[11px]">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(config('app.name'), 0, 2)) }}
                </span>
                <span class="text-xs text-gray-600 font-medium">{{ config('app.name') }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <span>📅</span>
                <span>{{ $publishedAt ? $publishedAt->format('M d, Y') : '' }}</span>
            </div>
        </div>
    </div>
</a>

