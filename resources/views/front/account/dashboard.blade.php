@php
    /** @var \App\Models\User $user */
@endphp
@extends('front.layouts.app')

@section('title', __('front.dashboard').' — '.config('app.name'))

@section('content')
        <main class="pt-20">
            <section class="px-6 sm:px-8 py-10 bg-background">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">Account</p>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                                {{ __('front.dashboard') }}
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $user->name }} — {{ $user->email }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('front.rentals.index') }}" class="inline-flex items-center rounded-xl bg-muted px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 transition">
                                Browse rentals
                            </a>
                            <a href="{{ \Illuminate\Support\Facades\Route::has('admin.rentals') ? route('admin.rentals') : '#' }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2 text-sm text-primary-foreground hover:bg-primary-hover transition">
                                {{ __('front.nav_add_listing') }}
                            </a>
                        </div>
                    </div>

                    <div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <aside class="lg:col-span-3">
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sticky top-24">
                                <nav class="space-y-1 text-sm">
                                    <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2 bg-primary/10 text-primary font-medium">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('front.account.bookings') }}" class="block rounded-xl px-3 py-2 text-gray-700 hover:bg-muted">
                                        {{ __('front.nav_my_bookings') }}
                                    </a>
                                    <a href="{{ route('front.account.listings') }}" class="block rounded-xl px-3 py-2 text-gray-700 hover:bg-muted">
                                        {{ __('front.nav_my_listings') }}
                                    </a>
                                    <a href="{{ route('front.account.settings') }}" class="block rounded-xl px-3 py-2 text-gray-700 hover:bg-muted">
                                        {{ __('front.nav_settings') }}
                                    </a>
                                    <div class="pt-2 border-t border-gray-100">
                                        <form method="post" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="w-full text-left rounded-xl px-3 py-2 text-gray-700 hover:bg-muted" type="submit">
                                                {{ __('front.nav_logout') }}
                                            </button>
                                        </form>
                                    </div>
                                </nav>
                            </div>
                        </aside>

                        <div class="lg:col-span-9">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                <a href="{{ route('front.account.bookings') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
                                    <p class="text-xs text-gray-400 uppercase tracking-widest">Bookings</p>
                                    <p class="mt-2 text-lg font-semibold text-gray-900">My bookings</p>
                                    <p class="mt-1 text-sm text-gray-500">View upcoming trips and past stays.</p>
                                </a>

                                <a href="{{ route('front.account.listings') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
                                    <p class="text-xs text-gray-400 uppercase tracking-widest">Listings</p>
                                    <p class="mt-2 text-lg font-semibold text-gray-900">My listings</p>
                                    <p class="mt-1 text-sm text-gray-500">Manage your properties and availability.</p>
                                </a>

                                <a href="{{ route('front.account.settings') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
                                    <p class="text-xs text-gray-400 uppercase tracking-widest">Settings</p>
                                    <p class="mt-2 text-lg font-semibold text-gray-900">Account settings</p>
                                    <p class="mt-1 text-sm text-gray-500">Update profile and preferences.</p>
                                </a>
                            </div>

                            <div class="mt-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                                <h2 class="text-lg font-semibold text-gray-900">Quick actions</h2>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a class="inline-flex items-center rounded-xl bg-muted px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 transition" href="{{ route('front.blog.index') }}">
                                        Read guides
                                    </a>
                                    <a class="inline-flex items-center rounded-xl bg-muted px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 transition" href="{{ route('front.rentals.index') }}">
                                        Find a stay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

@endsection

