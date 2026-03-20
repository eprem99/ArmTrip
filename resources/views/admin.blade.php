<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin | {{ config('app.name') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $blogTaxonomies = \App\Models\Taxonomy::query()
                ->orderBy('name')
                ->get(['name', 'slug', 'type', 'icon', 'image']);
        @endphp
        <script>
            window.__locale = @json(app()->getLocale());
            window.__userId = @json(auth()->id());
            window.__blogTaxonomies = @json($blogTaxonomies);
            window.__translations = {
                admin: @json(trans('admin')),
                settings: @json(trans('settings')),
            };
        </script>

        @vite(['resources/css/app.css', 'resources/js/admin/admin.js'])
    </head>
    <body class="bg-slate-100">
        <div id="admin-app"></div>
    </body>
</html>

