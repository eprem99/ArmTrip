<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $rental->meta_title ?? $rental->title }}</title>
    @if(filled($rental->meta_description))
        <meta name="description" content="{{ $rental->meta_description }}">
    @endif
    <link rel="canonical" href="{{ url()->route('front.rentals.show', [
        'typeSlug' => $rental->type->slug,
        'locationSlug' => $rental->location->slug,
        'slug' => $rental->slug,
    ]) }}">
    @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground font-sans">
    @include('front.partials.nav')

    <main class="mx-auto max-w-5xl px-4 py-10 pt-20">
        <h1 class="text-3xl font-semibold text-slate-900">{{ $rental->title }}</h1>
        <p class="mt-2 text-slate-600">{{ $rental->location->name }} · {{ $rental->type->name }}</p>
        @if($rental->description)
            <article class="prose prose-slate mt-6 max-w-none">
                {!! nl2br(e($rental->description)) !!}
            </article>
        @endif
    </main>

    @include('front.partials.footer')
</body>
</html>
