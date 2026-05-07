<instructions>
This file will be automatically added to your context. 
It serves multiple purposes:
  1. Storing frequently used tools so you can use them without searching each time
  2. Recording the user's code style preferences (naming conventions, preferred libraries, etc.)
  3. Maintaining useful information about the codebase structure and organization
  4. Remembering tricky quirks from this codebase

When you spend time searching for certain configuration files, tricky code coupled dependencies, or other codebase information, add that to this CODER.md file so you can remember it for next time.
Keep entries sorted in DESC order (newest first) so recent knowledge stays in prompt context if the file is truncated.
</instructions>

<coder>
# ArmTrip Codebase — Coder Notes

## Routes
- `/` → HomePage (src/App.tsx, inline)
- `/blog` → BlogListingPage (src/pages/BlogListingPage.tsx)
- `/blog/:slug` → BlogPostPage (src/pages/BlogPostPage.tsx)
- `/rentals` → RentalsListingPage (src/pages/RentalsListingPage.tsx)
- `/rentals/:slug` → RentalPropertyPage (src/pages/RentalPropertyPage.tsx)

## Data files
- `src/data/blogData.ts` — BlogPost type, 6 posts, authors, helper fns (getPostBySlug, getRelatedPosts)
- `src/data/rentalsData.ts` — RentalProperty type, 8 properties, amenitiesMeta, propertyTypes, helpers

## Component structure
- `src/components/Navbar.tsx` — unified navbar; navLinks array drives all nav; type:"route" = useNavigate, type:"scroll" = scrollIntoView with cross-page fallback
- `src/components/blog/BlogCard.tsx` — blog post card
- `src/components/rentals/PropertyCard.tsx` — rental property card (image gallery dots, wishlist, instant badge)

## Styling
- Tailwind v3 + tailwind.config.js with custom design tokens (primary=hsl(18,72%,55%), secondary=hsl(145,25%,35%))
- Fonts: DM Sans (body), Poppins (heading), Fira Code (mono) from Google Fonts in index.css
- Custom classes in index.css: .nav-link-underline, .img-zoom, .card-hover-image

## Key patterns
- All pages import Navbar + Footer
- Filtering with useMemo(); pagination with slice(0, page * N)
- Phosphor icons (@phosphor-icons/react) used throughout — prefer weight="regular" or "bold"
- No react-three/fiber or any native-module packages — browser-only stack
</coder>
