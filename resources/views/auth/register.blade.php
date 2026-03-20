<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('auth.register_title') }} | {{ config('app.name') }}</title>

        @vite(['resources/css/app.css'])
    </head>
    <body class="bg-slate-100 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded shadow p-6">
            <h1 class="text-2xl font-bold mb-4 text-center">{{ __('auth.register_title') }}</h1>

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm mb-1" for="name">{{ __('auth.name') }}</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm mb-1" for="email">{{ __('auth.email') }}</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm mb-1" for="password">{{ __('auth.password') }}</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm mb-1" for="password_confirmation">{{ __('auth.password_confirmation') }}</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div class="flex items-center justify-between text-sm">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                        {{ __('auth.already_account') }}
                    </a>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black text-white py-2 rounded text-sm font-medium hover:bg-gray-900"
                >
                    {{ __('auth.submit_register') }}
                </button>
            </form>
        </div>
    </body>
</html>

