<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('auth.login_title') }} | {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
    </head>
    <body class="min-h-screen bg-background text-foreground font-sans">
        @include('front.partials.nav')

        <main class="pt-20">
            <section class="px-6 sm:px-8 py-12">
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                    <div class="lg:col-span-5 bg-white rounded-3xl border border-gray-100 shadow-sm p-7 sm:p-10">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                            {{ __('auth.login_title') }}
                        </h1>
                        <p class="text-sm text-gray-500 mb-8">
                            Welcome back. Sign in to manage bookings and listings.
                        </p>

                        @if ($errors->any())
                            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm text-gray-600 mb-1" for="email">{{ __('auth.email') }}</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                >
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1" for="password">{{ __('auth.password') }}</label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                >
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <label class="inline-flex items-center gap-2 text-gray-600">
                                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                                    <span>{{ __('auth.remember_me') }}</span>
                                </label>

                                <a href="{{ route('register') }}" class="text-primary hover:underline">
                                    {{ __('auth.register') }}
                                </a>
                            </div>

                            <button
                                type="submit"
                                class="w-full bg-primary text-primary-foreground py-3 rounded-xl text-sm font-semibold hover:bg-primary-hover transition shadow-sm"
                            >
                                {{ __('auth.submit_login') }}
                            </button>
                        </form>
                    </div>

                    <div class="lg:col-span-7 rounded-3xl overflow-hidden border border-gray-100 shadow-sm relative">
                        <img
                            src="https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1600&h=900&fit=crop"
                            alt="Armenia"
                            class="absolute inset-0 w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/25 to-black/10"></div>
                        <div class="relative z-10 h-full p-8 sm:p-10 flex flex-col justify-end text-white">
                            <p class="text-xs uppercase tracking-widest text-white/70 mb-2">ArmTrip</p>
                            <h2 class="text-2xl sm:text-3xl font-bold leading-tight mb-2">
                                Plan your next stay in Armenia
                            </h2>
                            <p class="text-sm text-white/80 max-w-md">
                                Save favorites, manage bookings, and list your property to start earning.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('front.partials.footer')
    </body>
</html>

