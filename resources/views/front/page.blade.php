@extends('front.layouts.app')

@section('title', $page->title.' — '.config('app.name'))

@section('content')
    <main class="mx-auto max-w-5xl px-4 py-10 pt-20">
        <h1 class="text-3xl font-semibold">{{ $page->title }}</h1>
        <article class="prose prose-slate mt-6 max-w-none">
            {!! $page->content !!}
        </article>
    </main>
@endsection

