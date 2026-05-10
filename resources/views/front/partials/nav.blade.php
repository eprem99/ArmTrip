@php
    $isHome = request()->routeIs('front.home');
    $home = route('front.home');
    $anchor = fn (string $id) => $isHome ? ('#'.$id) : ($home.'#'.$id);
    $safeRoute = function (string $name, array $params = []) {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    };

    $headerBase = 'fixed inset-x-0 top-0 z-50 transition-all duration-300';
    $headerMode = $isHome ? 'transparent' : 'solid';
    $headerClasses = $isHome
        ? ($headerBase.' bg-transparent')
        : ($headerBase.' bg-background border-b border-border backdrop-blur');

    $orgName = \App\Models\Option::get('organization_name', '') ?: config('app.name');
    $logoLight = (string) \App\Models\Option::get('organization_logo_light', '');
    $logoDark = (string) \App\Models\Option::get('organization_logo_dark', '');
@endphp

<header id="site-header" class="{{ $headerClasses }}" data-header-mode="{{ $headerMode }}">
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 sm:px-8" aria-label="{{ __('front.nav_main') }}">
        <a
            href="{{ $home }}"
            class="flex items-center gap-2 select-none"
            aria-label="{{ $orgName }}"
        >
            @if(filled($logoLight) || filled($logoDark))
                <span class="inline-flex h-10 items-center">
                    @if(filled($logoLight))
                        <img
                            src="{{ $logoLight }}"
                            alt="{{ $orgName }}"
                            class="{{ $isHome ? '' : 'hidden' }} h-10 w-auto object-contain"
                            loading="eager"
                            decoding="async"
                            data-logo-light
                        />
                    @endif
                    @if(filled($logoDark))
                        <img
                            src="{{ $logoDark }}"
                            alt="{{ $orgName }}"
                            class="{{ $isHome ? 'hidden' : '' }} h-10 w-auto object-contain"
                            loading="eager"
                            decoding="async"
                            data-logo-dark
                        />
                    @endif
                </span>
                <span class="sr-only">{{ $orgName }}</span>
            @else
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground font-semibold">
                    SA
                </span>
                <span class="hidden sm:block font-semibold tracking-tight {{ $isHome ? 'text-white' : 'text-foreground' }}" data-header-contrast>
                    {{ $orgName }}
                </span>
            @endif
        </a>

        <div class="hidden lg:flex items-center gap-1" aria-label="{{ __('front.nav_main') }}">
            <a href="{{ $home }}" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }}" data-header-contrast>
                {{ __('front.nav_home') }}
            </a>

            {{-- Rentals dropdown --}}
            <div class="relative" data-dropdown>
                <button type="button" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }} inline-flex items-center gap-1" data-dropdown-toggle data-header-contrast>
                    {{ __('front.nav_rentals') }}
                    <span class="text-xs opacity-80">▾</span>
                </button>
                <div class="hidden absolute left-0 top-full mt-2 w-[340px] rounded-2xl border border-border bg-background shadow-xl overflow-hidden" data-dropdown-menu>
                    <div class="p-4 border-b border-gray-100">
                        <div class="text-xs text-gray-500 font-medium mb-2">Quick filters</div>
                        <form method="get" action="{{ route('front.rentals.index') }}" class="grid grid-cols-2 gap-2">
                            <input name="where" class="col-span-2 px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Location (e.g. Yerevan)" />
                            <input name="price_min" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Min $" inputmode="numeric" />
                            <input name="price_max" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Max $" inputmode="numeric" />
                            <button class="col-span-2 inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm text-primary-foreground hover:bg-primary-hover transition">
                                Search rentals
                            </button>
                        </form>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-2">
                        <a class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'apartments']) }}">{{ __('front.rentals_apartments') }}</a>
                        <a class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'houses']) }}">{{ __('front.rentals_houses') }}</a>
                        <a class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'hotels']) }}">{{ __('front.rentals_hotels') }}</a>
                        <a class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'villas']) }}">{{ __('front.rentals_villas') }}</a>
                        <a class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'offices']) }}">{{ __('front.rentals_offices') }}</a>
                        <a class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.rentals.index') }}">{{ __('front.rentals_daily_monthly') }}</a>
                    </div>
                </div>
            </div>

            {{-- Destinations dropdown --}}
            <div class="relative" data-dropdown>
                <button type="button" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }} inline-flex items-center gap-1" data-dropdown-toggle data-header-contrast>
                    {{ __('front.nav_destinations') }}
                    <span class="text-xs opacity-80">▾</span>
                </button>
                <div class="hidden absolute left-0 top-full mt-2 w-64 rounded-2xl border border-border bg-background shadow-xl overflow-hidden" data-dropdown-menu>
                    <div class="p-2">
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ $anchor('destinations') }}">Armenia</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ $anchor('destinations') }}">Yerevan</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ $anchor('destinations') }}">Dilijan</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ $anchor('destinations') }}">Tsaghkadzor</a>
                    </div>
                </div>
            </div>

            {{-- Experiences dropdown (placeholder links for now) --}}
            <div class="relative" data-dropdown>
                <button type="button" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }} inline-flex items-center gap-1" data-dropdown-toggle data-header-contrast>
                    {{ __('front.nav_experiences') }}
                    <span class="text-xs opacity-80">▾</span>
                </button>
                <div class="hidden absolute left-0 top-full mt-2 w-72 rounded-2xl border border-border bg-background shadow-xl overflow-hidden" data-dropdown-menu>
                    <div class="p-2">
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="#">{{ __('front.exp_day_tours') }}</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="#">{{ __('front.exp_multi_day') }}</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="#">{{ __('front.exp_activities') }}</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="#">{{ __('front.exp_guides') }}</a>
                    </div>
                </div>
            </div>

            {{-- Blog dropdown --}}
            <div class="relative" data-dropdown>
                <button type="button" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }} inline-flex items-center gap-1" data-dropdown-toggle data-header-contrast>
                    {{ __('front.nav_blog') }}
                    <span class="text-xs opacity-80">▾</span>
                </button>
                <div class="hidden absolute left-0 top-full mt-2 w-72 rounded-2xl border border-border bg-background shadow-xl overflow-hidden" data-dropdown-menu>
                    <div class="p-2">
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.blog.index') }}">{{ __('front.blog_travel_guides') }}</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.blog.index', ['q' => 'itinerary']) }}">{{ __('front.blog_itineraries') }}</a>
                        <a class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-muted" href="{{ route('front.blog.index', ['q' => 'tips']) }}">{{ __('front.blog_tips') }}</a>
                    </div>
                </div>
            </div>

            <a href="{{ $safeRoute('front.page', ['slug' => 'about']) }}" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }}" data-header-contrast>
                {{ __('front.nav_about') }}
            </a>
            <a href="{{ $safeRoute('front.page', ['slug' => 'contact']) }}" class="nav-link-underline px-3 py-2 text-sm rounded-md {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }}" data-header-contrast>
                {{ __('front.nav_contact') }}
            </a>
        </div>

        <div class="hidden lg:flex items-center gap-3">
            {{-- CTA --}}
            <a
                href="{{ $safeRoute('admin.rentals') }}"
                class="inline-flex items-center rounded-xl bg-primary px-5 py-2 text-sm text-primary-foreground hover:bg-primary-hover transition"
            >
                {{ __('front.nav_add_listing') }}
            </a>

            {{-- User menu --}}
            <div class="relative" data-dropdown>
                <button
                    type="button"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }} transition-colors"
                    data-dropdown-toggle
                    data-header-contrast
                    aria-label="{{ __('front.nav_user_menu') }}"
                >
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/15">👤</span>
                    <span class="hidden xl:block">
                        @auth
                            {{ auth()->user()->name ?? __('front.dashboard') }}
                        @else
                            {{ __('front.nav_login') }}
                        @endauth
                    </span>
                    <span class="text-xs opacity-80">▾</span>
                </button>
                <div class="hidden absolute right-0 top-full mt-2 min-w-[220px] overflow-hidden rounded-2xl border border-border bg-background shadow-xl" data-dropdown-menu>
                    <div class="p-2">
                        @guest
                            <a class="block px-4 py-2 text-sm text-foreground hover:bg-muted rounded-xl" href="{{ route('login') }}">{{ __('front.nav_login') }}</a>
                            <a class="block px-4 py-2 text-sm text-foreground hover:bg-muted rounded-xl" href="{{ route('register') }}">{{ __('front.nav_register') }}</a>
                        @endguest

                        @auth
                            <a class="block px-4 py-2 text-sm text-foreground hover:bg-muted rounded-xl" href="{{ $safeRoute('dashboard') }}">
                                {{ __('front.nav_profile') }}
                            </a>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-muted rounded-xl">
                                    {{ __('front.nav_logout') }}
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="relative">
                <button
                    type="button"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm {{ $isHome ? 'text-white hover:text-white/80' : 'text-foreground hover:text-foreground/80' }} transition-colors"
                    data-lang-toggle
                    data-header-contrast
                    aria-label="Select language"
                >
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-white/15">🌐</span>
                    <span class="uppercase">{{ app()->getLocale() }}</span>
                    <span class="text-xs opacity-80">▾</span>
                </button>
                <div
                    class="hidden absolute right-0 top-full mt-2 min-w-[140px] overflow-hidden rounded-lg border border-border bg-background shadow-lg"
                    data-lang-menu
                >
                    <a class="block px-4 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('locale.switch', ['locale' => 'en']) }}">English</a>
                    <a class="block px-4 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('locale.switch', ['locale' => 'ru']) }}">Русский</a>
                    <a class="block px-4 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('locale.switch', ['locale' => 'am']) }}">Հայերեն</a>
                </div>
            </div>
        </div>

        <button
            type="button"
            class="lg:hidden inline-flex items-center justify-center rounded-lg p-2 {{ $isHome ? 'text-white hover:bg-white/10' : 'text-foreground hover:bg-muted' }} transition"
            data-mobile-toggle
            aria-label="Open menu"
        >
            ☰
        </button>
    </nav>

    <div class="hidden lg:hidden border-t border-white/10 bg-background/95" data-mobile-menu>
        <div class="mx-auto max-w-7xl px-6 py-4 space-y-2">
            <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $home }}">{{ __('front.nav_home') }}</a>

            <details class="group rounded-lg bg-white/60 border border-border">
                <summary class="cursor-pointer list-none px-3 py-2 text-sm text-foreground flex items-center justify-between">
                    <span>{{ __('front.nav_rentals') }}</span>
                    <span class="text-xs text-gray-400 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-3 pb-3 space-y-1">
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.rentals.index') }}">{{ __('front.nav_rentals') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'apartments']) }}">{{ __('front.rentals_apartments') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'houses']) }}">{{ __('front.rentals_houses') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'hotels']) }}">{{ __('front.rentals_hotels') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.rentals.index', ['type' => 'villas']) }}">{{ __('front.rentals_villas') }}</a>
                </div>
            </details>

            <details class="group rounded-lg bg-white/60 border border-border">
                <summary class="cursor-pointer list-none px-3 py-2 text-sm text-foreground flex items-center justify-between">
                    <span>{{ __('front.nav_destinations') }}</span>
                    <span class="text-xs text-gray-400 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-3 pb-3 space-y-1">
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $anchor('destinations') }}">Armenia</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $anchor('destinations') }}">Yerevan</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $anchor('destinations') }}">Dilijan</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $anchor('destinations') }}">Tsaghkadzor</a>
                </div>
            </details>

            <details class="group rounded-lg bg-white/60 border border-border">
                <summary class="cursor-pointer list-none px-3 py-2 text-sm text-foreground flex items-center justify-between">
                    <span>{{ __('front.nav_experiences') }}</span>
                    <span class="text-xs text-gray-400 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-3 pb-3 space-y-1">
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="#">{{ __('front.exp_day_tours') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="#">{{ __('front.exp_multi_day') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="#">{{ __('front.exp_activities') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="#">{{ __('front.exp_guides') }}</a>
                </div>
            </details>

            <details class="group rounded-lg bg-white/60 border border-border">
                <summary class="cursor-pointer list-none px-3 py-2 text-sm text-foreground flex items-center justify-between">
                    <span>{{ __('front.nav_blog') }}</span>
                    <span class="text-xs text-gray-400 group-open:rotate-180 transition">▾</span>
                </summary>
                <div class="px-3 pb-3 space-y-1">
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.blog.index') }}">{{ __('front.blog_travel_guides') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.blog.index', ['q' => 'itinerary']) }}">{{ __('front.blog_itineraries') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('front.blog.index', ['q' => 'tips']) }}">{{ __('front.blog_tips') }}</a>
                </div>
            </details>

            <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $safeRoute('front.page', ['slug' => 'about']) }}">{{ __('front.nav_about') }}</a>
            <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $safeRoute('front.page', ['slug' => 'contact']) }}">{{ __('front.nav_contact') }}</a>

            <a class="block rounded-lg px-3 py-2 text-sm text-primary-foreground bg-primary hover:bg-primary-hover transition" href="{{ $safeRoute('admin.rentals') }}">
                {{ __('front.nav_add_listing') }}
            </a>

            <div class="pt-3 border-t border-border space-y-2">
                @guest
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('login') }}">{{ __('front.nav_login') }}</a>
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ route('register') }}">{{ __('front.nav_register') }}</a>
                @endguest
                @auth
                    <a class="block rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" href="{{ $safeRoute('dashboard') }}">{{ __('front.nav_profile') }}</a>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left rounded-lg px-3 py-2 text-sm text-foreground hover:bg-muted" type="submit">{{ __('front.nav_logout') }}</button>
                    </form>
                @endauth
            </div>
            <div class="pt-3 border-t border-border flex gap-2">
                <a class="inline-flex items-center rounded-lg bg-muted px-3 py-2 text-xs text-foreground hover:bg-gray-200" href="{{ route('locale.switch', ['locale' => 'en']) }}">EN</a>
                <a class="inline-flex items-center rounded-lg bg-muted px-3 py-2 text-xs text-foreground hover:bg-gray-200" href="{{ route('locale.switch', ['locale' => 'ru']) }}">RU</a>
                <a class="inline-flex items-center rounded-lg bg-muted px-3 py-2 text-xs text-foreground hover:bg-gray-200" href="{{ route('locale.switch', ['locale' => 'am']) }}">AM</a>
            </div>
        </div>
    </div>
