export type PropertyType = "apartment" | "hotel" | "house" | "cottage" | "villa";
export type RentalLocation = "Yerevan" | "Dilijan" | "Gyumri" | "Sevan" | "Garni" | "Tsaghkadzor" | "Goris" | "Tatev";
export type SortOption = "price-asc" | "price-desc" | "rating" | "popularity";

export interface Amenity {
  key: string;
  label: string;
  icon: string; // phosphor icon name
}

export interface PropertyHost {
  name: string;
  avatar: string;
  joined: string;
  responseRate: number;
  bio: string;
}

export interface PropertyReview {
  author: string;
  avatar: string;
  date: string;
  rating: number;
  text: string;
}

export interface RentalProperty {
  slug: string;
  type: PropertyType;
  title: string;
  shortDescription: string;
  description: string;
  location: {
    country: string;
    region: string;
    city: RentalLocation;
    district: string;
    address: string;
    lat: number;
    lng: number;
  };
  images: { src: string; alt: string }[];
  pricePerNight: number;
  currency: string;
  rating: number;
  reviewsCount: number;
  instantBooking: boolean;
  rooms: number;
  beds: number;
  bathrooms: number;
  maxGuests: number;
  areaSqm: number;
  floor?: number;
  totalFloors?: number;
  amenities: string[];
  houseRules: string[];
  cancellationPolicy: string;
  host: PropertyHost;
  reviews: PropertyReview[];
  tags: string[];
  popularity: number; // 0-100 score
}

export const amenitiesMeta: Record<string, { label: string; emoji: string }> = {
  wifi: { label: "Free WiFi", emoji: "📶" },
  parking: { label: "Free Parking", emoji: "🅿️" },
  pool: { label: "Swimming Pool", emoji: "🏊" },
  ac: { label: "Air Conditioning", emoji: "❄️" },
  kitchen: { label: "Full Kitchen", emoji: "🍳" },
  washing: { label: "Washing Machine", emoji: "🫧" },
  tv: { label: "Smart TV", emoji: "📺" },
  heating: { label: "Central Heating", emoji: "🔥" },
  balcony: { label: "Balcony / Terrace", emoji: "🌿" },
  gym: { label: "Gym / Fitness", emoji: "💪" },
  breakfast: { label: "Breakfast Included", emoji: "☕" },
  petFriendly: { label: "Pet Friendly", emoji: "🐾" },
  bbq: { label: "BBQ Area", emoji: "🥩" },
  fireplace: { label: "Fireplace", emoji: "🪵" },
  garden: { label: "Garden", emoji: "🌳" },
  hotTub: { label: "Hot Tub", emoji: "🛁" },
};

const hosts: Record<string, PropertyHost> = {
  ani: {
    name: "Ani Sargsyan",
    avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=80&h=80&fit=crop&crop=face",
    joined: "2019",
    responseRate: 98,
    bio: "Yerevan local and passionate host. I love helping travelers discover the hidden gems of my city. My apartments are fully equipped and centrally located.",
  },
  david: {
    name: "David Mkrtchyan",
    avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face",
    joined: "2018",
    responseRate: 95,
    bio: "Born in Dilijan, I manage a handful of forest retreats across the Tavush region. Nature, silence, and comfort — that&#39;s what I offer.",
  },
  lara: {
    name: "Lara Hovhannisyan",
    avatar: "https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=80&h=80&fit=crop&crop=face",
    joined: "2021",
    responseRate: 100,
    bio: "Boutique villa owner in Tsaghkadzor. My properties are designed for families seeking mountain comfort with modern amenities.",
  },
  armen: {
    name: "Armen Petrosyan",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face",
    joined: "2020",
    responseRate: 92,
    bio: "Gyumri architecture enthusiast and hotel owner. I restored a 19th-century merchant house into a boutique guesthouse in the Kumayri district.",
  },
};

