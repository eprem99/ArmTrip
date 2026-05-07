<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('front.rentals_meta_title') }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('front.rentals_meta_description') }}">
    @vite(['resources/css/app.css', 'resources/js/frontend/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground font-sans">
    @include('front.partials.nav')

    @php
        $q = (string) request()->query('q', '');
        $type = (string) request()->query('type', '');
        $locationId = (string) request()->query('location_id', '');
        $priceMin = (string) request()->query('price_min', '');
        $priceMax = (string) request()->query('price_max', '');
        $guests = (string) request()->query('guests', '2');
        $ratingMin = (string) request()->query('rating_min', '0');
        $sort = (string) request()->query('sort', 'popularity');
        $amenitiesSelected = request()->query('amenities', []);
        $amenitiesSelected = is_array($amenitiesSelected) ? $amenitiesSelected : array_filter(array_map('trim', explode(',', (string) $amenitiesSelected)));

        $priceMinInt = is_numeric($priceMin) ? (int) $priceMin : 0;
        $priceMaxInt = is_numeric($priceMax) ? (int) $priceMax : 400;
        $priceMinInt = max(0, min(400, $priceMinInt));
        $priceMaxInt = max(0, min(400, $priceMaxInt));
        if ($priceMinInt > $priceMaxInt) { $tmp = $priceMinInt; $priceMinInt = $priceMaxInt; $priceMaxInt = $tmp; }

        $demo = [
            [
                'title' => 'Mountain View Retreat',
                'location' => 'Dilijan, Armenia',
                'type' => 'cottage',
                'type_label' => 'Cottage',
                'type_emoji' => '🏡',
                'price' => 85,
                'rating' => 4.9,
                'reviews' => 128,
                'images' => [
                    ['src' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png', 'alt' => 'Cozy stone cottage in Armenian mountains'],
                    ['src' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png', 'alt' => 'Hikers on Armenian mountain trail'],
                ],
                'amenities' => ['wifi', 'kitchen', 'mountain'],
                'instant' => true,
                'beds' => 2,
                'baths' => 1,
                'max' => 4,
                'area' => 52,
            ],
            [
                'title' => 'Sevan Lakeside Villa',
                'location' => 'Lake Sevan, Armenia',
                'type' => 'villa',
                'type_label' => 'Villa',
                'type_emoji' => '🏖️',
                'price' => 120,
                'rating' => 4.8,
                'reviews' => 94,
                'images' => [
                    ['src' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png', 'alt' => 'Lake Sevan shoreline panorama'],
                    ['src' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png', 'alt' => 'Yerevan city skyline with Mount Ararat'],
                ],
                'amenities' => ['wifi', 'kitchen', 'parking'],
                'instant' => false,
                'beds' => 3,
                'baths' => 2,
                'max' => 6,
                'area' => 88,
            ],
            [
                'title' => 'Yerevan Heritage Suite',
                'location' => 'Yerevan, Armenia',
                'type' => 'apartment',
                'type_label' => 'Apartment',
                'type_emoji' => '🏙️',
                'price' => 95,
                'rating' => 4.7,
                'reviews' => 211,
                'images' => [
                    ['src' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png', 'alt' => 'Yerevan city skyline with Mount Ararat'],
                    ['src' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png', 'alt' => 'Garni Temple in Armenia'],
                ],
                'amenities' => ['wifi', 'ac', 'tv'],
                'instant' => true,
                'beds' => 1,
                'baths' => 1,
                'max' => 2,
                'area' => 34,
            ],
        ];

        $amenityIcons = [
            'wifi' => '📶',
            'parking' => '🅿️',
            'pool' => '🏊',
            'ac' => '❄️',
            'kitchen' => '🍳',
            'washing' => '🫧',
            'tv' => '📺',
            'heating' => '🔥',
            'balcony' => '🌿',
            'gym' => '💪',
            'breakfast' => '☕',
            'petFriendly' => '🐾',
            'bbq' => '🥩',
            'fireplace' => '🪵',
            'garden' => '🌳',
            'hotTub' => '🛁',
            'mountain' => '⛰️',
        ];
        $amenityLabels = [
            'wifi' => 'WiFi',
            'parking' => 'Parking',
            'pool' => 'Pool',
            'ac' => 'A/C',
            'kitchen' => 'Kitchen',
            'washing' => 'Washer',
            'tv' => 'TV',
            'heating' => 'Heating',
            'balcony' => 'Balcony',
            'gym' => 'Gym',
            'breakfast' => 'Breakfast',
            'petFriendly' => 'Pets OK',
            'bbq' => 'BBQ',
            'fireplace' => 'Fireplace',
            'garden' => 'Garden',
            'hotTub' => 'Hot Tub',
            'mountain' => 'Mountain View',
        ];

        $items = $rentals->count() ? $rentals : collect($demo);
    @endphp

    <main class="pt-20">
        <!-- Top search header (design) -->
        <section class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 py-8">
                <nav class="flex items-center gap-2 text-xs text-gray-400 mb-3" aria-label="Breadcrumb">
                    <a href="{{ route('front.home') }}" class="hover:text-blue-500 transition-colors">Home</a>
                    <span>/</span>
                    <span class="text-gray-600 font-medium">Rentals</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight" style="font-family:'Poppins',sans-serif;">
                    Find Your Perfect Stay in Armenia
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ (int) ($rentals->total() ?? $items->count()) }} handpicked properties — apartments, villas, cottages &amp; more
                </p>

                @php
                    $where = (string) request()->query('where', '');
                    $checkIn = (string) request()->query('check_in', '');
                    $checkOut = (string) request()->query('check_out', '');
                @endphp

                <form method="GET" action="{{ route('front.rentals.index') }}" class="mt-6">
                    <div class="rounded-2xl border border-gray-150 bg-white shadow-sm overflow-hidden" style="border-color:hsl(210,14%,93%);">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                            <!-- WHERE -->
                            <div class="md:col-span-3 px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Where</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-blue-500">📍</span>
                                    <input
                                        name="where"
                                        value="{{ $where }}"
                                        placeholder="Any"
                                        class="w-full bg-transparent text-sm text-gray-800 outline-none placeholder:text-gray-400"
                                    />
                                </div>
                            </div>
                            <!-- CHECK IN -->
                            <div class="md:col-span-2 px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Check in</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-blue-500">📅</span>
                                    <input
                                        type="date"
                                        name="check_in"
                                        value="{{ $checkIn }}"
                                        class="w-full bg-transparent text-sm text-gray-800 outline-none"
                                    />
                                </div>
                            </div>
                            <!-- CHECK OUT -->
                            <div class="md:col-span-2 px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Check out</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-blue-500">📅</span>
                                    <input
                                        type="date"
                                        name="check_out"
                                        value="{{ $checkOut }}"
                                        class="w-full bg-transparent text-sm text-gray-800 outline-none"
                                    />
                                </div>
                            </div>
                            <!-- GUESTS -->
                            <div class="md:col-span-2 px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Guests</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-blue-500">👥</span>
                                    <select name="guests" class="w-full bg-transparent text-sm text-gray-800 outline-none cursor-pointer">
                                        @foreach([1,2,3,4,5,6,7,8] as $n)
                                            <option value="{{ $n }}" @selected((string) $n === (string) $guests)>{{ $n }} guests</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- TYPE -->
                            <div class="md:col-span-2 px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Type</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-blue-500">🏠</span>
                                    <select name="type" class="w-full bg-transparent text-sm text-gray-800 outline-none cursor-pointer">
                                        <option value="">All Types</option>
                                        @foreach($types as $t)
                                            <option value="{{ $t->slug }}" @selected($type === $t->slug)>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- SEARCH BUTTON -->
                            <div class="md:col-span-1 p-3 flex items-stretch">
                                <button type="submit" class="w-full rounded-2xl bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-sm transition">
                                    <span>🔎</span>
                                    <span class="hidden md:inline">Search</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- preserve sidebar-only params when using top search -->
                    @foreach($amenitiesSelected as $a)
                        <input type="hidden" name="amenities[]" value="{{ $a }}">
                    @endforeach
                    <input type="hidden" name="price_min" value="{{ $priceMinInt }}">
                    <input type="hidden" name="price_max" value="{{ $priceMaxInt }}">
                    <input type="hidden" name="rating_min" value="{{ $ratingMin }}">
                    <input type="hidden" name="location_id" value="{{ $locationId }}">
                    <input type="hidden" name="instant" value="{{ request()->boolean('instant') ? '1' : '0' }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="q" value="{{ $q }}">
                </form>

                <!-- Type chips + Instant + Sort + View mode (design) -->
                <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $chipBase = array_merge(request()->query(), ['page' => null]);
                            $chipUrl = function (array $over) use ($chipBase) {
                                $q = array_merge($chipBase, $over);
                                return route('front.rentals.index', array_filter($q, fn ($v) => $v !== null && $v !== ''));
                            };
                        @endphp
                        <a href="{{ $chipUrl(['type' => '']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm border transition {{ $type === '' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600' }}">
                            🔥 <span>All Types</span>
                        </a>
                        @foreach($types as $t)
                            <a href="{{ $chipUrl(['type' => $t->slug]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm border transition {{ $type === $t->slug ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600' }}">
                                🏠 <span>{{ $t->name }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ $chipUrl(['instant' => request()->boolean('instant') ? '0' : '1']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm border transition {{ request()->boolean('instant') ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-white text-gray-600 border-gray-200 hover:border-amber-200 hover:text-amber-700' }}">
                            ⚡ <span>Instant Book</span>
                        </a>

                        <form method="GET" action="{{ route('front.rentals.index') }}" class="flex items-center gap-2">
                            @foreach(request()->query() as $k => $v)
                                @if($k === 'sort') @continue @endif
                                @if(is_array($v))
                                    @foreach($v as $vv)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <select name="sort" class="px-3 py-2 rounded-full border border-gray-200 bg-white text-sm text-gray-700 hover:border-blue-300 transition cursor-pointer">
                                <option value="popularity" @selected($sort === 'popularity')>🔥 Popular</option>
                                <option value="rating" @selected($sort === 'rating')>⭐ Rating</option>
                                <option value="price_asc" @selected($sort === 'price_asc')>↑ Price</option>
                                <option value="price_desc" @selected($sort === 'price_desc')>↓ Price</option>
                            </select>
                        </form>

                        <button type="button" class="h-10 w-10 inline-flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition" aria-label="Grid view">
                            ▦
                        </button>
                        <button type="button" class="h-10 w-10 inline-flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition" aria-label="List view">
                            ≡
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-6 sm:px-8 py-10 bg-background">
            <div class="max-w-7xl mx-auto">

                <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
                    <!-- Sidebar filters (design) -->
                    <aside class="lg:col-span-3">
                        @php
                            $instant = request()->boolean('instant');
                            $activeFilterCount =
                                ($type !== '' ? 1 : 0)
                                + ($locationId !== '' ? 1 : 0)
                                + ($priceMinInt > 0 ? 1 : 0)
                                + ($priceMaxInt < 400 ? 1 : 0)
                                + (((float) $ratingMin) > 0 ? 1 : 0)
                                + ((int) $guests > 2 ? 1 : 0)
                                + ($instant ? 1 : 0)
                                + count($amenitiesSelected)
                                + (trim($q) !== '' ? 1 : 0);

                            $amenitiesMeta = [
                                'wifi' => ['emoji' => '📶', 'label' => 'WiFi'],
                                'parking' => ['emoji' => '🅿️', 'label' => 'Parking'],
                                'pool' => ['emoji' => '🏊', 'label' => 'Pool'],
                                'ac' => ['emoji' => '❄️', 'label' => 'A/C'],
                                'kitchen' => ['emoji' => '🍳', 'label' => 'Kitchen'],
                                'washing' => ['emoji' => '🫧', 'label' => 'Washer'],
                                'tv' => ['emoji' => '📺', 'label' => 'TV'],
                                'heating' => ['emoji' => '🔥', 'label' => 'Heating'],
                                'balcony' => ['emoji' => '🌿', 'label' => 'Balcony'],
                                'gym' => ['emoji' => '💪', 'label' => 'Gym'],
                                'breakfast' => ['emoji' => '☕', 'label' => 'Breakfast'],
                                'petFriendly' => ['emoji' => '🐾', 'label' => 'Pets OK'],
                                'bbq' => ['emoji' => '🥩', 'label' => 'BBQ'],
                                'fireplace' => ['emoji' => '🪵', 'label' => 'Fireplace'],
                                'garden' => ['emoji' => '🌳', 'label' => 'Garden'],
                                'hotTub' => ['emoji' => '🛁', 'label' => 'Hot Tub'],
                            ];
                        @endphp

                        <form method="GET" action="{{ route('front.rentals.index') }}" class="sticky top-24 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm max-w-[340px]" style="border-color:hsl(210,14%,93%);">
                            <div class="flex items-center justify-between">
                                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Poppins',sans-serif;">Filters</h2>
                                <a href="{{ route('front.rentals.index') }}" class="text-xs font-medium text-gray-400 hover:text-blue-600 transition">Reset</a>
                            </div>

                            <div class="mt-4 space-y-6">
                                <!-- Search properties text -->
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔎</span>
                                    <input
                                        type="search"
                                        name="q"
                                        value="{{ $q }}"
                                        placeholder="Search by name or keyword..."
                                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all"
                                        aria-label="Search properties"
                                    />
                                </div>

                                <div class="h-px bg-gray-100"></div>

                                <!-- Price range -->
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" style="font-family:'Poppins',sans-serif;">Price per Night</h3>
                                    <div class="flex items-center justify-between">
                                        <div class="text-center">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Min</p>
                                            <p class="text-sm font-bold text-gray-800" data-price-min-label>${{ $priceMinInt }}</p>
                                        </div>
                                        <div class="w-px h-6 bg-gray-200"></div>
                                        <div class="text-center">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Max</p>
                                            <p class="text-sm font-bold text-gray-800" data-price-max-label>${{ $priceMaxInt }}</p>
                                        </div>
                                    </div>

                                    <div class="relative h-1.5 bg-gray-100 rounded-full mx-2 mt-3" data-price-track data-min="0" data-max="400">
                                        <div class="absolute h-1.5 rounded-full" data-price-fill style="left:0; right:0; background: linear-gradient(90deg, #3b82f6, #06b6d4);"></div>
                                        <input type="range" min="0" max="400" value="{{ $priceMinInt }}" class="absolute inset-0 w-full opacity-0 h-1.5 cursor-pointer" aria-label="Minimum price" data-price-min />
                                        <input type="range" min="0" max="400" value="{{ $priceMaxInt }}" class="absolute inset-0 w-full opacity-0 h-1.5 cursor-pointer" aria-label="Maximum price" data-price-max />
                                        <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-white border-2 border-blue-500 shadow-md pointer-events-none" data-price-thumb-min></div>
                                        <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-white border-2 border-blue-500 shadow-md pointer-events-none" data-price-thumb-max></div>
                                    </div>

                                    <input type="hidden" name="price_min" value="{{ $priceMinInt }}" data-price-min-hidden />
                                    <input type="hidden" name="price_max" value="{{ $priceMaxInt }}" data-price-max-hidden />
                                </div>

                                <div class="h-px bg-gray-100"></div>

                                <!-- Property type -->
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" style="font-family:'Poppins',sans-serif;">Property Type</h3>
                                    <div class="space-y-1.5">
                                        @foreach($types as $t)
                                            @php $checked = $type === $t->slug; @endphp
                                            <label class="flex items-center justify-between cursor-pointer group py-0.5">
                                                <div class="flex items-center gap-3">
                                                    <input type="radio" name="type" value="{{ $t->slug }}" class="sr-only peer" @checked($checked) />
                                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-all border-gray-300 bg-white peer-checked:bg-blue-500 peer-checked:border-blue-500 group-hover:border-blue-400">
                                                        <svg class="hidden peer-checked:block" width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 4L3.5 6.5L9 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors select-none">
                                                        {{ $t->name }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-100">{{ (int) $t->rentals_count }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="h-px bg-gray-100"></div>

                                <!-- Location -->
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" style="font-family:'Poppins',sans-serif;">Location</h3>
                                    <div class="space-y-1.5">
                                        @foreach($locations as $loc)
                                            @continue((int) $loc->rentals_count === 0)
                                            @php $checked = (string) $loc->id === (string) $locationId; @endphp
                                            <label class="flex items-center justify-between cursor-pointer group py-0.5">
                                                <div class="flex items-center gap-3">
                                                    <input type="radio" name="location_id" value="{{ $loc->id }}" class="sr-only peer" @checked($checked) />
                                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-all border-gray-300 bg-white peer-checked:bg-blue-500 peer-checked:border-blue-500 group-hover:border-blue-400">
                                                        <svg class="hidden peer-checked:block" width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 4L3.5 6.5L9 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors select-none">
                                                        {{ $loc->name }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-100">{{ (int) $loc->rentals_count }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="h-px bg-gray-100"></div>

                                <!-- Amenities -->
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" style="font-family:'Poppins',sans-serif;">Amenities</h3>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($amenitiesMeta as $key => $meta)
                                            @php $isOn = in_array($key, $amenitiesSelected, true); @endphp
                                            <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-all select-none text-xs {{ $isOn ? 'border-blue-400 bg-blue-50 text-blue-700' : 'border-gray-100 bg-gray-50 text-gray-600 hover:border-gray-200 hover:bg-white' }}">
                                                <input type="checkbox" name="amenities[]" value="{{ $key }}" class="sr-only" @checked($isOn) />
                                                <span>{{ $meta['emoji'] }}</span>
                                                <span class="leading-tight font-medium">{{ $meta['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="h-px bg-gray-100"></div>

                                <!-- Rating -->
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" style="font-family:'Poppins',sans-serif;">Guest Rating</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @php
                                            $ratingOptions = [
                                                ['v' => 0, 'label' => 'Any'],
                                                ['v' => 4, 'label' => '4+'],
                                                ['v' => 4.5, 'label' => '4.5+'],
                                                ['v' => 4.8, 'label' => '4.8+'],
                                            ];
                                        @endphp
                                        @foreach($ratingOptions as $opt)
                                            @php $isOn = (string) $opt['v'] === (string) $ratingMin; @endphp
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating_min" value="{{ $opt['v'] }}" class="sr-only peer" @checked($isOn) />
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium border transition-all
                                                    {{ $isOn ? 'bg-blue-500 text-white border-blue-500 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600' }}">
                                                    @if((float) $opt['v'] > 0)<span class="text-amber-400">★</span>@endif
                                                    {{ $opt['label'] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="h-px bg-gray-100"></div>

                                <!-- Instant Booking -->
                                <div>
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                                                <span class="text-amber-500">⚡</span>
                                                Instant Booking
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">Reserve without waiting for approval</p>
                                        </div>
                                        <button
                                            type="button"
                                            data-instant-toggle
                                            class="relative w-11 h-6 rounded-full transition-colors duration-200 cursor-pointer shrink-0 {{ $instant ? 'bg-blue-500' : 'bg-gray-200' }}"
                                            role="switch"
                                            aria-checked="{{ $instant ? 'true' : 'false' }}"
                                        >
                                            <span class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 {{ $instant ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                        <input type="hidden" name="instant" value="{{ $instant ? '1' : '0' }}" data-instant-hidden />
                                    </label>
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sort</label>
                                    <select name="sort" class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition">
                                        <option value="popularity" @selected($sort === 'popularity')>Popularity</option>
                                        <option value="rating" @selected($sort === 'rating')>Rating</option>
                                        <option value="price_asc" @selected($sort === 'price_asc')>Price: Low → High</option>
                                        <option value="price_desc" @selected($sort === 'price_desc')>Price: High → Low</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full rounded-xl bg-primary px-5 py-3 text-sm text-primary-foreground hover:bg-primary-hover transition">
                                    Apply filters
                                </button>

                                @if($activeFilterCount > 0)
                                    <a
                                        href="{{ route('front.rentals.index') }}"
                                        class="block w-full text-center py-2.5 text-sm text-gray-500 hover:text-blue-600 border border-dashed border-gray-300 hover:border-blue-300 rounded-xl transition-all font-medium hover:bg-blue-50"
                                    >
                                        Clear all filters ({{ $activeFilterCount }})
                                    </a>
                                @endif
                            </div>
                        </form>
                    </aside>

                    <!-- Results -->
                    <div class="lg:col-span-9">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ $rentals->total() ?? $items->count() }}</span> results
                            </div>
                        </div>

                        <!-- Results grid -->
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($items as $row)
                        @php
                            $isModel = $row instanceof \App\Models\Rental;
                            $title = $isModel ? $row->title : $row['title'];
                            $price = $isModel ? (float) $row->base_price : (float) $row['price'];
                            $rating = $isModel ? (float) $row->rating_average : (float) $row['rating'];
                            $reviews = $isModel ? (int) $row->ratings_count : (int) $row['reviews'];
                            $locLabel = $isModel ? ($row->location?->name ?? 'Armenia') : $row['location'];
                            $typeLabel = $isModel ? ($row->type?->name ?? 'Rental') : $row['type_label'];
                            $typeEmoji = $isModel ? '🏠' : $row['type_emoji'];
                            $amenities = $isModel ? $row->amenities->pluck('slug')->take(5)->all() : $row['amenities'];
                            $images = $isModel
                                ? $row->images->sortBy([['is_primary','desc'],['sort_order','asc']])->values()->map(fn ($i) => ['src' => $i->path, 'alt' => $i->alt ?: $title])->take(5)->all()
                                : $row['images'];
                            $img0 = $images[0]['src'] ?? 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png';
                            $alt0 = $images[0]['alt'] ?? $title;
                            $href = $isModel
                                ? route('front.rentals.show', ['typeSlug' => $row->type->slug, 'locationSlug' => $row->location->slug, 'slug' => $row->slug])
                                : route('front.rentals.index');
                            $night = number_format($price, 0, '.', ' ');
                            $dots = count($images);
                        @endphp

                        <a href="{{ $href }}" class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-200/70 hover:-translate-y-1 transition-all duration-300 h-full" style="border-color:hsl(210,14%,93%);">
                            <div class="relative overflow-hidden shrink-0" style="height: 226px" data-gallery>
                                <img src="{{ $img0 }}" alt="{{ $alt0 }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" data-gallery-img />
                                <div class="absolute inset-0 bg-linear-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true"></div>

                                <button type="button" class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 hover:bg-white shadow-md transition cursor-pointer" aria-label="Save to wishlist" data-wish>
                                    ♡
                                </button>

                                <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                    @if($rating >= 4.9)
                                        <span class="flex items-center gap-1 text-[10px] font-bold bg-amber-400 text-white px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wide">⭐ Top Rated</span>
                                    @endif
                                </div>

                                @if($dots > 1)
                                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200" data-gallery-dots>
                                        @for($i = 0; $i < $dots; $i++)
                                            <button type="button" class="{{ $i === 0 ? 'bg-white w-4 h-1.5' : 'bg-white/60 hover:bg-white/90 w-1.5 h-1.5' }} rounded-full transition-all duration-200 cursor-pointer" aria-label="Image {{ $i + 1 }}" data-dot="{{ $i }}"></button>
                                        @endfor
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-center justify-between gap-3 mb-2">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border bg-gray-50 text-gray-700 border-gray-200">
                                        {{ $typeEmoji }} {{ $typeLabel }}
                                    </span>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900 text-lg">${{ $night }}</p>
                                        <p class="text-[11px] text-gray-400 -mt-1">/ night</p>
                                    </div>
                                </div>

                                <h3 class="font-semibold text-gray-900 text-base leading-snug group-hover:text-blue-600 transition-colors duration-200" style="font-family:'Poppins',sans-serif;">
                                    {{ $title }}
                                </h3>
                                <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                                    <span class="text-blue-400">📍</span>
                                    <span>{{ $locLabel }}</span>
                                </div>

                                <div class="flex gap-1.5 flex-wrap mt-3 mb-auto">
                                    @foreach(array_slice($amenities, 0, 5) as $a)
                                        <span class="text-xs bg-gray-50 text-gray-500 px-2 py-0.5 rounded-full border border-gray-100">
                                            {{ $amenityIcons[$a] ?? '•' }} {{ $amenityLabels[$a] ?? ucfirst(str_replace(['_', '-'], ' ', (string) $a)) }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-amber-400">★</span>
                                        <span class="text-sm font-bold text-gray-800">{{ number_format($rating, 1) }}</span>
                                        <span class="text-xs text-gray-400">({{ $reviews }} reviews)</span>
                                    </div>
                                    <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full font-medium border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                                        View Details →
                                    </span>
                                </div>
                            </div>
                        </a>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            @if($rentals instanceof \Illuminate\Contracts\Pagination\Paginator)
                                {{ $rentals->withQueryString()->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
(() => {
    // wishlist toggle
    document.querySelectorAll('[data-wish]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            btn.textContent = (btn.textContent || '').trim() === '♡' ? '♥' : '♡';
            btn.classList.toggle('text-rose-500');
        });
    });

    // simple image dots switching (uses data-images JSON if present later; for now only dots visual)
    document.querySelectorAll('[data-gallery]').forEach((gallery) => {
        const img = gallery.querySelector('[data-gallery-img]');
        const dots = gallery.querySelectorAll('[data-gallery-dots] [data-dot]');
        if (!img || dots.length <= 1) return;
        // no-op: placeholder for future multi-image swapping
    });
})();
    </script>

    <script>
(() => {
    const track = document.querySelector('[data-price-track]');
    if (!track) return;
    const minEl = track.querySelector('[data-price-min]');
    const maxEl = track.querySelector('[data-price-max]');
    const fill = track.querySelector('[data-price-fill]');
    const thumbMin = track.querySelector('[data-price-thumb-min]');
    const thumbMax = track.querySelector('[data-price-thumb-max]');
    const labelMin = document.querySelector('[data-price-min-label]');
    const labelMax = document.querySelector('[data-price-max-label]');
    const hiddenMin = document.querySelector('[data-price-min-hidden]');
    const hiddenMax = document.querySelector('[data-price-max-hidden]');

    if (!minEl || !maxEl || !fill || !thumbMin || !thumbMax || !labelMin || !labelMax || !hiddenMin || !hiddenMax) return;

    const min = Number(track.getAttribute('data-min') || 0);
    const max = Number(track.getAttribute('data-max') || 400);

    const pct = (v) => ((v - min) / (max - min)) * 100;

    const render = () => {
        let lo = Number(minEl.value);
        let hi = Number(maxEl.value);
        if (lo > hi - 10) lo = hi - 10;
        if (hi < lo + 10) hi = lo + 10;
        lo = Math.max(min, Math.min(max, lo));
        hi = Math.max(min, Math.min(max, hi));
        minEl.value = String(lo);
        maxEl.value = String(hi);

        const loPct = pct(lo);
        const hiPct = pct(hi);
        fill.style.left = `${loPct}%`;
        fill.style.right = `${100 - hiPct}%`;
        thumbMin.style.left = `calc(${loPct}% - 8px)`;
        thumbMax.style.left = `calc(${hiPct}% - 8px)`;
        labelMin.textContent = `$${lo}`;
        labelMax.textContent = `$${hi}`;
        hiddenMin.value = String(lo);
        hiddenMax.value = String(hi);
    };

    minEl.addEventListener('input', render);
    maxEl.addEventListener('input', render);
    render();
})();
    </script>

    <script>
(() => {
    const btn = document.querySelector('[data-instant-toggle]');
    const hidden = document.querySelector('[data-instant-hidden]');
    if (!btn || !hidden) return;

    btn.addEventListener('click', () => {
        const isOn = hidden.value === '1';
        hidden.value = isOn ? '0' : '1';
        btn.setAttribute('aria-checked', hidden.value === '1' ? 'true' : 'false');
        btn.classList.toggle('bg-blue-500', hidden.value === '1');
        btn.classList.toggle('bg-gray-200', hidden.value !== '1');
        const knob = btn.querySelector('span');
        if (knob) {
            knob.classList.toggle('translate-x-6', hidden.value === '1');
            knob.classList.toggle('translate-x-1', hidden.value !== '1');
        }
    });
})();
    </script>

    @include('front.partials.footer')
</body>
</html>