</header>

<script>
(() => {
    const header = document.getElementById('site-header');
    if (!header) return;

    const onScroll = () => {
        const mode = header.getAttribute('data-header-mode') || 'transparent';
        if (mode === 'solid') {
            header.classList.add('bg-background', 'border-b', 'border-border', 'backdrop-blur');
            header.querySelectorAll('[data-header-contrast]').forEach((el) => {
                el.classList.remove('text-white');
                el.classList.add('text-foreground');
            });

            // non-home pages: prefer dark logo
            const light = header.querySelector('[data-logo-light]');
            const dark = header.querySelector('[data-logo-dark]');
            if (light) light.classList.add('hidden');
            if (dark) dark.classList.remove('hidden');
            return;
        }
        const scrolled = window.scrollY > 60;
        header.classList.toggle('bg-background', scrolled);
        header.classList.toggle('border-b', scrolled);
        header.classList.toggle('border-border', scrolled);
        header.classList.toggle('backdrop-blur', scrolled);

        // contrast swap
        header.querySelectorAll('[data-header-contrast]').forEach((el) => {
            el.classList.toggle('text-white', !scrolled);
            el.classList.toggle('text-foreground', scrolled);
        });

        // home page: light logo over hero, dark logo when scrolled
        const light = header.querySelector('[data-logo-light]');
        const dark = header.querySelector('[data-logo-dark]');
        if (light && dark) {
            light.classList.toggle('hidden', scrolled);
            dark.classList.toggle('hidden', !scrolled);
        }
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    // mobile menu
    const mobileToggle = header.querySelector('[data-mobile-toggle]');
    const mobileMenu = header.querySelector('[data-mobile-menu]');
    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // language menu
    const langToggle = header.querySelector('[data-lang-toggle]');
    const langMenu = header.querySelector('[data-lang-menu]');
    const closeLang = () => {
        if (langMenu) langMenu.classList.add('hidden');
    };
    if (langToggle && langMenu) {
        langToggle.addEventListener('click', (e) => {
            e.preventDefault();
            langMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!header.contains(e.target)) return;
            if (langToggle.contains(e.target) || langMenu.contains(e.target)) return;
            closeLang();
        });
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLang();
        });
    }

    // generic dropdowns (rentals/destinations/experiences/blog/user)
    const dropdowns = Array.from(header.querySelectorAll('[data-dropdown]'));
    const closeAll = () => dropdowns.forEach((dd) => dd.querySelector('[data-dropdown-menu]')?.classList.add('hidden'));
    dropdowns.forEach((dd) => {
        const toggle = dd.querySelector('[data-dropdown-toggle]');
        const menu = dd.querySelector('[data-dropdown-menu]');
        if (!toggle || !menu) return;
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const isHidden = menu.classList.contains('hidden');
            closeAll();
            if (isHidden) menu.classList.remove('hidden');
        });
    });
    document.addEventListener('click', (e) => {
        if (!header.contains(e.target)) {
            // click outside header → close everything
            closeAll();
            closeLang();
            mobileMenu?.classList.add('hidden');
            return;
        }
        // click inside header but outside any dropdown → close dropdowns
        const insideAny = dropdowns.some((dd) => dd.contains(e.target));
        if (!insideAny) {
            closeAll();
        }
    });

    // click any link/button inside dropdown → close immediately
    dropdowns.forEach((dd) => {
        const menu = dd.querySelector('[data-dropdown-menu]');
        if (!menu) return;
        menu.addEventListener('click', (e) => {
            const t = e.target;
            if (!(t instanceof Element)) return;
            if (t.closest('a,button')) {
                closeAll();
                closeLang();
                mobileMenu?.classList.add('hidden');
            }
        });
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
    });
})();
</script>
