<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page->title }} — {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
    </head>
    <body class="min-h-screen bg-background text-foreground font-sans">
        @include('front.partials.nav')

        <main class="mx-auto max-w-5xl px-4 py-10 pt-20">
            <h1 class="text-3xl font-semibold">{{ $page->title }}</h1>
            <article class="prose prose-slate mt-6 max-w-none">
                {!! $page->content !!}
            </article>
        </main>

        @include('front.partials.footer')
    </body>
</html>

