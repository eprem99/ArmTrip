export type BlogCategory = "Guides" | "Food" | "Culture" | "Nature";
export type BlogLocation = "Yerevan" | "Dilijan" | "Gyumri" | "Tatev" | "Garni" | "Sevan";
export type BlogDuration = "1 day" | "3 days" | "7 days";

export interface BlogTag {
  category?: BlogCategory;
  location?: BlogLocation;
  duration?: BlogDuration;
}

export interface Author {
  name: string;
  avatar: string;
  bio: string;
}

export interface BlogPost {
  slug: string;
  title: string;
  excerpt: string;
  content: BlogContentBlock[];
  featuredImage: string;
  category: BlogCategory;
  location: BlogLocation;
  duration: BlogDuration;
  publishDate: string;
  readingTime: number;
  author: Author;
  tags: string[];
  quickFacts: { label: string; value: string }[];
  tableOfContents: { id: string; title: string; level: 2 | 3 }[];
}

export type BlogContentBlock =
  | { type: "h2"; id: string; text: string }
  | { type: "h3"; id: string; text: string }
  | { type: "p"; text: string }
  | { type: "quote"; text: string; attribution?: string }
  | { type: "image"; src: string; alt: string; caption?: string }
  | { type: "list"; items: string[] };

const authors: Record<string, Author> = {
  anna: {
    name: "Anna Petrosyan",
    avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop&crop=face",
    bio: "Travel writer & photographer based in Yerevan. I&#39;ve been exploring Armenia&#39;s hidden corners for over 10 years and love sharing stories about its culture, food, and landscapes.",
  },
  tigran: {
    name: "Tigran Hakobyan",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face",
    bio: "Outdoor adventure guide and nature writer. Specialist in Armenia&#39;s hiking trails, gorges, and wilderness camping.",
  },
  marie: {
    name: "Marie Dubois",
    avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&h=80&fit=crop&crop=face",
    bio: "Food blogger and culinary traveler. On a mission to taste every Armenian dish — from Yerevan&#39;s modern bistros to remote village kitchens.",
  },
};

