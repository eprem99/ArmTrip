@php
    $orgName = \App\Models\Option::get('organization_name', '') ?: config('app.name');
    $copyright = (string) \App\Models\Option::get('footer_copyright', '');
    $socialJson = \App\Models\Option::get('footer_social_links', '');
    $socialLinks = [];
    if (is_string($socialJson) && $socialJson !== '') {
        $decoded = json_decode($socialJson, true);
        $socialLinks = is_array($decoded) ? $decoded : [];
    } elseif (is_array($socialJson)) {
        $socialLinks = $socialJson;
    }
    $socialLinks = array_values(array_filter($socialLinks, fn ($r) => is_array($r) && !empty($r['url'])));
@endphp

<footer class="bg-gray-900 text-white" aria-label="Site footer">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-primary text-2xl">★</span>
                    <span class="text-lg font-semibold" style="font-family:'Poppins',sans-serif;">{{ $orgName }}</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Your gateway to discovering the Land of Mountains and Legends. Explore, stay, and experience Armenia like never before.
                </p>
                @if(!empty($socialLinks))
                    <div class="flex flex-wrap gap-4">
                        @foreach($socialLinks as $row)
                            @php
                                $label = (string) ($row['label'] ?? '');
                                $url = (string) ($row['url'] ?? '');
                                $safeLabel = $label !== '' ? $label : parse_url($url, PHP_URL_HOST);
                            @endphp
                            <a
                                href="{{ $url }}"
                                class="text-gray-400 hover:text-primary transition text-sm"
                                aria-label="{{ $safeLabel }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >{{ $safeLabel }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h3 class="text-sm font-medium uppercase tracking-widest mb-4" style="font-family:'Poppins',sans-serif;">Explore</h3>
                    <ul class="space-y-2">
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#destinations">Destinations</a></li>
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#stays">Stays & Rentals</a></li>
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#attractions">Attractions</a></li>
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#experiences">Experiences</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-medium uppercase tracking-widest mb-4" style="font-family:'Poppins',sans-serif;">Resources</h3>
                    <ul class="space-y-2">
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#guides">Travel Guides</a></li>
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#about">About Armenia</a></li>
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#guides">Travel Tips</a></li>
                        <li><a class="text-gray-400 hover:text-primary text-sm transition" href="#contact">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium uppercase tracking-widest mb-4" style="font-family:'Poppins',sans-serif;">Contact</h3>
                <ul class="space-y-3">
                    <li class="text-gray-400 text-sm">📍 Yerevan, Republic of Armenia</li>
                    <li><a class="text-gray-400 hover:text-primary text-sm transition" href="tel:+37410000000">📞 +374 10 000 000</a></li>
                    <li><a class="text-gray-400 hover:text-primary text-sm transition" href="mailto:hello@armenia.travel">✉️ hello@armenia.travel</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-gray-500 text-xs">
                &copy; {{ date('Y') }}
                {{ $copyright !== '' ? $copyright : ($orgName.'. All rights reserved.') }}
            </p>
            <div class="flex gap-4">
                <a href="#" class="text-gray-500 hover:text-gray-300 text-xs transition">Privacy Policy</a>
                <a href="#" class="text-gray-500 hover:text-gray-300 text-xs transition">Terms of Service</a>
                <a href="#" class="text-gray-500 hover:text-gray-300 text-xs transition">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
