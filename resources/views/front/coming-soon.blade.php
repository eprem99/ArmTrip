@extends('front.layouts.app')

@section('hide_nav', '1')
@section('hide_footer', '1')

@section('title', \App\Models\Option::get('site_coming_soon_title', '') ?: (\App\Models\Option::get('organization_name', '') ?: config('app.name')))

@section('content')
@php
    $title = \App\Models\Option::get('site_coming_soon_title', '') ?: __('Coming soon');
    $message = \App\Models\Option::get('site_coming_soon_message', '') ?: __('We’re working on something great. Please check back soon.');
@endphp

<main class="min-h-screen flex items-center justify-center px-6 py-16">
    <div class="w-full max-w-xl text-center">
        <div class="mx-auto mb-6 h-14 w-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl">
            ⏳
        </div>
        <h1 class="text-3xl sm:text-4xl font-semibold text-slate-900">{{ $title }}</h1>
        <p class="mt-4 text-slate-600 text-base leading-relaxed">
            {{ $message }}
        </p>
        <p class="mt-8 text-xs text-slate-400">
            {{ \App\Models\Option::get('organization_name', '') ?: config('app.name') }}
        </p>
    </div>
</main>
@endsection