export const blogPosts: BlogPost[] = [
  {
    slug: "3-day-itinerary-in-yerevan",
    title: "The Perfect 3-Day Itinerary in Yerevan",
    excerpt: "From the pink-tufa streets of the old city to the rooftop bars of the Northern Avenue — here&#39;s how to spend 72 unforgettable hours in Armenia&#39;s vibrant capital.",
    featuredImage: "https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1200&h=700&fit=crop",
    category: "Guides",
    location: "Yerevan",
    duration: "3 days",
    publishDate: "2026-03-15",
    readingTime: 8,
    author: authors.anna,
    tags: ["Yerevan", "City Guide", "3 days", "Culture", "Food"],
    quickFacts: [
      { label: "Location", value: "Yerevan, Armenia" },
      { label: "Duration", value: "3 days / 2 nights" },
      { label: "Best season", value: "April – October" },
      { label: "Est. budget", value: "$80–$150/day" },
      { label: "Difficulty", value: "Easy" },
    ],
    tableOfContents: [
      { id: "day-1", title: "Day 1 – Old City & Cascade", level: 2 },
      { id: "day-2", title: "Day 2 – Museums & Markets", level: 2 },
      { id: "day-3", title: "Day 3 – Day Trips & Farewell Dinner", level: 2 },
      { id: "where-to-eat", title: "Where to Eat", level: 3 },
      { id: "getting-around", title: "Getting Around", level: 3 },
      { id: "tips", title: "Practical Tips", level: 2 },
    ],
    content: [
      { type: "p", text: "Yerevan is one of the world&#39;s oldest continuously inhabited cities. Its rose-pink tuff stone buildings glow at sunrise, its café culture rivals any European capital, and the backdrop of Mount Ararat makes every sunset postcard-perfect." },
      { type: "h2", id: "day-1", text: "Day 1 – Old City & Cascade" },
      { type: "p", text: "Start your morning with a strong Armenian coffee at one of the charming cafés near Republic Square. Walk the grand square itself — its musical fountains come alive in the evening — then head up to the iconic Cascade Complex, a giant stairway of fountains and flowers linking central Yerevan to the Kentron district." },
      { type: "image", src: "https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=800&h=450&fit=crop", alt: "Republic Square Yerevan at sunset", caption: "Republic Square glows in the late afternoon sun." },
      { type: "quote", text: "Yerevan is not just a city — it&#39;s an open-air museum where history and modernity coexist beautifully.", attribution: "Lonely Planet Armenia" },
      { type: "h2", id: "day-2", text: "Day 2 – Museums & Markets" },
      { type: "p", text: "Dedicate your second day to culture. The History Museum of Armenia on Republic Square is exceptional — carved khachkars, Bronze Age artifacts, and medieval manuscripts tell the story of a 3,000-year-old civilization. In the afternoon, lose yourself in the Vernissage flea market, where local artisans sell hand-painted ceramics, carpets, and vintage Soviet memorabilia." },
      { type: "h3", id: "where-to-eat", text: "Where to Eat" },
      { type: "list", items: ["Lavash Restaurant – traditional Armenian cuisine in a beautiful courtyard", "The Club – rooftop dining with views of Ararat", "Dargett Craft Beer & Grill – local brews and modern Armenian fare", "GaumardJo – Georgian-Armenian fusion in the Mashtots area"] },
      { type: "h2", id: "day-3", text: "Day 3 – Day Trips & Farewell Dinner" },
      { type: "p", text: "On your final day, consider a half-day trip to Garni Temple and Geghard Monastery — both just 30 km from Yerevan and easily reachable by shared taxi (marshrutka). Return in time for a farewell dinner and evening stroll along the Northern Avenue." },
      { type: "h3", id: "getting-around", text: "Getting Around" },
      { type: "p", text: "Yerevan is very walkable in the center. Metro costs 100 AMD per ride. Taxis via GG app are cheap — expect $1–2 for most city rides." },
      { type: "h2", id: "tips", text: "Practical Tips" },
      { type: "list", items: ["The AMD is the local currency — always carry some cash for markets", "Most restaurants accept cards in Yerevan&#39;s center", "Download the GG app for ride-sharing", "Republic Square fountains perform at 9 PM on weekends"] },
    ],
  },
  {
    slug: "hiking-dilijan-national-park",
    title: "Hiking Through Dilijan National Park",
    excerpt: "Lush forests, medieval monasteries, and clean mountain air — Dilijan is often called the 'Armenian Switzerland' for good reason. Here&#39;s your complete hiking guide.",
    featuredImage: "https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=1200&h=700&fit=crop",
    category: "Nature",
    location: "Dilijan",
    duration: "1 day",
    publishDate: "2026-03-10",
    readingTime: 6,
    author: authors.tigran,
    tags: ["Dilijan", "Hiking", "Nature", "1 day", "National Park"],
    quickFacts: [
      { label: "Location", value: "Dilijan, Tavush Province" },
      { label: "Duration", value: "Full day (8–10 hrs)" },
      { label: "Difficulty", value: "Moderate" },
      { label: "Best season", value: "May – October" },
      { label: "Entry", value: "Free" },
    ],
    tableOfContents: [
      { id: "trail-overview", title: "Trail Overview", level: 2 },
      { id: "monasteries", title: "Haghartsin & Goshavank", level: 2 },
      { id: "what-to-bring", title: "What to Bring", level: 3 },
      { id: "getting-there", title: "Getting There", level: 2 },
    ],
    content: [
      { type: "p", text: "Dilijan National Park covers over 28,000 hectares of dense oak, beech, and hornbeam forest in northeastern Armenia. It&#39;s one of the greenest places in the entire country — a dramatic contrast to Yerevan&#39;s arid surroundings." },
      { type: "h2", id: "trail-overview", text: "Trail Overview" },
      { type: "p", text: "The most popular day hike runs from the center of Dilijan town through the forest to Haghartsin Monastery, covering about 14 km round-trip with 400m elevation gain. The trail is well-marked and passes pristine streams and ancient forest." },
      { type: "image", src: "https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=800&h=450&fit=crop", alt: "Forest trail in Dilijan National Park", caption: "The trail weaves through ancient beech and oak forests." },
      { type: "h2", id: "monasteries", text: "Haghartsin & Goshavank" },
      { type: "p", text: "Haghartsin Monastery (12th–13th century) is the jewel at the end of the trail. Its carved stone walls rise organically from the forest, almost as if the trees grew around them. Nearby Goshavank is equally stunning and less visited." },
      { type: "quote", text: "The forest cathedral of Haghartsin outshines any Gothic nave I&#39;ve visited in Europe.", attribution: "Tigran Hakobyan" },
      { type: "h3", id: "what-to-bring", text: "What to Bring" },
      { type: "list", items: ["Sturdy hiking shoes (trail can be muddy)", "2+ liters of water", "Snacks — no cafés on the trail", "Rain jacket (weather changes quickly)", "Camera — you&#39;ll want it"] },
      { type: "h2", id: "getting-there", text: "Getting There" },
      { type: "p", text: "Dilijan is 2 hours from Yerevan by marshrutka (shared minibus). Buses depart from Kilikia Bus Station every 30–60 minutes. Alternatively, rent a car for more flexibility." },
    ],
  },
  {
    slug: "armenian-food-guide",
    title: "A Complete Guide to Armenian Cuisine",
    excerpt: "Dolma, khorovats, lavash, gata — Armenian food is as ancient as its civilization. This is your definitive guide to eating well across the country.",
    featuredImage: "https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200&h=700&fit=crop",
    category: "Food",
    location: "Yerevan",
    duration: "3 days",
    publishDate: "2026-03-05",
    readingTime: 10,
    author: authors.marie,
    tags: ["Food", "Culture", "Yerevan", "Cuisine", "Traditional"],
    quickFacts: [
      { label: "Must-try dishes", value: "Khorovats, Dolma, Lavash" },
      { label: "Avg. meal cost", value: "$5–$20 per person" },
      { label: "Best food market", value: "GUM Market, Yerevan" },
      { label: "Food culture", value: "Family-centered, seasonal" },
    ],
    tableOfContents: [
      { id: "essential-dishes", title: "Essential Dishes", level: 2 },
      { id: "street-food", title: "Street Food", level: 2 },
      { id: "drinks", title: "What to Drink", level: 3 },
      { id: "restaurants", title: "Best Restaurants", level: 2 },
    ],
    content: [
      { type: "p", text: "Armenian cuisine is one of the oldest in the world, shaped by geography, religion, and thousands of years of trade routes. It&#39;s a cuisine of herbs, grilled meats, stuffed vegetables, and bread — always bread." },
      { type: "h2", id: "essential-dishes", text: "Essential Dishes" },
      { type: "list", items: ["Khorovats – Armenian BBQ, the national obsession", "Dolma – grape leaves stuffed with spiced meat and rice", "Lavash – UNESCO-listed flatbread, baked in a tonir clay oven", "Gata – flaky pastry filled with sugar and butter", "Spas – cold yogurt soup with wheat berries and herbs"] },
      { type: "image", src: "https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=450&fit=crop", alt: "Traditional Armenian food spread", caption: "A classic Armenian feast — khorovats, lavash, and herb salads." },
      { type: "h2", id: "street-food", text: "Street Food" },
      { type: "p", text: "Yerevan&#39;s streets are lined with bakeries selling fresh boregs (cheese pastries), and vendors grilling corn cobs and churning out khoravats. GUM Market&#39;s basement is a labyrinth of dried fruits, spices, and local cheeses — absolutely essential." },
      { type: "h3", id: "drinks", text: "What to Drink" },
      { type: "p", text: "Armenia is one of the world&#39;s oldest wine-producing regions — Areni wine from Vayots Dzor is outstanding. And of course, Armenian brandy (cognac) — Churchill reportedly preferred it to French cognac." },
      { type: "h2", id: "restaurants", text: "Best Restaurants in Yerevan" },
      { type: "list", items: ["Lavash (Northern Avenue) – refined traditional Armenian", "Sherep – local seasonal ingredients, beautiful garden", "Dolmama – upscale Armenian in a charming house", "Old Yerevan Food Court – budget-friendly, all classics"] },
    ],
  },
  {
    slug: "tatev-monastery-guide",
    title: "Tatev Monastery & the Wings of Tatev",
    excerpt: "Perched on a basalt ridge above a deep gorge, Tatev is Armenia&#39;s most dramatic monastery. Ride the world&#39;s longest reversible cable car and step into medieval history.",
    featuredImage: "https://images.unsplash.com/photo-1548013146-72479768bada?w=1200&h=700&fit=crop",
    category: "Culture",
    location: "Tatev",
    duration: "1 day",
    publishDate: "2026-02-28",
    readingTime: 7,
    author: authors.anna,
    tags: ["Tatev", "Culture", "Architecture", "1 day", "Cable Car"],
    quickFacts: [
      { label: "Location", value: "Syunik Province, Southern Armenia" },
      { label: "Cable car", value: "Wings of Tatev – 5.7 km" },
      { label: "Duration", value: "Day trip from Goris" },
      { label: "Entry", value: "Cable car: ~3,500 AMD" },
      { label: "Best season", value: "April – November" },
    ],
    tableOfContents: [
      { id: "cable-car", title: "Wings of Tatev Cable Car", level: 2 },
      { id: "monastery", title: "Tatev Monastery", level: 2 },
      { id: "surroundings", title: "Exploring the Surroundings", level: 3 },
      { id: "practical", title: "Practical Information", level: 2 },
    ],
    content: [
      { type: "p", text: "Tatev Monastery stands as one of the most visually stunning religious complexes in the entire Caucasus. Built in the 9th century on a dramatic basalt promontory above the Vorotan Gorge, it&#39;s both a spiritual landmark and an architectural marvel." },
      { type: "h2", id: "cable-car", text: "Wings of Tatev Cable Car" },
      { type: "p", text: "The Wings of Tatev (Tatevi Tonanparer) is the world&#39;s longest non-stop double track cable car at 5.7 km. The 12-minute ride from Halidzor village offers jaw-dropping views of the Vorotan Gorge — one of the most spectacular experiences in Armenia." },
      { type: "image", src: "https://images.unsplash.com/photo-1548013146-72479768bada?w=800&h=450&fit=crop", alt: "Tatev Monastery on rocky cliff", caption: "Tatev Monastery clings to the edge of a 1,000-meter basalt drop." },
      { type: "quote", text: "Few places on earth combine engineering, nature, and spiritual history as perfectly as Tatev.", attribution: "Anna Petrosyan" },
      { type: "h2", id: "monastery", text: "Tatev Monastery" },
      { type: "p", text: "The monastery complex includes the Cathedral of St. Paul and Peter (895–906 AD), a perpetual-motion swing column (Gavazan), oil press, and refectory. Guides are available on-site and highly recommended." },
      { type: "h3", id: "surroundings", text: "Exploring the Surroundings" },
      { type: "list", items: ["Tatev Hermitage (a 30-min hike from the main complex)", "Devil&#39;s Bridge natural arch over the Vorotan River", "Khndzoresk Cave Village – 4 km away, fascinating abandoned cave settlement"] },
      { type: "h2", id: "practical", text: "Practical Information" },
      { type: "list", items: ["Nearest base: Goris town (30 km away)", "Cable car runs daily 10:00–18:00", "Combined monastery + cable car ticket available", "Book return cable car in advance on busy days"] },
    ],
  },
  {
    slug: "gyumri-street-art-architecture",
    title: "Gyumri: Street Art, Black Tuff & Soviet Soul",
    excerpt: "Armenia&#39;s second city is a living museum of resilience. Its dark volcanic stone buildings, vibrant street art scene, and quirky café culture make it a must-visit destination.",
    featuredImage: "https://images.unsplash.com/photo-1562979314-bee7453e911c?w=1200&h=700&fit=crop",
    category: "Culture",
    location: "Gyumri",
    duration: "1 day",
    publishDate: "2026-02-18",
    readingTime: 6,
    author: authors.anna,
    tags: ["Gyumri", "Architecture", "Culture", "Art", "1 day"],
    quickFacts: [
      { label: "Location", value: "Shirak Province, NW Armenia" },
      { label: "Distance from Yerevan", value: "126 km (1.5 hrs by train)" },
      { label: "Duration", value: "Day trip or overnight" },
      { label: "Best season", value: "May – September" },
    ],
    tableOfContents: [
      { id: "old-town", title: "Old Town & Black Tuff Buildings", level: 2 },
      { id: "street-art", title: "Street Art Scene", level: 2 },
      { id: "cafes", title: "Cafés & Cultural Spaces", level: 3 },
    ],
    content: [
      { type: "p", text: "Gyumri was once Armenia&#39;s most cosmopolitan city — a hub of merchants, artists, and intellectuals. Despite the devastating 1988 earthquake that killed over 25,000 people, the city has rebuilt itself while preserving its unique black tuff stone architecture." },
      { type: "h2", id: "old-town", text: "Old Town & Black Tuff Buildings" },
      { type: "p", text: "The Kumayri Historic District is the heart of old Gyumri. Here, 19th-century merchant houses built from dark volcanic tuff line narrow cobblestone streets. Many are now workshops, galleries, and boutique hotels." },
      { type: "h2", id: "street-art", text: "Street Art Scene" },
      { type: "p", text: "Gyumri has embraced street art as a form of urban renewal. Walking the backstreets you&#39;ll find ambitious murals — Soviet nostalgia meets modern Armenian identity — created during annual art festivals." },
      { type: "h3", id: "cafes", text: "Cafés & Cultural Spaces" },
      { type: "list", items: ["Amasia café – legendary local haunt, live folk music on weekends", "Craft Beer Gyumri – local brewery with outdoor terrace", "Dzitoghtsyan Museum of Social Life & National Architecture"] },
    ],
  },
  {
    slug: "lake-sevan-summer-guide",
    title: "Lake Sevan: The Jewel of the Armenian Highlands",
    excerpt: "At 1,900 meters above sea level, Lake Sevan is one of the largest high-altitude lakes in the world. Here&#39;s your complete summer guide to beaches, fish, and monasteries.",
    featuredImage: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&h=700&fit=crop",
    category: "Nature",
    location: "Sevan",
    duration: "3 days",
    publishDate: "2026-02-10",
    readingTime: 7,
    author: authors.tigran,
    tags: ["Sevan", "Lake", "Nature", "Summer", "3 days"],
    quickFacts: [
      { label: "Location", value: "Gegharkunik Province" },
      { label: "Altitude", value: "1,900 m above sea level" },
      { label: "Duration", value: "2–3 days" },
      { label: "Best for", value: "Swimming, hiking, seafood" },
      { label: "Best season", value: "June – September" },
    ],
    tableOfContents: [
      { id: "beaches", title: "Beaches & Swimming", level: 2 },
      { id: "sevanavank", title: "Sevanavank Monastery", level: 2 },
      { id: "food", title: "Sevan Fish & Local Food", level: 3 },
    ],
    content: [
      { type: "p", text: "Lake Sevan covers nearly 5% of Armenia&#39;s total area and its electric-blue waters at high altitude are truly breathtaking. The surrounding beaches fill up with vacationing Armenians every summer — and for good reason." },
      { type: "h2", id: "beaches", text: "Beaches & Swimming" },
      { type: "p", text: "The northern shore has the best sandy beaches. Tsakhkadzor-side beaches are more resort-oriented while the eastern shore is quieter and wilder. Water temperature peaks in August at around 22°C." },
      { type: "image", src: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=450&fit=crop", alt: "Lake Sevan shore with blue water", caption: "Lake Sevan&#39;s turquoise waters at 1,900 meters altitude." },
      { type: "h2", id: "sevanavank", text: "Sevanavank Monastery" },
      { type: "p", text: "Built in 874 AD on what was once an island (now a peninsula), Sevanavank offers panoramic views of the whole lake. The hike up takes about 15 minutes and is worth every step." },
      { type: "h3", id: "food", text: "Sevan Fish & Local Food" },
      { type: "p", text: "Sevan is famous for its ishkhan (Armenian trout) and sig — both freshwater fish found only here. Every restaurant on the lakefront serves them grilled or baked with local herbs. Try the khorovats (BBQ) on the beach — vendors fire up grills every evening." },
    ],
  },
];

export const categories: BlogCategory[] = ["Guides", "Food", "Culture", "Nature"];
export const locations: BlogLocation[] = ["Yerevan", "Dilijan", "Gyumri", "Tatev", "Garni", "Sevan"];
export const durations: BlogDuration[] = ["1 day", "3 days", "7 days"];

export function getPostBySlug(slug: string): BlogPost | undefined {
  return blogPosts.find((p) => p.slug === slug);
}

export function getRelatedPosts(post: BlogPost, count = 3): BlogPost[] {
  return blogPosts
    .filter((p) => p.slug !== post.slug && (p.category === post.category || p.location === post.location))
    .slice(0, count);
}
