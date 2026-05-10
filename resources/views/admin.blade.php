@extends('layouts.admin')

@section('title', 'Admin')

@section('head')
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
@endsection

@section('content')
    <div id="admin-app"></div>
@endsection

