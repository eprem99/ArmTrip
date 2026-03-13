<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login | {{ config('app.name') }}</title>

        @vite(['resources/css/app.css'])
    </head>
    <body class="bg-slate-100 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded shadow p-6">
            <h1 class="text-2xl font-bold mb-4 text-center">Вход</h1>

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600">
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
                    <label class="block text-sm mb-1" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm mb-1" for="password">Пароль</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full border rounded px-3 py-2 text-sm"
                    >
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span>Запомнить меня</span>
                    </label>

                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
                        Регистрация
                    </a>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black text-white py-2 rounded text-sm font-medium hover:bg-gray-900"
                >
                    Войти
                </button>
            </form>
        </div>
    </body>
</html>

