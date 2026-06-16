@php
    /** @var \App\Models\Taxonomy $taxonomy */
    /** @var \App\Models\Term $term */
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Post> $posts */

    $featuredImage = $term->image ?: 'https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1600&h=600&fit=crop';
@endphp

@extends('front.layouts.app')

@section('title', $term->name.' — '.config('app.name'))

@section('content')
        {{-- Hero --}}
        <section class="relative flex items-center justify-center overflow-hidden" style="min-height: 420px;" aria-label="{{ $term->name }}">
            <img
                src="{{ $featuredImage }}"
                alt="{{ $term->name }}"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-linear-to-b from-black/50 via-black/40 to-black/70"></div>
            <div class="relative z-10 text-center text-white px-6 max-w-3xl mx-auto pt-16">
                <nav class="flex items-center justify-center gap-2 text-sm text-white/70 mb-6" aria-label="Breadcrumb">
                    <a href="{{ route('front.home') }}" class="hover:text-white transition-colors">Home</a>
                    <span>/</span>
                    <span class="text-white">{{ $term->name }}</span>
                </nav>
                <span class="inline-block text-xs font-semibold uppercase tracking-widest text-white/80 mb-3">
                    {{ $taxonomy->name }}
                </span>
                <h1 class="font-semibold text-4xl md:text-5xl leading-tight mb-4">
                    {{ $term->name }}
                </h1>
                @if($term->short_description)
                    <p class="text-lg text-white/80 leading-relaxed">
                        {{ strip_tags($term->short_description) }}
                    </p>
                @endif
            </div>
        </section>

        {{-- Posts --}}
        <main class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex items-center gap-2 mb-8">
                <span class="text-gray-400">⎇</span>
                <span class="text-sm text-gray-500">
                    {{ $posts->total() }} article{{ $posts->total() !== 1 ? 's' : '' }}
                </span>
            </div>

            @if($posts->count() === 0)
                <div class="text-center py-24">
                    <p class="text-3xl mb-3">🏔️</p>
                    <p class="text-gray-500 text-lg font-medium">No articles for this destination yet</p>
                    <a href="{{ route('front.blog.index') }}" class="mt-4 inline-block text-primary text-sm hover:underline">
                        Browse all articles
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

            @if($term->description)
                <section class="mt-16 max-w-3xl mx-auto border-t border-gray-100 pt-12" aria-label="{{ $term->name }}">
                    <div class="prose prose-slate max-w-none text-gray-600 leading-relaxed">
                        {!! $term->description !!}
                    </div>
                </section>
            @endif
        </main>
@endsection
