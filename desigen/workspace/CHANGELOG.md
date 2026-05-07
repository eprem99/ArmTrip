<instructions>
## 🚨 MANDATORY: CHANGELOG TRACKING 🚨

You MUST maintain this file to track your work across messages. This is NON-NEGOTIABLE.

---

## INSTRUCTIONS

- **MAX 5 lines** per entry - be concise but informative
- **Include file paths** of key files modified or discovered
- **Note patterns/conventions** found in the codebase
- **Sort entries by date** in DESCENDING order (most recent first)
- If this file gets corrupted, messy, or unsorted -> re-create it. 
- CRITICAL: Updating this file at the END of EVERY response is MANDATORY.
- CRITICAL: Keep this file under 300 lines. You are allowed to summarize, change the format, delete entries, etc., in order to keep it under the limit.

</instructions>

<changelog>
<!-- NEXT_ENTRY_HERE -->

## 2026-05-01
- Generated light + dark ARMTrip brand logos via image_generation
- Light logo URL: https://c.animaapp.com/mmoxd21v67mhk5/img/generated-image-1777656473304.png
- Dark logo URL: https://c.animaapp.com/mmoxd21v67mhk5/img/generated-image-1777656649054.png
- Replaced SVG emblem in `src/components/Navbar.tsx` with `<img>` tag switching between light/dark logos based on `scrolled` state

## 2026-04-12 (fix 2)
- Fixed second build crash: `SquaresFour` in `RentalsListingPage.tsx` also undefined in @phosphor-icons/react v2.1.10
- Replaced import + usage of `SquaresFour` → `GridFour` (correct stable name in v2.x)

## 2026-04-12 (fix)
- Fixed build crash in `RentalPropertyPage.tsx`: `BathtubSimple`, `Stairs`, `SquaresFour` resolved to `undefined` in @phosphor-icons/react v2.1.10
- Replaced with confirmed-stable v2 equivalents: `Bathtub`, `Ladder`, `SquareHalf`

## 2026-04-12
- Full redesign of `RentalsListingPage.tsx` — Airbnb-style premium catalog UI
- New Airbnb-style top search bar (Where/Check-in/Check-out/Guests/Type) with guest dropdown popover
- Sticky horizontal filter pill bar with type, instant-book toggle, sort dropdown, grid/list view toggle
- Sidebar filter panel upgraded: custom checkbox UI, animated toggle switch, amenity chip grid, rating pills
- `PropertyCard.tsx` redesigned: arrow prev/next nav, Top Rated/New/Popular badges, list mode layout, wish anim
- Added: SkeletonCard loader, mobile sticky bottom bar (Filters + Show Map), full-screen map placeholder, empty state redesign

## 2026-03-31
- Built full ArmTrip rentals: `/rentals` listing + `/rentals/:slug` single property page
- Created `src/data/rentalsData.ts` — 8 properties (apt/hotel/house/cottage/villa), 4 hosts, amenities meta, typed taxonomy
- Created `src/components/rentals/PropertyCard.tsx` — reusable card with image gallery dots, wishlist, instant-booking badge
- Created `src/pages/RentalsListingPage.tsx` — hero search bar (location/dates/guests), sticky type pill filters, sidebar (price range, type, location, amenities, rating), mobile drawer, sort, load-more pagination, active filter chips
- Created `src/pages/RentalPropertyPage.tsx` — gallery grid + thumbnails, sticky booking card (price calc, dates, guests), amenity groups, host box, reviews breakdown, map placeholder, mobile sticky bar, related properties
- Wired `/rentals` + `/rentals/:slug` into `src/App.tsx`; updated Navbar navLinks with Rentals route link

## 2026-03-31
- Built full ArmTrip blog: `/blog` listing page + `/blog/:slug` single post page
- Created `src/data/blogData.ts` — 6 real posts, author profiles, typed taxonomy (category/location/duration)
- Created `src/components/blog/BlogCard.tsx` — reusable card for listing & related posts
- Created `src/pages/BlogListingPage.tsx` — hero, sticky filter bar, 3-col grid, sidebar (popular/categories/newsletter), load-more pagination
- Created `src/pages/BlogPostPage.tsx` — hero, quickfacts, content blocks (h2/h3/p/quote/img/list), sticky TOC with scroll-spy, share, map placeholder, author box, comments, related posts
- Wired `BrowserRouter` + routes (`/`, `/blog`, `/blog/:slug`) in `src/App.tsx`
- Updated `src/components/Navbar.tsx` — Blog nav link (route type), `useNavigate`/`useLocation`, cross-page scroll navigation

## 2026-03-13
- Redesigned Navbar logo with mountain+sun SVG emblem and two-line "Stay / ARMENIA" brand text
- Fixed HeroSection search bar button alignment (items-end + self-stretch) to prevent popping
- Files: src/components/Navbar.tsx, src/components/HeroSection.tsx
</changelog>
