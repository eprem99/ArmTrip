@php
    /** @var \App\Models\User $user */
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('front.nav_settings') }} — {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
    </head>
    <body class="min-h-screen bg-background text-foreground font-sans">
        @include('front.partials.nav')
        <main class="pt-20">
            <section class="px-6 sm:px-8 py-10">
                <div class="max-w-5xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('front.nav_settings') }}</h1>
                    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <p class="text-sm text-gray-500">Пока показываем данные профиля. Следующий шаг — форма редактирования.</p>
                        <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-widest">Name</dt>
                                <dd class="text-sm text-gray-800 font-medium mt-1">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-widest">Email</dt>
                                <dd class="text-sm text-gray-800 font-medium mt-1">{{ $user->email }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>
        </main>
        @include('front.partials.footer')
    </body>
</html>

