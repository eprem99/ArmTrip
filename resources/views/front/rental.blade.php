@extends('front.layouts.app')

@section('title', $rental->meta_title ?? $rental->title)
@section('meta_description', filled($rental->meta_description) ? $rental->meta_description : '')
@section('canonical', url()->route('front.rentals.show', [
    'typeSlug' => $rental->type->slug,
    'locationSlug' => $rental->location->slug,
    'slug' => $rental->slug,
]))

@section('content')
    <main class="mx-auto max-w-5xl px-4 py-10 pt-20">
        <h1 class="text-3xl font-semibold text-slate-900">{{ $rental->title }}</h1>
        <p class="mt-2 text-slate-600">{{ $rental->location->name }} · {{ $rental->type->name }}</p>
        @if($rental->description)
            <article class="rich-content mt-6 max-w-none">
                {!! nl2br(e($rental->description)) !!}
            </article>
        @endif
    </main>
@endsection
