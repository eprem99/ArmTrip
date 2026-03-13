<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin | {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/admin.js'])
    </head>
    <body class="bg-slate-100">
        <div id="admin-app"></div>
    </body>
</html>