export const rentalProperties: RentalProperty[] = [
  {
    slug: "modern-apt-yerevan-center",
    type: "apartment",
    title: "Modern Studio in the Heart of Yerevan",
    shortDescription: "Stylish studio steps from Republic Square with mountain views and fast WiFi.",
    description: "This beautifully designed studio sits on the 8th floor of a modern building just two blocks from Republic Square. Wake up to panoramic views of Mount Ararat through floor-to-ceiling windows. The space features a fully equipped open kitchen, a king-sized bed, and high-speed fiber internet — perfect for both leisure and remote work. The building has a lift, secure parking, and a 24/7 lobby. Cafés, restaurants, and the metro are all within a 3-minute walk.",
    location: { country: "Armenia", region: "Yerevan", city: "Yerevan", district: "Kentron", address: "Abovyan St., Kentron, Yerevan", lat: 40.1872, lng: 44.5152 },
    images: [
      { src: "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=900&h=600&fit=crop", alt: "Modern studio living area" },
      { src: "https://images.unsplash.com/photo-1540518614846-7eded433c457?w=900&h=600&fit=crop", alt: "Bedroom with views" },
      { src: "https://images.unsplash.com/photo-1484154218962-a197022b5858?w=900&h=600&fit=crop", alt: "Open plan kitchen" },
      { src: "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=900&h=600&fit=crop", alt: "Bathroom" },
    ],
    pricePerNight: 55,
    currency: "USD",
    rating: 4.9,
    reviewsCount: 87,
    instantBooking: true,
    rooms: 1,
    beds: 1,
    bathrooms: 1,
    maxGuests: 2,
    areaSqm: 45,
    floor: 8,
    totalFloors: 14,
    amenities: ["wifi", "parking", "ac", "kitchen", "washing", "tv", "balcony", "heating"],
    houseRules: ["No smoking inside", "No parties or events", "Pets not allowed", "Quiet hours 22:00 – 08:00", "Check-in from 14:00, check-out by 12:00"],
    cancellationPolicy: "Free cancellation up to 48 hours before check-in. After that, the first night is non-refundable.",
    host: hosts.ani,
    popularity: 95,
    tags: ["Yerevan", "Apartment", "City Center", "Ararat Views"],
    reviews: [
      { author: "Sophie D.", avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face", date: "March 2026", rating: 5, text: "Absolutely perfect location and stunning views of Ararat. Ani was incredibly responsive. Will definitely return!" },
      { author: "Marco F.", avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face", date: "February 2026", rating: 5, text: "Clean, modern, and everything worked perfectly. The WiFi was blazing fast — great for remote work." },
      { author: "Irina V.", avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=40&h=40&fit=crop&crop=face", date: "January 2026", rating: 4, text: "Great place, very central. The only thing I would improve is a slightly larger bathroom, but everything else was perfect." },
    ],
  },
  {
    slug: "forest-cottage-dilijan",
    type: "cottage",
    title: "Cozy Forest Cottage in Dilijan",
    shortDescription: "Tranquil timber cottage surrounded by oak and beech forest — perfect for a nature escape.",
    description: "Nestled among the ancient beech forests of Dilijan National Park, this hand-crafted wooden cottage is the ideal retreat for nature lovers. The two-bedroom space features vaulted ceilings, exposed wooden beams, a stone fireplace, and a private sun deck overlooking the forest. Mornings here are filled with birdsong and mist; evenings are magical by the fire with a glass of Armenian wine. A full kitchen lets you cook your own meals, or the village market is just 10 minutes away.",
    location: { country: "Armenia", region: "Tavush", city: "Dilijan", district: "Mets Mank", address: "Forest Road 4, Dilijan", lat: 40.7422, lng: 44.8634 },
    images: [
      { src: "https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=900&h=600&fit=crop", alt: "Forest cottage exterior" },
      { src: "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=900&h=600&fit=crop", alt: "Cozy living room with fireplace" },
      { src: "https://images.unsplash.com/photo-1521782462922-9579b3a6c432?w=900&h=600&fit=crop", alt: "Bedroom with wooden walls" },
      { src: "https://images.unsplash.com/photo-1416331108676-a22ccb276e35?w=900&h=600&fit=crop", alt: "Forest deck view" },
    ],
    pricePerNight: 85,
    currency: "USD",
    rating: 4.8,
    reviewsCount: 52,
    instantBooking: false,
    rooms: 2,
    beds: 2,
    bathrooms: 1,
    maxGuests: 4,
    areaSqm: 70,
    amenities: ["wifi", "kitchen", "fireplace", "balcony", "heating", "bbq", "petFriendly", "garden"],
    houseRules: ["No smoking indoors", "Pets welcome with prior notice", "Quiet hours 22:00 – 07:00", "Check-in 15:00, check-out 11:00", "Please leave the fireplace area clean"],
    cancellationPolicy: "Free cancellation up to 5 days before check-in. After that, 50% of the total is non-refundable.",
    host: hosts.david,
    popularity: 88,
    tags: ["Dilijan", "Cottage", "Forest", "Nature", "Fireplace"],
    reviews: [
      { author: "James K.", avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face", date: "March 2026", rating: 5, text: "The most peaceful place I&#39;ve stayed in Armenia. Woke up to deer in the forest. David was an amazing host!" },
      { author: "Elena B.", avatar: "https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=40&h=40&fit=crop&crop=face", date: "February 2026", rating: 5, text: "Perfect family retreat. The kids loved exploring the forest and we loved the fireplace evenings." },
    ],
  },
  {
    slug: "luxury-villa-tsaghkadzor",
    type: "villa",
    title: "Luxury Mountain Villa in Tsaghkadzor",
    shortDescription: "Stunning 4-bedroom villa with private pool, panoramic ski resort views, and premium amenities.",
    description: "This architectural masterpiece sits at 1,800 meters above sea level in Armenia&#39;s premier mountain resort town of Tsaghkadzor. The four-bedroom villa was designed by a local architect to frame the ski slopes through dramatic glass walls. Inside: heated marble floors, a private heated pool, a wine cellar stocked with Armenian labels, a state-of-the-art kitchen, and a home cinema. A full-time housekeeper and private chef can be arranged on request.",
    location: { country: "Armenia", region: "Kotayk", city: "Tsaghkadzor", district: "Upper Resort", address: "Akhtan St. 12, Tsaghkadzor", lat: 40.5412, lng: 44.7265 },
    images: [
      { src: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=900&h=600&fit=crop", alt: "Villa exterior with mountain view" },
      { src: "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=900&h=600&fit=crop", alt: "Villa living room" },
      { src: "https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&h=600&fit=crop", alt: "Private pool" },
      { src: "https://images.unsplash.com/photo-1560448204-603b3fc33ddc?w=900&h=600&fit=crop", alt: "Master bedroom" },
      { src: "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=900&h=600&fit=crop", alt: "Modern bathroom" },
    ],
    pricePerNight: 320,
    currency: "USD",
    rating: 5.0,
    reviewsCount: 24,
    instantBooking: false,
    rooms: 4,
    beds: 5,
    bathrooms: 3,
    maxGuests: 10,
    areaSqm: 280,
    amenities: ["wifi", "parking", "pool", "ac", "kitchen", "washing", "tv", "heating", "gym", "hotTub", "fireplace", "garden", "bbq"],
    houseRules: ["No smoking indoors", "No parties without prior approval", "Pets not allowed", "A security deposit of $500 is required", "Check-in 16:00, check-out 12:00"],
    cancellationPolicy: "Free cancellation up to 14 days before check-in. Within 14 days: 50% of total. Within 48 hours: non-refundable.",
    host: hosts.lara,
    popularity: 99,
    tags: ["Tsaghkadzor", "Villa", "Luxury", "Pool", "Ski Resort", "Mountain Views"],
    reviews: [
      { author: "Richard H.", avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face", date: "February 2026", rating: 5, text: "Words don&#39;t do this place justice. The pool at sunset with Ararat in the distance was absolutely unforgettable." },
      { author: "Natalia P.", avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&crop=face", date: "January 2026", rating: 5, text: "We came for a family ski holiday and this villa exceeded every expectation. Lara arranged a private chef for us — brilliant." },
    ],
  },
  {
    slug: "boutique-hotel-gyumri",
    type: "hotel",
    title: "Boutique Hotel in Kumayri Historic District",
    shortDescription: "Restored 19th-century black tuff mansion with 8 atmospheric rooms in Gyumri&#39;s old town.",
    description: "The Black Stone Hotel occupies a lovingly restored 1880s merchant&#39;s mansion built from Gyumri&#39;s iconic black volcanic tuff. Each of the 8 rooms is uniquely decorated with antique Armenian crafts, hand-woven textiles, and original stone arches. A courtyard breakfast is included daily. The hotel is steps from the Kumayri pedestrian zone, art galleries, and Gyumri&#39;s famous craft beer scene. An evening reception with local wine and cheese is hosted every Friday.",
    location: { country: "Armenia", region: "Shirak", city: "Gyumri", district: "Kumayri", address: "Abovyan Lane 7, Kumayri, Gyumri", lat: 40.7942, lng: 43.8481 },
    images: [
      { src: "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&h=600&fit=crop", alt: "Hotel exterior stone facade" },
      { src: "https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=900&h=600&fit=crop", alt: "Hotel room with stone arch" },
      { src: "https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=900&h=600&fit=crop", alt: "Courtyard breakfast" },
      { src: "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=900&h=600&fit=crop", alt: "Lounge area" },
    ],
    pricePerNight: 70,
    currency: "USD",
    rating: 4.7,
    reviewsCount: 113,
    instantBooking: true,
    rooms: 1,
    beds: 1,
    bathrooms: 1,
    maxGuests: 2,
    areaSqm: 30,
    amenities: ["wifi", "breakfast", "heating", "tv"],
    houseRules: ["No smoking anywhere on premises", "Pets not allowed", "Check-in 15:00–22:00", "Check-out by 11:00"],
    cancellationPolicy: "Free cancellation up to 72 hours before check-in.",
    host: hosts.armen,
    popularity: 82,
    tags: ["Gyumri", "Hotel", "Boutique", "Historic", "Breakfast Included"],
    reviews: [
      { author: "Chloe M.", avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face", date: "March 2026", rating: 5, text: "One of the most unique places I&#39;ve ever slept. The stone walls, the courtyard, the breakfast — absolute perfection." },
      { author: "Thomas B.", avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face", date: "February 2026", rating: 4, text: "Brilliant character hotel. Rooms are small but beautifully decorated. Armen is a wonderful host and great storyteller." },
    ],
  },
  {
    slug: "lakeside-house-sevan",
    type: "house",
    title: "Lakeside House on Lake Sevan",
    shortDescription: "Spacious 3-bedroom house with direct lake access, private garden, and BBQ terrace.",
    description: "This charming whitewashed house sits literally on the northern shore of Lake Sevan with private stairs leading to the water. The three bedrooms sleep up to 6 guests comfortably. The large terrace and garden are perfect for summer BBQs with the turquoise lake as your backdrop. Bring kayaks — there&#39;s a storage shed and easy water access. The Sevanavank Monastery is just a 10-minute drive, and the town of Sevan has shops and restaurants.",
    location: { country: "Armenia", region: "Gegharkunik", city: "Sevan", district: "North Shore", address: "North Shore Road 18, Sevan", lat: 40.5631, lng: 44.9488 },
    images: [
      { src: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=900&h=600&fit=crop", alt: "House at Lake Sevan" },
      { src: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&h=600&fit=crop", alt: "Lake view from terrace" },
      { src: "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=900&h=600&fit=crop", alt: "Bright living room" },
      { src: "https://images.unsplash.com/photo-1595599899162-2f98eb5d8c44?w=900&h=600&fit=crop", alt: "Bedroom interior" },
    ],
    pricePerNight: 120,
    currency: "USD",
    rating: 4.6,
    reviewsCount: 38,
    instantBooking: true,
    rooms: 3,
    beds: 3,
    bathrooms: 2,
    maxGuests: 6,
    areaSqm: 130,
    amenities: ["wifi", "parking", "kitchen", "washing", "bbq", "garden", "heating", "petFriendly"],
    houseRules: ["No smoking inside", "Pets welcome", "Quiet hours 23:00 – 08:00", "Check-in 14:00, check-out 11:00", "No motorized watercraft from the property"],
    cancellationPolicy: "Free cancellation up to 7 days before check-in. After that, the first night is non-refundable.",
    host: hosts.david,
    popularity: 79,
    tags: ["Sevan", "House", "Lakeside", "Family", "BBQ"],
    reviews: [
      { author: "Alicia R.", avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&crop=face", date: "August 2025", rating: 5, text: "The best summer rental I&#39;ve found in Armenia. Woke up every morning to the lake view and it never got old." },
      { author: "Pavel N.", avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face", date: "July 2025", rating: 4, text: "Great for a group trip. Plenty of space, a great BBQ setup, and the lake is right there. Very clean." },
    ],
  },
  {
    slug: "historic-apt-yerevan-old-town",
    type: "apartment",
    title: "Heritage Apartment in Old Yerevan",
    shortDescription: "Charming 2-bed flat in a restored Soviet-era building with exposed stone walls and vintage decor.",
    description: "Located in the quiet streets of old Yerevan, this beautifully restored apartment blends Soviet-era charm with modern comforts. Original mosaic floors, exposed tufa stone walls, and high ceilings create an authentic atmosphere. Two bedrooms, a separate living room, and a dining table for four. The building has a central courtyard typical of Yerevan&#39;s old residential style. A 15-minute walk to Republic Square; 5 minutes to the Vernissage market.",
    location: { country: "Armenia", region: "Yerevan", city: "Yerevan", district: "Mashtots", address: "Mashtots Ave., Yerevan", lat: 40.1820, lng: 44.5090 },
    images: [
      { src: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=900&h=600&fit=crop", alt: "Heritage apartment interior" },
      { src: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=900&h=600&fit=crop", alt: "Living room with original flooring" },
      { src: "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=900&h=600&fit=crop", alt: "Bedroom" },
      { src: "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=900&h=600&fit=crop", alt: "Bathroom" },
    ],
    pricePerNight: 65,
    currency: "USD",
    rating: 4.5,
    reviewsCount: 61,
    instantBooking: true,
    rooms: 2,
    beds: 2,
    bathrooms: 1,
    maxGuests: 4,
    areaSqm: 75,
    floor: 3,
    totalFloors: 5,
    amenities: ["wifi", "kitchen", "washing", "heating", "tv", "ac"],
    houseRules: ["No smoking", "No parties", "Pets not allowed", "Check-in from 13:00", "Check-out by 12:00"],
    cancellationPolicy: "Free cancellation up to 48 hours before check-in.",
    host: hosts.ani,
    popularity: 74,
    tags: ["Yerevan", "Apartment", "Heritage", "2 bedrooms", "Vernissage"],
    reviews: [
      { author: "Oliver S.", avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face", date: "March 2026", rating: 5, text: "This apartment has so much soul. The mosaic floors and stone walls are stunning. Central and quiet. Loved it." },
    ],
  },
  {
    slug: "eco-retreat-garni",
    type: "house",
    title: "Eco Retreat near Garni Gorge",
    shortDescription: "Off-grid eco house with stunning canyon views, solar power, and organic garden — 30 km from Yerevan.",
    description: "Perched on the rim of the dramatic Garni Gorge — whose basalt columns look like a giant natural pipe organ — this eco retreat is powered entirely by solar panels with rainwater harvesting. The stone and timber house has two bedrooms, a spacious veranda overlooking the canyon, and an organic permaculture garden. The Garni Temple and Geghard Monastery are each within 10 minutes by car. Stargazing here is unbeatable — far from city light pollution.",
    location: { country: "Armenia", region: "Kotayk", city: "Garni", district: "Garni Village", address: "Gorge Road 2, Garni", lat: 40.1128, lng: 44.7293 },
    images: [
      { src: "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&h=600&fit=crop", alt: "Eco house with canyon view" },
      { src: "https://images.unsplash.com/photo-1513694203232-719a280e022f?w=900&h=600&fit=crop", alt: "Stone interior" },
      { src: "https://images.unsplash.com/photo-1416331108676-a22ccb276e35?w=900&h=600&fit=crop", alt: "Garden terrace" },
      { src: "https://images.unsplash.com/photo-1519974719765-e6559eac2575?w=900&h=600&fit=crop", alt: "Canyon view from veranda" },
    ],
    pricePerNight: 95,
    currency: "USD",
    rating: 4.8,
    reviewsCount: 29,
    instantBooking: false,
    rooms: 2,
    beds: 2,
    bathrooms: 1,
    maxGuests: 4,
    areaSqm: 85,
    amenities: ["kitchen", "garden", "bbq", "petFriendly", "heating", "balcony"],
    houseRules: ["No smoking", "Eco-friendly products only (provided)", "Quiet hours after 22:00", "Pets welcome with care", "Check-in 15:00, check-out 11:00"],
    cancellationPolicy: "Free cancellation up to 5 days before check-in. 50% refund within 5 days.",
    host: hosts.david,
    popularity: 85,
    tags: ["Garni", "Eco", "Canyon Views", "Off-Grid", "Stargazing"],
    reviews: [
      { author: "Hannah T.", avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=40&h=40&fit=crop&crop=face", date: "October 2025", rating: 5, text: "The most magical place I&#39;ve stayed in the Caucasus. Sunrise over the gorge from the veranda was life-changing." },
    ],
  },
  {
    slug: "ski-chalet-tsaghkadzor",
    type: "cottage",
    title: "Ski-In Chalet at Tsaghkadzor Resort",
    shortDescription: "Slope-side chalet sleeping 6, sauna, mountain views, perfect for winter ski holidays.",
    description: "Step out the front door and onto the ski slopes of Tsaghkadzor — Armenia&#39;s top winter mountain resort. This chalet-style cottage has three bedrooms, a large open-plan living area with a wood-burning stove, a private sauna, and a terrace with ski slope views. The ski lifts are a 2-minute walk. In summer the same area transforms with hiking trails and mountain flowers. Tsaghkadzor is just 50 km from Yerevan, making it ideal for a quick weekend escape.",
    location: { country: "Armenia", region: "Kotayk", city: "Tsaghkadzor", district: "Ski Zone", address: "Slope Road 5, Tsaghkadzor", lat: 40.5380, lng: 44.7220 },
    images: [
      { src: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=900&h=600&fit=crop", alt: "Chalet exterior in snow" },
      { src: "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=900&h=600&fit=crop", alt: "Warm living room interior" },
      { src: "https://images.unsplash.com/photo-1601918774946-25832a4be0d6?w=900&h=600&fit=crop", alt: "Mountain view from terrace" },
      { src: "https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&h=600&fit=crop", alt: "Sauna" },
    ],
    pricePerNight: 150,
    currency: "USD",
    rating: 4.9,
    reviewsCount: 44,
    instantBooking: false,
    rooms: 3,
    beds: 4,
    bathrooms: 2,
    maxGuests: 6,
    areaSqm: 120,
    amenities: ["wifi", "parking", "kitchen", "heating", "fireplace", "hotTub", "tv", "balcony"],
    houseRules: ["No smoking inside", "Ski equipment storage available", "Pets not allowed", "Check-in 15:00, check-out 11:00"],
    cancellationPolicy: "Free cancellation up to 7 days before check-in. Non-refundable within 7 days.",
    host: hosts.lara,
    popularity: 91,
    tags: ["Tsaghkadzor", "Ski", "Chalet", "Mountain", "Sauna", "Winter"],
    reviews: [
      { author: "Dmitri K.", avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face", date: "January 2026", rating: 5, text: "Ski-in chalet at an unbeatable price. Sauna after a day on the slopes — paradise. Lara made everything effortless." },
      { author: "Lisa M.", avatar: "https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=40&h=40&fit=crop&crop=face", date: "December 2025", rating: 5, text: "Best ski holiday I&#39;ve had outside the Alps. Armenia surprised us completely — and the chalet was immaculate." },
    ],
  },
];

export const propertyTypes: { value: PropertyType | ""; label: string; emoji: string }[] = [
  { value: "", label: "All Types", emoji: "🏠" },
  { value: "apartment", label: "Apartment", emoji: "🏢" },
  { value: "hotel", label: "Hotel", emoji: "🏨" },
  { value: "house", label: "House", emoji: "🏡" },
  { value: "cottage", label: "Cottage", emoji: "🌲" },
  { value: "villa", label: "Villa", emoji: "🌴" },
];

export const rentalLocations: RentalLocation[] = ["Yerevan", "Dilijan", "Gyumri", "Sevan", "Garni", "Tsaghkadzor", "Goris", "Tatev"];

export function getPropertyBySlug(slug: string): RentalProperty | undefined {
  return rentalProperties.find((p) => p.slug === slug);
}

export function getRelatedProperties(prop: RentalProperty, count = 4): RentalProperty[] {
  return rentalProperties
    .filter((p) => p.slug !== prop.slug && (p.type === prop.type || p.location.city === prop.location.city))
    .slice(0, count);
}
