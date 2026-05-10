@extends('front.layouts.app')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
@endpush

@section('content')
        <main>
            <!-- Hero -->
            <section id="hero" class="relative w-full h-screen min-h-[600px] flex flex-col items-center justify-center overflow-hidden">
                <video
                    class="absolute inset-0 w-full h-full object-cover"
                    autoplay
                    loop
                    muted
                    playsinline
                    poster="https://c.animaapp.com/mmoxd21v67mhk5/img/ai_1-poster.png"
                >
                    <source src="https://c.animaapp.com/mmoxd21v67mhk5/img/ai_1.mp4" type="video/mp4">
                </video>
                <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.65) 100%)" aria-hidden="true"></div>

                <div class="relative z-10 flex flex-col items-center text-center px-6 sm:px-8 max-w-4xl mx-auto w-full">
                    <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-medium leading-tight mb-4" style="font-family: 'Poppins', sans-serif;">
                        Discover Armenia –<br>Land of Mountains and Legends
                    </h1>
                    <p class="text-white/90 text-lg font-light mb-8 max-w-xl">
                        Find unique stays and unforgettable experiences.
                    </p>
                    <div class="mb-8">
                        <a href="#destinations" class="inline-flex items-center rounded-xl bg-primary px-8 py-3 text-base text-primary-foreground hover:bg-primary-hover transition">
                            Start Exploring
                        </a>
                    </div>

                    <!-- Search bar (UI only) -->
                    <div class="w-full max-w-3xl">
                        <div class="bg-white/95 rounded-xl p-3 flex flex-col md:flex-row gap-2 items-stretch md:items-end border border-white/30">
                            <div class="flex items-center gap-2 flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-border">
                                <span class="text-primary">📍</span>
                                <input type="text" placeholder="Where to?" class="flex-1 bg-transparent text-foreground text-sm outline-none placeholder:text-gray-400" />
                            </div>
                            <div class="flex items-center gap-2 flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-border">
                                <span class="text-primary">📅</span>
                                <input type="date" class="flex-1 bg-transparent text-foreground text-sm outline-none cursor-pointer" />
                            </div>
                            <div class="flex items-center gap-2 flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-border">
                                <span class="text-primary">📅</span>
                                <input type="date" class="flex-1 bg-transparent text-foreground text-sm outline-none cursor-pointer" />
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-border min-w-[110px]">
                                <span class="text-primary">👥</span>
                                <select class="flex-1 bg-transparent text-foreground text-sm outline-none cursor-pointer">
                                    @foreach([1,2,3,4,5,6] as $n)
                                        <option value="{{ $n }}">{{ $n }} Guest{{ $n > 1 ? 's' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="{{ route('front.rentals.index') }}" class="inline-flex items-center justify-center gap-2 bg-primary text-primary-foreground hover:bg-primary-hover rounded-lg px-5 text-sm transition" style="min-height: 42px;">
                                🔎 <span>Search</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-0 left-0 right-0 h-24 pointer-events-none" style="background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.15))" aria-hidden="true"></div>
            </section>

            <!-- Popular Destinations -->
            <section id="destinations" class="py-24 px-6 sm:px-8 bg-background" aria-labelledby="destinations-heading">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <span class="text-primary font-medium text-sm uppercase tracking-widest">Explore</span>
                        <h2 id="destinations-heading" class="mt-2 mb-4 text-4xl font-medium text-foreground" style="font-family: 'Poppins', sans-serif;">
                            Popular Destinations
                        </h2>
                        <p class="text-muted-foreground text-base max-w-xl mx-auto">
                            From ancient monasteries to alpine lakes, discover the most beloved corners of Armenia.
                        </p>
                    </div>

                    @php
                        $destinations = [
                            ['name' => 'Yerevan', 'desc' => 'The vibrant capital city with stunning views of Mount Ararat and a rich cultural heritage.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png', 'alt' => 'Yerevan city skyline with Mount Ararat'],
                            ['name' => 'Lake Sevan', 'desc' => 'One of the largest freshwater high-altitude lakes in the world, surrounded by breathtaking scenery.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png', 'alt' => 'Lake Sevan shoreline panorama'],
                            ['name' => 'Dilijan', 'desc' => "Armenia's little Switzerland — lush forests, fresh air, and charming architecture.", 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_4.png', 'alt' => 'Dilijan forest trail in Armenia'],
                            ['name' => 'Garni & Geghard', 'desc' => 'Ancient pagan temple and medieval monastery carved into the rock — a UNESCO World Heritage Site.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png', 'alt' => 'Garni Temple in Armenia'],
                        ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($destinations as $d)
                            <div class="group rounded-xl overflow-hidden border border-border bg-card">
                                <div class="img-zoom-container h-52 overflow-hidden">
                                    <img src="{{ $d['img'] }}" alt="{{ $d['alt'] }}" loading="lazy" class="img-zoom w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                </div>
                                <div class="p-5">
                                    <h3 class="text-base font-medium text-foreground group-hover:text-primary transition-colors mb-2" style="font-family:'Poppins',sans-serif;">
                                        {{ $d['name'] }}
                                    </h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed mb-4">{{ $d['desc'] }}</p>
                                    <span class="inline-flex items-center gap-2 text-primary text-sm">Explore →</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Featured Accommodations -->
            <section id="stays" class="py-24 px-6 sm:px-8 bg-gray-50" aria-labelledby="stays-heading">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                        <div>
                            <span class="text-primary font-medium text-sm uppercase tracking-widest">Book Your Stay</span>
                            <h2 id="stays-heading" class="mt-2 mb-2 text-4xl font-medium text-foreground" style="font-family:'Poppins',sans-serif;">
                                Featured Accommodations
                            </h2>
                            <p class="text-muted-foreground text-base max-w-lg">
                                Handpicked stays that blend comfort with authentic Armenian hospitality.
                            </p>
                        </div>
                        <div class="flex gap-2 md:pb-1">
                            <button
                                type="button"
                                class="h-11 w-11 inline-flex items-center justify-center rounded-full border border-border bg-background text-foreground hover:bg-primary hover:text-primary-foreground hover:border-primary transition disabled:opacity-40 disabled:cursor-not-allowed"
                                aria-label="Previous accommodations"
                                data-stays-prev
                            >
                                ←
                            </button>
                            <button
                                type="button"
                                class="h-11 w-11 inline-flex items-center justify-center rounded-full border border-border bg-background text-foreground hover:bg-primary hover:text-primary-foreground hover:border-primary transition disabled:opacity-40 disabled:cursor-not-allowed"
                                aria-label="Next accommodations"
                                data-stays-next
                            >
                                →
                            </button>
                        </div>
                    </div>

                    @php
                        $featured = \App\Models\Rental::query()
                            ->active()
                            ->with([
                                'type:id,name,slug',
                                'location:id,name,slug',
                                'amenities:id,name,slug,icon',
                                'images',
                            ])
                            ->orderByDesc('is_featured')
                            ->orderByDesc('published_at')
                            ->limit(12)
                            ->get();

                        $demo = [
                            [
                                'title' => 'Mountain View Retreat',
                                'location' => 'Dilijan, Armenia',
                                'price' => 85,
                                'rating' => 4.9,
                                'reviews' => 128,
                                'image' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png',
                                'amenities' => ['wifi' => 'Wi‑Fi', 'kitchen' => 'Kitchen', 'mountain' => 'Mountain View'],
                            ],
                            [
                                'title' => 'Sevan Lakeside Villa',
                                'location' => 'Lake Sevan, Armenia',
                                'price' => 120,
                                'rating' => 4.8,
                                'reviews' => 94,
                                'image' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png',
                                'amenities' => ['wifi' => 'Wi‑Fi', 'kitchen' => 'Kitchen'],
                            ],
                            [
                                'title' => 'Yerevan Heritage Suite',
                                'location' => 'Yerevan, Armenia',
                                'price' => 95,
                                'rating' => 4.7,
                                'reviews' => 211,
                                'image' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png',
                                'amenities' => ['wifi' => 'Wi‑Fi', 'mountain' => 'Mountain View'],
                            ],
                            [
                                'title' => 'Forest Cabin Escape',
                                'location' => 'Dilijan Forest, Armenia',
                                'price' => 70,
                                'rating' => 4.9,
                                'reviews' => 67,
                                'image' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_4.png',
                                'amenities' => ['wifi' => 'Wi‑Fi', 'kitchen' => 'Kitchen', 'mountain' => 'Mountain View'],
                            ],
                            [
                                'title' => 'Ararat Valley Guesthouse',
                                'location' => 'Ararat Valley, Armenia',
                                'price' => 60,
                                'rating' => 4.6,
                                'reviews' => 45,
                                'image' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png',
                                'amenities' => ['wifi' => 'Wi‑Fi', 'mountain' => 'Mountain View'],
                            ],
                        ];
                    @endphp

                    <div class="overflow-hidden" data-stays-carousel>
                        <div class="flex gap-6 transition-transform duration-300 ease-in-out" data-stays-track>
                            @php
                                $items = $featured->isNotEmpty() ? $featured : collect($demo);
                            @endphp

                            @foreach($items as $item)
                                @php
                                    $isModel = $item instanceof \App\Models\Rental;
                                    $title = $isModel ? $item->title : $item['title'];
                                    $location = $isModel ? ($item->location?->name ? ($item->location->name.', Armenia') : 'Armenia') : $item['location'];
                                    $price = $isModel ? (float) $item->base_price : (float) $item['price'];
                                    $rating = $isModel ? (float) $item->rating_average : (float) $item['rating'];
                                    $reviews = $isModel ? (int) $item->ratings_count : (int) $item['reviews'];
                                    $img = $isModel
                                        ? ($item->images->sortBy([['is_primary', 'desc'], ['sort_order', 'asc']])->first()?->path ?? 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png')
                                        : $item['image'];

                                    $href = $isModel
                                        ? route('front.rentals.show', ['typeSlug' => $item->type->slug, 'locationSlug' => $item->location->slug, 'slug' => $item->slug])
                                        : route('front.rentals.index');

                                    $amenityMap = $isModel
                                        ? $item->amenities->take(3)->mapWithKeys(fn ($a) => [$a->slug => $a->name])->all()
                                        : $item['amenities'];

                                    $night = number_format($price, 0, '.', ' ');
                                @endphp

                                <a href="{{ $href }}" class="group shrink-0 w-full md:w-[calc(33.333%-16px)] rounded-xl overflow-hidden border border-border bg-card relative">
                                    <div class="relative h-56 overflow-hidden">
                                        <img src="{{ $img }}" alt="{{ $title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                        <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true"></div>
                                        <div class="absolute top-3 right-3 bg-background/90 rounded-full px-3 py-1 text-sm font-medium text-foreground">
                                            ${{ $night }}<span class="text-muted-foreground font-normal">/night</span>
                                        </div>
                                    </div>
                                    <div class="p-5 group-hover:-translate-y-1 transition-transform duration-300">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <h3 class="text-base font-medium text-foreground" style="font-family:'Poppins',sans-serif;">{{ $title }}</h3>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <span class="text-amber-500">★</span>
                                                <span class="text-sm text-foreground font-medium">{{ number_format($rating, 1) }}</span>
                                                <span class="text-xs text-muted-foreground">({{ $reviews }})</span>
                                            </div>
                                        </div>
                                        <p class="text-muted-foreground text-sm mb-3">{{ $location }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($amenityMap as $slug => $label)
                                                <span class="inline-flex items-center gap-1 text-xs text-secondary bg-secondary/10 px-2 py-1 rounded-full">
                                                    @if(in_array($slug, ['wifi','wi-fi','wifi_x'], true)) 📶 @endif
                                                    @if(in_array($slug, ['kitchen'], true)) 🍴 @endif
                                                    @if(in_array($slug, ['mountain','mountain-view','mountain_view'], true)) ⛰️ @endif
                                                    {{ $label }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <script>
(() => {
    const root = document.querySelector('[data-stays-carousel]');
    const track = document.querySelector('[data-stays-track]');
    const prev = document.querySelector('[data-stays-prev]');
    const next = document.querySelector('[data-stays-next]');
    if (!root || !track || !prev || !next) return;

    let index = 0;
    const visible = () => window.matchMedia('(min-width: 768px)').matches ? 3 : 1;

    const maxIndex = () => {
        const total = track.children.length;
        return Math.max(0, total - visible());
    };

    const cardStep = () => {
        const first = track.children[0];
        if (!first) return 0;
        const gap = 24; // gap-6
        return first.getBoundingClientRect().width + gap;
    };

    const render = () => {
        const step = cardStep();
        index = Math.min(Math.max(0, index), maxIndex());
        track.style.transform = `translateX(${-index * step}px)`;
        prev.disabled = index <= 0;
        next.disabled = index >= maxIndex();
    };

    prev.addEventListener('click', () => { index -= 1; render(); });
    next.addEventListener('click', () => { index += 1; render(); });
    window.addEventListener('resize', () => render());
    render();
})();
                    </script>
                </div>
            </section>

            <!-- Tourist Attractions -->
            <section id="attractions" class="py-24 px-6 sm:px-8 bg-background" aria-labelledby="attractions-heading">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <span class="text-primary font-medium text-sm uppercase tracking-widest">Must-See</span>
                        <h2 id="attractions-heading" class="mt-2 mb-4 text-4xl font-medium text-foreground" style="font-family:'Poppins',sans-serif;">
                            Tourist Attractions
                        </h2>
                        <p class="text-muted-foreground text-base max-w-xl mx-auto">
                            Explore Armenia's most iconic cultural and historical landmarks.
                        </p>
                    </div>

                    @php
                        $attractions = [
                            ['name' => 'Garni Temple', 'desc' => 'The only standing Greco-Roman colonnaded building in Armenia, dating back to the 1st century AD.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png', 'alt' => 'Garni Temple in Armenia'],
                            ['name' => 'Mount Ararat', 'desc' => 'The iconic snow-capped volcanic massif — the eternal symbol of Armenian identity and culture.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png', 'alt' => 'Yerevan city skyline with Mount Ararat'],
                            ['name' => 'Lake Sevan', 'desc' => "One of the world's largest high-altitude freshwater lakes, a natural wonder of the Caucasus.", 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png', 'alt' => 'Lake Sevan shoreline panorama'],
                            ['name' => 'Tatev Monastery', 'desc' => "A medieval monastery perched on a basalt plateau, accessible via the world's longest cable car.", 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png', 'alt' => 'Hikers on Armenian mountain trail'],
                        ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($attractions as $a)
                            <div class="group rounded-xl overflow-hidden border border-border bg-card">
                                <div class="relative h-52 overflow-hidden">
                                    <img src="{{ $a['img'] }}" alt="{{ $a['alt'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                    <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                        <p class="text-white text-xs leading-relaxed">{{ $a['desc'] }}</p>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-base font-medium text-foreground mb-1" style="font-family:'Poppins',sans-serif;">{{ $a['name'] }}</h3>
                                    <p class="text-muted-foreground text-sm line-clamp-2 mb-3">{{ $a['desc'] }}</p>
                                    <span class="inline-flex items-center gap-2 text-primary text-sm">View more →</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Experiences -->
            <section id="experiences" class="py-24 px-6 sm:px-8 bg-gray-50" aria-labelledby="experiences-heading">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <span class="text-primary font-medium text-sm uppercase tracking-widest">Activities</span>
                        <h2 id="experiences-heading" class="mt-2 mb-4 text-4xl font-medium text-foreground" style="font-family:'Poppins',sans-serif;">
                            Experiences & Activities
                        </h2>
                        <p class="text-muted-foreground text-base max-w-xl mx-auto">
                            From mountain hikes to wine tours — craft your perfect Armenian adventure.
                        </p>
                    </div>

                    @php
                        $experiences = [
                            ['icon' => '🥾', 'title' => 'Mountain Hiking', 'desc' => "Trek through Armenia's stunning mountain trails with expert local guides.", 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png', 'alt' => 'Hikers on Armenian mountain trail'],
                            ['icon' => '🍷', 'title' => 'Wine Tours', 'desc' => "Discover Armenia's ancient winemaking tradition in the Ararat Valley vineyards.", 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png', 'alt' => 'Yerevan city skyline with Mount Ararat'],
                            ['icon' => '☕', 'title' => 'Food Tours', 'desc' => 'Savor authentic Armenian cuisine — from lavash to khorovats — on guided food walks.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png', 'alt' => 'Cozy stone cottage in Armenian mountains'],
                            ['icon' => '📷', 'title' => 'Cultural Immersion', 'desc' => 'Immerse yourself in Armenian history, art, and traditions with local cultural experts.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png', 'alt' => 'Garni Temple in Armenia'],
                            ['icon' => '🚴', 'title' => 'Cycling Adventures', 'desc' => 'Explore scenic routes through valleys and villages on two wheels.', 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_4.png', 'alt' => 'Dilijan forest trail in Armenia'],
                            ['icon' => '🔥', 'title' => 'Camping & Stargazing', 'desc' => "Spend nights under Armenia's crystal-clear skies in remote mountain camps.", 'img' => 'https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png', 'alt' => 'Lake Sevan shoreline panorama'],
                        ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($experiences as $e)
                            <div class="group rounded-xl overflow-hidden border border-border bg-card">
                                <div class="relative h-44 overflow-hidden">
                                    <img src="{{ $e['img'] }}" alt="{{ $e['alt'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                    <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent" aria-hidden="true"></div>
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="text-primary text-2xl">{{ $e['icon'] }}</span>
                                        <h3 class="text-base font-medium text-foreground group-hover:text-primary transition-colors" style="font-family:'Poppins',sans-serif;">
                                            {{ $e['title'] }}
                                        </h3>
                                    </div>
                                    <p class="text-muted-foreground text-sm leading-relaxed">{{ $e['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Why Choose Armenia (design) -->
            <section id="about" class="py-24 px-8 bg-gray-50" aria-labelledby="why-heading">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <span class="text-primary font-medium text-sm uppercase tracking-widest">Why Armenia</span>
                        <h2 id="why-heading" class="mt-2 mb-4 text-5xl font-medium text-foreground" style="font-family:'Poppins',sans-serif;">
                            Why Choose Armenia?
                        </h2>
                        <p class="text-muted-foreground text-base max-w-xl mx-auto">
                            A destination unlike any other — where ancient history meets breathtaking nature.
                        </p>
                    </div>

                    @php
                        $reasons = [
                            [
                                'icon' => 'tree',
                                'title' => 'Pristine Nature',
                                'desc' => "From alpine lakes to ancient forests and volcanic peaks — Armenia's landscapes are truly awe-inspiring.",
                            ],
                            [
                                'icon' => 'buildings',
                                'title' => 'Rich Heritage',
                                'desc' => "One of the world's oldest civilizations with thousands of years of history, art, and architecture.",
                            ],
                            [
                                'icon' => 'hand',
                                'title' => 'Warm Hospitality',
                                'desc' => 'Armenians are renowned for their legendary hospitality — guests are treated like family.',
                            ],
                            [
                                'icon' => 'bowl',
                                'title' => 'Exquisite Cuisine',
                                'desc' => 'A culinary tradition spanning millennia — from fresh lavash to world-class wines and brandy.',
                            ],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($reasons as $r)
                            <div class="flex flex-col items-center text-center p-6 rounded-xl bg-background border border-border">
                                <span class="text-primary mb-4" aria-hidden="true">
                                    @if($r['icon'] === 'tree')
                                        <svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M24 42V28" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                            <path d="M24 28C16 28 10 22 10 14C10 8 14.5 4 20 4C23 4 25.5 5.2 27.2 7.2C28.4 6.5 29.8 6.1 31.3 6.1C36.6 6.1 41 10.5 41 15.8C41 22.1 35.8 28 29 28H24Z" stroke="currentColor" stroke-width="2.25" stroke-linejoin="round"/>
                                        </svg>
                                    @elseif($r['icon'] === 'buildings')
                                        <svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 42V12L24 6V42" stroke="currentColor" stroke-width="2.25" stroke-linejoin="round"/>
                                            <path d="M24 42V18H38V42" stroke="currentColor" stroke-width="2.25" stroke-linejoin="round"/>
                                            <path d="M15 16H19M15 22H19M15 28H19M29 22H33M29 28H33M29 34H33" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                        </svg>
                                    @elseif($r['icon'] === 'hand')
                                        <svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 26V14C16 12.3 17.3 11 19 11C20.7 11 22 12.3 22 14V26" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                            <path d="M22 24V12C22 10.3 23.3 9 25 9C26.7 9 28 10.3 28 12V24" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                            <path d="M28 24V14C28 12.3 29.3 11 31 11C32.7 11 34 12.3 34 14V28" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                            <path d="M34 28V20C34 18.3 35.3 17 37 17C38.7 17 40 18.3 40 20V30C40 36.6 34.6 42 28 42H24C18.5 42 14 37.5 14 32V26H16Z" stroke="currentColor" stroke-width="2.25" stroke-linejoin="round"/>
                                            <path d="M13 23C10 23 8 25 8 28C8 33 12 36 16 37" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                        </svg>
                                    @else
                                        <svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14 22C14 14 19.5 8 27 8C34.5 8 40 14 40 22C40 31 33 38 24 38C15 38 8 31 8 22H14Z" stroke="currentColor" stroke-width="2.25" stroke-linejoin="round"/>
                                            <path d="M16 22C16 18 19.2 14 24 14C28.8 14 32 18 32 22" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                            <path d="M16 38H32" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
                                        </svg>
                                    @endif
                                </span>
                                <h3 class="text-xl font-medium text-foreground mb-3" style="font-family:'Poppins',sans-serif;">{{ $r['title'] }}</h3>
                                <p class="text-muted-foreground text-sm leading-relaxed">{{ $r['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Newsletter (design) -->
            <section
                id="contact"
                class="py-24 px-6 sm:px-8"
                style="background: linear-gradient(135deg, hsl(145, 25%, 35%), hsl(150, 24%, 30%))"
                aria-labelledby="newsletter-heading"
            >
                <div class="max-w-2xl mx-auto text-center">
                    <div class="flex justify-center mb-4">
                        <span class="text-white/80 text-4xl">✉️</span>
                    </div>
                    <h2 id="newsletter-heading" class="text-4xl font-medium text-white mb-3" style="font-family:'Poppins',sans-serif;">
                        Stay Inspired!
                    </h2>
                    <p class="text-white/80 text-base mb-8">
                        Subscribe for travel tips, destination guides, and exclusive Armenia travel news.
                    </p>

                    <form
                        id="newsletter-form"
                        class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto"
                        aria-label="Newsletter signup form"
                        method="POST"
                        action="{{ route('front.subscribe') }}"
                        data-success-msg="{{ __('front.newsletter_subscribed') }}"
                        data-error-msg="{{ __('front.newsletter_submit_error') }}"
                    >
                        @csrf
                        <input type="hidden" name="source" value="home_newsletter" />
                        <div class="flex-1">
                            <label for="newsletter-email" class="sr-only">Email address</label>
                            <input
                                id="newsletter-email"
                                name="email"
                                type="email"
                                placeholder="Enter your email address"
                                required
                                autocomplete="email"
                                class="w-full px-4 py-3 rounded-lg bg-white/20 border border-white/30 text-white placeholder:text-white/60 text-sm outline-none focus:border-white/60 transition-colors"
                            />
                        </div>
                        <button
                            type="submit"
                            class="bg-primary text-primary-foreground hover:bg-primary-hover font-normal text-sm px-6 py-3 rounded-lg transition disabled:opacity-60"
                        >
                            Subscribe
                        </button>
                    </form>

                    <div
                        id="newsletter-feedback"
                        class="mt-4 min-h-[1.25rem] text-sm font-medium"
                        role="status"
                        aria-live="polite"
                    >
                        @if(session('subscribed'))
                            <p class="text-white/90">{{ __('front.newsletter_subscribed') }}</p>
                        @elseif($errors->has('email'))
                            <p class="text-red-200">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <p class="text-white/50 text-xs mt-4">
                        No spam, ever. Unsubscribe at any time.
                    </p>
                </div>
            </section>
        </main>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('newsletter-form');
            if (!form) return;
            var feedback = document.getElementById('newsletter-feedback');
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = form.querySelector('[type="submit"]');
                var emailInput = form.querySelector('[name="email"]');
                var token = csrfMeta ? csrfMeta.getAttribute('content') : '';
                var successMsg = form.getAttribute('data-success-msg') || '';
                var errorMsg = form.getAttribute('data-error-msg') || '';

                feedback.innerHTML = '';
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');

                var sourceInput = form.querySelector('[name="source"]');

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        email: emailInput.value,
                        source: sourceInput ? sourceInput.value : 'home_newsletter',
                    }),
                })
                    .then(function (res) {
                        return res.text().then(function (text) {
                            var data = {};
                            try {
                                data = text ? JSON.parse(text) : {};
                            } catch (err) {}
                            return { res: res, data: data };
                        });
                    })
                    .then(function (_ref) {
                        var res = _ref.res;
                        var data = _ref.data;
                        var p = document.createElement('p');
                        if (res.ok) {
                            p.className = 'text-white/90';
                            p.textContent = data.message || successMsg;
                            feedback.appendChild(p);
                            emailInput.value = '';
                        } else if (res.status === 422 && data.errors && data.errors.email && data.errors.email[0]) {
                            p.className = 'text-red-200';
                            p.textContent = data.errors.email[0];
                            feedback.appendChild(p);
                        } else {
                            p.className = 'text-red-200';
                            p.textContent = data.message || errorMsg;
                            feedback.appendChild(p);
                        }
                    })
                    .catch(function () {
                        var p = document.createElement('p');
                        p.className = 'text-red-200';
                        p.textContent = errorMsg;
                        feedback.appendChild(p);
                    })
                    .finally(function () {
                        btn.disabled = false;
                        btn.removeAttribute('aria-busy');
                    });
            });
        })();
    </script>
@endpush

