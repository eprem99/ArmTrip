<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @yield('head')
        @vite(['resources/css/app.css', 'resources/js/admin/admin.js'])
    </head>
    <body class="bg-slate-100">
        @yield('content')
    </body>
</html>

