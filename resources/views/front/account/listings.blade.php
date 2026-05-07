@php
    /** @var \App\Models\User $user */
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('front.nav_my_listings') }} — {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
    </head>
    <body class="min-h-screen bg-background text-foreground font-sans">
        @include('front.partials.nav')
        <main class="pt-20">
            <section class="px-6 sm:px-8 py-10">
                <div class="max-w-5xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('front.nav_my_listings') }}</h1>
                    <p class="text-sm text-gray-500 mt-2">Пока ведёт в админку для управления Rentals. Следующий шаг — сделать фронтовый кабинет владельца.</p>
                    <a class="mt-6 inline-flex items-center rounded-xl bg-primary px-4 py-2 text-sm text-primary-foreground hover:bg-primary-hover transition" href="{{ \Illuminate\Support\Facades\Route::has('admin.rentals') ? route('admin.rentals') : '#' }}">
                        Open rentals manager
                    </a>
                </div>
            </section>
        </main>
        @include('front.partials.footer')
    </body>
</html>

