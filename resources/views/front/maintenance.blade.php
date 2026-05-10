@extends('front.layouts.app')

@section('hide_nav', '1')
@section('hide_footer', '1')

@section('title', __('Maintenance').' | '.(\App\Models\Option::get('organization_name', '') ?: config('app.name')))

@section('content')
@php
    $message = \App\Models\Option::get('site_maintenance_message', '') ?: __('We’re doing some maintenance right now. Please try again later.');
@endphp

<main class="min-h-screen flex items-center justify-center px-6 py-16">
    <div class="w-full max-w-xl text-center">
        <div class="mx-auto mb-6 h-14 w-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl">
            🛠️
        </div>
        <h1 class="text-3xl sm:text-4xl font-semibold text-slate-900">{{ __('Maintenance') }}</h1>
        <p class="mt-4 text-slate-600 text-base leading-relaxed">
            {{ $message }}
        </p>
        <p class="mt-8 text-xs text-slate-400">
            {{ \App\Models\Option::get('organization_name', '') ?: config('app.name') }}
        </p>
    </div>
</main>
@endsection

