@php
    /** @var \App\Models\User $user */
@endphp
@extends('front.layouts.app')

@section('title', __('front.nav_my_bookings').' — '.config('app.name'))

@section('content')
        <main class="pt-20">
            <section class="px-6 sm:px-8 py-10">
                <div class="max-w-5xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('front.nav_my_bookings') }}</h1>
                    <p class="text-sm text-gray-500 mt-2">Coming next: connect to your bookings API and show real reservations here.</p>
                    <a class="mt-6 inline-flex items-center rounded-xl bg-primary px-4 py-2 text-sm text-primary-foreground hover:bg-primary-hover transition" href="{{ route('front.rentals.index') }}">
                        Browse rentals
                    </a>
                </div>
            </section>
        </main>
@endsection

