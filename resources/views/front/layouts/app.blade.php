@php
    $orgName = \App\Models\Option::get('organization_name', '') ?: config('app.name');
    $orgDesc = \App\Models\Option::get('organization_description', '');
    $favicon = \App\Models\Option::get('organization_favicon', '');

    $pageTitle = trim((string) $__env->yieldContent('title'));
    $metaDescription = trim((string) $__env->yieldContent('meta_description'));
    $canonical = trim((string) $__env->yieldContent('canonical'));

    $computedTitle = $pageTitle !== '' ? $pageTitle : $orgName;
    $computedDescription = $metaDescription !== '' ? $metaDescription : $orgDesc;

    $hideNav = trim((string) $__env->yieldContent('hide_nav')) === '1';
    $hideFooter = trim((string) $__env->yieldContent('hide_footer')) === '1';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $computedTitle }}</title>
        @if(filled($computedDescription))
            <meta name="description" content="{{ $computedDescription }}">
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(filled($canonical))
            <link rel="canonical" href="{{ $canonical }}">
        @endif
        @if(filled($favicon))
            <link rel="icon" href="{{ $favicon }}">
        @endif

        @stack('head')
        @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
    </head>
    <body class="min-h-screen bg-background text-foreground font-sans">
        @if(! $hideNav)
            @include('front.partials.nav')
        @endif

        @yield('content')

        @if(! $hideFooter)
            @include('front.partials.footer')
        @endif
        @stack('scripts')
    </body>
</html>

