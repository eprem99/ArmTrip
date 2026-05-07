import React, { useState } from "react";
import { Link, useParams, Navigate } from "react-router-dom";
import {
  MapPin, Star, Heart, Lightning, Users, Bed, Bathtub, ArrowsOut,
  CheckCircle, XCircle, Phone, ChatCircle, ShareNetwork, ArrowLeft,
  CalendarBlank, Plus, Minus, Info, Ladder, SquareHalf,
} from "@phosphor-icons/react";
import { getPropertyBySlug, getRelatedProperties, amenitiesMeta, propertyTypes } from "../data/rentalsData";
import PropertyCard from "../components/rentals/PropertyCard";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

function StarRating({ value, max = 5 }: { value: number; max?: number }) {
  return (
    <div className="flex items-center gap-0.5" aria-label={`Rating: ${value} out of ${max}`}>
      {Array.from({ length: max }).map((_, i) => (
        <Star
          key={i}
          size={14}
          weight={i < Math.floor(value) ? "fill" : i < value ? "duotone" : "regular"}
          className={i < value ? "text-amber-400" : "text-gray-200"}
        />
      ))}
    </div>
  );
}

export default function RentalPropertyPage() {
  const { slug } = useParams<{ slug: string }>();
  const property = getPropertyBySlug(slug ?? "");
  if (!property) return <Navigate to="/rentals" replace />;

  const related = getRelatedProperties(property, 4);
  const [activeImg, setActiveImg] = useState(0);
  const [lightboxOpen, setLightboxOpen] = useState(false);
  const [wished, setWished] = useState(false);
  const [checkIn, setCheckIn] = useState("");
  const [checkOut, setCheckOut] = useState("");
  const [adults, setAdults] = useState(2);
  const [children, setChildren] = useState(0);
  const typeInfo = propertyTypes.find((t) => t.value === property.type);

  const nights = checkIn && checkOut
    ? Math.max(0, Math.round((new Date(checkOut).getTime() - new Date(checkIn).getTime()) / 86400000))
    : 0;
  const subtotal = nights * property.pricePerNight;
  const serviceFee = Math.round(subtotal * 0.1);
  const total = subtotal + serviceFee;

  const typeColors: Record<string, string> = {
    apartment: "bg-sky-100 text-sky-700",
    hotel: "bg-violet-100 text-violet-700",
    house: "bg-emerald-100 text-emerald-700",
    cottage: "bg-amber-100 text-amber-700",
    villa: "bg-rose-100 text-rose-700",
  };

  const amenityGroups: Record<string, string[]> = {
    "Connectivity & Entertainment": ["wifi", "tv"],
    "Kitchen & Laundry": ["kitchen", "washing"],
    "Climate": ["ac", "heating"],
    "Outdoor & Leisure": ["pool", "hotTub", "garden", "balcony", "bbq", "fireplace"],
    "Parking & Fitness": ["parking", "gym"],
    "Extras": ["breakfast", "petFriendly"],
  };

  return (
    <div className="min-h-screen bg-gray-50 text-foreground font-sans">
      <Navbar />

      {/* ── GALLERY ── */}
      <section className="bg-gray-900 pt-16" aria-label="Property gallery">
        <div className="max-w-7xl mx-auto px-6 py-6">
          {/* Breadcrumb */}
          <nav className="flex items-center gap-2 text-sm text-gray-400 mb-4" aria-label="Breadcrumb">
            <Link to="/" className="hover:text-white transition-colors">Home</Link>
            <span>/</span>
            <Link to="/rentals" className="hover:text-white transition-colors">Rentals</Link>
            <span>/</span>
            <Link to={`/rentals/${property.location.city.toLowerCase()}`} className="hover:text-white transition-colors capitalize">{property.location.city}</Link>
            <span>/</span>
            <span className="text-gray-300 truncate max-w-xs">{property.title}</span>
          </nav>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-2 rounded-2xl overflow-hidden" style={{ maxHeight: "520px" }}>
            {/* Main image */}
            <div
              className="relative cursor-pointer group"
              onClick={() => setLightboxOpen(true)}
              style={{ height: "520px" }}
            >
              <img
                src={property.images[activeImg].src}
                alt={property.images[activeImg].alt}
                className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
              />
              <div className="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
              <button
                className="absolute bottom-4 right-4 flex items-center gap-1.5 bg-white text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full shadow-md hover:bg-gray-50 transition-colors cursor-pointer opacity-0 group-hover:opacity-100"
                aria-label="View fullscreen"
              >
                <ArrowsOut size={14} />
                Fullscreen
              </button>
            </div>

            {/* Thumbnails grid */}
            <div className="hidden md:grid grid-cols-2 gap-2" style={{ height: "520px" }}>
              {property.images.slice(1, 5).map((img, i) => (
                <div
                  key={i}
                  className={`relative cursor-pointer overflow-hidden ${i + 1 === activeImg ? "ring-3 ring-primary" : ""}`}
                  onClick={() => { setActiveImg(i + 1); }}
                  style={{ height: "calc(520px / 2 - 4px)" }}
                >
                  <img
                    src={img.src}
                    alt={img.alt}
                    className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                  />
                  {i === 3 && property.images.length > 5 && (
                    <div className="absolute inset-0 bg-black/50 flex items-center justify-center">
                      <span className="text-white font-semibold text-sm">+{property.images.length - 5} more</span>
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>

          {/* Mobile thumbnails */}
          <div className="flex gap-2 mt-2 md:hidden overflow-x-auto pb-1">
            {property.images.map((img, i) => (
              <button
                key={i}
                onClick={() => setActiveImg(i)}
                className={`flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all cursor-pointer ${i === activeImg ? "border-primary" : "border-transparent opacity-70"}`}
              >
                <img src={img.src} alt={img.alt} className="w-full h-full object-cover" />
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* ── MAIN CONTENT ── */}
      <main className="max-w-7xl mx-auto px-6 py-10">
        <div className="flex gap-8 items-start">

          {/* ── LEFT: Property Details ── */}
          <div className="flex-1 min-w-0">
            <Link
              to="/rentals"
              className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition-colors mb-6 cursor-pointer"
            >
              <ArrowLeft size={16} />
              Back to all rentals
            </Link>

            {/* Title block */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
              <div className="flex items-start justify-between gap-4 flex-wrap mb-3">
                <div>
                  <div className="flex items-center gap-2 mb-2 flex-wrap">
                    <span className={`text-xs font-semibold px-2.5 py-1 rounded-full ${typeColors[property.type]}`}>
                      {typeInfo?.emoji} {typeInfo?.label}
                    </span>
                    {property.instantBooking && (
                      <span className="flex items-center gap-1 text-xs font-medium bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full border border-amber-200">
                        <Lightning size={11} weight="fill" />
                        Instant Booking
                      </span>
                    )}
                  </div>
                  <h1 className="font-heading font-bold text-gray-900 text-2xl md:text-3xl leading-snug mb-2">
                    {property.title}
                  </h1>
                  <a
                    href={`https://www.google.com/maps/search/${encodeURIComponent(property.location.address)}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-1.5 text-sm text-primary hover:underline cursor-pointer"
                    aria-label="Open location in Google Maps"
                  >
                    <MapPin size={15} weight="bold" />
                    {property.location.district}, {property.location.city}, {property.location.region}
                  </a>
                </div>
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setWished((w) => !w)}
                    className="flex items-center gap-1.5 px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-rose-300 hover:text-rose-500 transition-colors cursor-pointer"
                    aria-label={wished ? "Remove from wishlist" : "Save to wishlist"}
                  >
                    <Heart size={16} weight={wished ? "fill" : "regular"} className={wished ? "text-rose-500" : ""} />
                    {wished ? "Saved" : "Save"}
                  </button>
                  <button className="flex items-center gap-1.5 px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-primary hover:text-primary transition-colors cursor-pointer">
                    <ShareNetwork size={16} />
                    Share
                  </button>
                </div>
              </div>

              {/* Rating & reviews */}
              <div className="flex items-center gap-3 pt-3 border-t border-gray-50">
                <StarRating value={property.rating} />
                <span className="font-semibold text-gray-800">{property.rating.toFixed(1)}</span>
                <span className="text-sm text-gray-400">({property.reviewsCount} reviews)</span>
              </div>
            </div>

            {/* Quick specs */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
              {[
                { icon: <SquareHalf size={22} className="text-primary" />, label: "Rooms", value: `${property.rooms} room${property.rooms !== 1 ? "s" : ""}` },
                { icon: <Bed size={22} className="text-primary" />, label: "Beds", value: `${property.beds} bed${property.beds !== 1 ? "s" : ""}` },
                { icon: <Bathtub size={22} className="text-primary" />, label: "Bathrooms", value: `${property.bathrooms} bath${property.bathrooms !== 1 ? "s" : ""}` },
                { icon: <Users size={22} className="text-primary" />, label: "Max guests", value: `${property.maxGuests} guests` },
              ].map((s) => (
                <div key={s.label} className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                  {s.icon}
                  <div>
                    <p className="text-xs text-gray-400 leading-none mb-0.5">{s.label}</p>
                    <p className="text-sm font-semibold text-gray-800">{s.value}</p>
                  </div>
                </div>
              ))}
            </div>

            {/* Description */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
              <h2 className="font-heading font-semibold text-gray-800 text-lg mb-4">About this property</h2>
              <p className="text-gray-600 leading-relaxed text-[15px]">{property.description}</p>

              {/* Floor / area info */}
              <div className="flex flex-wrap gap-4 mt-5 pt-5 border-t border-gray-50">
                <div className="flex items-center gap-2 text-sm text-gray-500">
                  <ArrowsOut size={16} className="text-gray-400" />
                  <span><strong className="text-gray-700">{property.areaSqm} m²</strong> area</span>
                </div>
                {property.floor && (
                  <div className="flex items-center gap-2 text-sm text-gray-500">
                    <Ladder size={16} className="text-gray-400" />
                    <span>Floor <strong className="text-gray-700">{property.floor}</strong>{property.totalFloors ? ` of ${property.totalFloors}` : ""}</span>
                  </div>
                )}
                <div className="flex items-center gap-2 text-sm text-gray-500">
                  <Users size={16} className="text-gray-400" />
                  <span>Up to <strong className="text-gray-700">{property.maxGuests}</strong> guests</span>
                </div>
              </div>
            </div>

            {/* Amenities */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
              <h2 className="font-heading font-semibold text-gray-800 text-lg mb-5">What&#39;s included</h2>
              <div className="space-y-6">
                {Object.entries(amenityGroups).map(([groupName, keys]) => {
                  const available = keys.filter((k) => property.amenities.includes(k));
                  if (available.length === 0) return null;
                  return (
                    <div key={groupName}>
                      <h3 className="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">{groupName}</h3>
                      <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        {available.map((key) => {
                          const meta = amenitiesMeta[key];
                          return (
                            <div key={key} className="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl">
                              <span className="text-lg">{meta.emoji}</span>
                              <span className="text-sm text-gray-700 font-medium">{meta.label}</span>
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* House Rules & Cancellation */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 className="font-heading font-semibold text-gray-800 text-[15px] mb-4">🏠 House Rules</h2>
                <ul className="space-y-2.5">
                  {property.houseRules.map((rule, i) => (
                    <li key={i} className="flex items-start gap-2.5 text-sm text-gray-600">
                      <CheckCircle size={16} className="text-green-500 flex-shrink-0 mt-0.5" />
                      {rule}
                    </li>
                  ))}
                </ul>
              </div>
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 className="font-heading font-semibold text-gray-800 text-[15px] mb-4">🔄 Cancellation Policy</h2>
                <div className="flex items-start gap-2.5">
                  <Info size={16} className="text-primary flex-shrink-0 mt-0.5" />
                  <p className="text-sm text-gray-600 leading-relaxed">{property.cancellationPolicy}</p>
                </div>
              </div>
            </div>

            {/* Map placeholder */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
              <h2 className="font-heading font-semibold text-gray-800 text-lg mb-4">📍 Location</h2>
              <p className="text-sm text-gray-500 mb-4">{property.location.address}</p>
              <div className="w-full rounded-xl overflow-hidden bg-gradient-to-br from-green-50 to-sky-50 border border-gray-100 flex items-center justify-center" style={{ height: "280px" }}>
                <div className="text-center">
                  <MapPin size={48} className="mx-auto mb-3 text-primary/30" />
                  <p className="text-sm font-semibold text-gray-500">{property.location.city}, {property.location.region}</p>
                  <p className="text-xs text-gray-400 mt-1">Exact location provided after booking</p>
                  <a
                    href={`https://www.google.com/maps?q=${property.location.lat},${property.location.lng}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-1.5 mt-3 text-xs text-primary border border-primary/30 px-3 py-1.5 rounded-full hover:bg-primary/5 transition-colors cursor-pointer"
                  >
                    <MapPin size={12} />
                    Open in Google Maps
                  </a>
                </div>
              </div>
            </div>

            {/* Reviews */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
              <div className="flex items-center justify-between mb-6">
                <h2 className="font-heading font-semibold text-gray-800 text-lg flex items-center gap-2">
                  <Star size={18} weight="fill" className="text-amber-400" />
                  {property.rating.toFixed(1)} · {property.reviewsCount} Reviews
                </h2>
              </div>

              {/* Rating breakdown */}
              <div className="grid grid-cols-2 gap-3 mb-8 pb-6 border-b border-gray-100">
                {[
                  { label: "Cleanliness", score: 4.8 },
                  { label: "Accuracy", score: 4.7 },
                  { label: "Location", score: 4.9 },
                  { label: "Value", score: 4.6 },
                ].map((r) => (
                  <div key={r.label} className="flex items-center justify-between gap-3">
                    <span className="text-sm text-gray-600">{r.label}</span>
                    <div className="flex items-center gap-2 flex-1">
                      <div className="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div className="h-full bg-primary rounded-full" style={{ width: `${(r.score / 5) * 100}%` }} />
                      </div>
                      <span className="text-xs font-medium text-gray-700 w-6 text-right">{r.score}</span>
                    </div>
                  </div>
                ))}
              </div>

              <div className="space-y-5">
                {property.reviews.map((review, i) => (
                  <div key={i} className="pb-5 border-b border-gray-50 last:border-0 last:pb-0">
                    <div className="flex items-center gap-3 mb-3">
                      <img src={review.avatar} alt={review.author} className="w-10 h-10 rounded-full object-cover" />
                      <div>
                        <p className="font-semibold text-gray-800 text-sm">{review.author}</p>
                        <p className="text-xs text-gray-400">{review.date}</p>
                      </div>
                      <div className="ml-auto">
                        <StarRating value={review.rating} />
                      </div>
                    </div>
                    <p className="text-sm text-gray-600 leading-relaxed">{review.text}</p>
                  </div>
                ))}
              </div>
            </div>

            {/* Host */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-10">
              <h2 className="font-heading font-semibold text-gray-800 text-lg mb-5">Hosted by</h2>
              <div className="flex items-start gap-4">
                <div className="relative flex-shrink-0">
                  <img src={property.host.avatar} alt={property.host.name} className="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm" />
                  <div className="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white" title="Online" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="font-heading font-semibold text-gray-900 text-[16px]">{property.host.name}</p>
                  <p className="text-xs text-gray-400 mb-1">Host since {property.host.joined} · {property.host.responseRate}% response rate</p>
                  <p className="text-sm text-gray-600 leading-relaxed">{property.host.bio}</p>
                  <div className="flex gap-3 mt-4">
                    <button className="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-hover transition-colors cursor-pointer">
                      <ChatCircle size={16} />
                      Message Host
                    </button>
                    <button className="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-xl hover:border-primary hover:text-primary transition-colors cursor-pointer">
                      <Phone size={16} />
                      Contact
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* ── RIGHT: Sticky Booking Card ── */}
          <aside className="hidden lg:block w-80 xl:w-96 flex-shrink-0 sticky top-28 self-start" aria-label="Booking widget">
            <div className="bg-white rounded-2xl border border-gray-100 shadow-lg p-6">
              {/* Price */}
              <div className="flex items-baseline justify-between mb-5 pb-5 border-b border-gray-50">
                <div>
                  <span className="font-heading font-bold text-gray-900 text-2xl">${property.pricePerNight}</span>
                  <span className="text-sm text-gray-400"> / night</span>
                </div>
                <div className="flex items-center gap-1">
                  <Star size={14} weight="fill" className="text-amber-400" />
                  <span className="text-sm font-semibold">{property.rating.toFixed(1)}</span>
                  <span className="text-xs text-gray-400">({property.reviewsCount})</span>
                </div>
              </div>

              {/* Date pickers */}
              <div className="rounded-xl border border-gray-200 overflow-hidden mb-4">
                <div className="grid grid-cols-2 divide-x divide-gray-200">
                  <div className="p-3">
                    <label className="block text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Check-in</label>
                    <input
                      type="date"
                      value={checkIn}
                      onChange={(e) => setCheckIn(e.target.value)}
                      className="w-full text-sm font-medium text-gray-800 bg-transparent focus:outline-none cursor-pointer"
                      aria-label="Check-in date"
                    />
                  </div>
                  <div className="p-3">
                    <label className="block text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Check-out</label>
                    <input
                      type="date"
                      value={checkOut}
                      onChange={(e) => setCheckOut(e.target.value)}
                      className="w-full text-sm font-medium text-gray-800 bg-transparent focus:outline-none cursor-pointer"
                      aria-label="Check-out date"
                    />
                  </div>
                </div>
                <div className="border-t border-gray-200 p-3">
                  <label className="block text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Guests</label>
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                      <span className="text-sm text-gray-600">Adults</span>
                      <div className="flex items-center gap-2">
                        <button onClick={() => setAdults(Math.max(1, adults - 1))} className="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-primary hover:text-primary transition-colors cursor-pointer text-gray-600">
                          <Minus size={12} />
                        </button>
                        <span className="text-sm font-semibold text-gray-800 w-4 text-center">{adults}</span>
                        <button onClick={() => setAdults(Math.min(property.maxGuests, adults + 1))} className="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-primary hover:text-primary transition-colors cursor-pointer text-gray-600">
                          <Plus size={12} />
                        </button>
                      </div>
                    </div>
                    <div className="flex items-center gap-3">
                      <span className="text-sm text-gray-600">Children</span>
                      <div className="flex items-center gap-2">
                        <button onClick={() => setChildren(Math.max(0, children - 1))} className="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-primary hover:text-primary transition-colors cursor-pointer text-gray-600">
                          <Minus size={12} />
                        </button>
                        <span className="text-sm font-semibold text-gray-800 w-4 text-center">{children}</span>
                        <button onClick={() => setChildren(Math.min(property.maxGuests - adults, children + 1))} className="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:border-primary hover:text-primary transition-colors cursor-pointer text-gray-600">
                          <Plus size={12} />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Price breakdown */}
              {nights > 0 && (
                <div className="space-y-2.5 mb-5 pb-5 border-b border-gray-100">
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>${property.pricePerNight} × {nights} night{nights !== 1 ? "s" : ""}</span>
                    <span>${subtotal}</span>
                  </div>
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>Service fee</span>
                    <span>${serviceFee}</span>
                  </div>
                  <div className="flex justify-between font-semibold text-gray-900 text-[15px] pt-2 border-t border-gray-100">
                    <span>Total</span>
                    <span>${total}</span>
                  </div>
                </div>
              )}

              {/* CTA */}
              <button
                className="w-full py-3.5 bg-primary text-white font-bold text-base rounded-xl hover:bg-primary-hover transition-colors duration-200 cursor-pointer shadow-sm hover:shadow-md"
                aria-label="Reserve this property"
              >
                {property.instantBooking ? "⚡ Book Instantly" : "Reserve"}
              </button>
              <p className="text-center text-xs text-gray-400 mt-3">You won&#39;t be charged yet</p>

              {/* Policies summary */}
              <div className="mt-4 pt-4 border-t border-gray-50 space-y-2">
                <div className="flex items-start gap-2 text-xs text-gray-500">
                  <CheckCircle size={14} className="text-green-500 flex-shrink-0 mt-0.5" />
                  <span>{property.cancellationPolicy.split(".")[0]}.</span>
                </div>
                <div className="flex items-start gap-2 text-xs text-gray-500">
                  <Info size={14} className="text-primary flex-shrink-0 mt-0.5" />
                  <span>Max {property.maxGuests} guests allowed.</span>
                </div>
              </div>
            </div>

            {/* Need help */}
            <div className="mt-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
              <p className="text-xs text-gray-500 mb-2">Need help choosing?</p>
              <button className="text-sm text-primary font-medium hover:underline cursor-pointer">
                Chat with an ArmTrip expert →
              </button>
            </div>
          </aside>
        </div>

        {/* ── RELATED PROPERTIES ── */}
        {related.length > 0 && (
          <section className="mt-16 pt-10 border-t border-gray-100" aria-label="Similar properties">
            <div className="flex items-center justify-between mb-8">
              <h2 className="font-heading font-semibold text-gray-900 text-2xl">Similar Properties</h2>
              <Link to="/rentals" className="text-sm text-primary hover:underline font-medium">View all →</Link>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {related.map((p) => (
                <PropertyCard key={p.slug} property={p} />
              ))}
            </div>
          </section>
        )}
      </main>

      {/* Mobile sticky booking bar */}
      <div className="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 z-40">
        <div className="flex items-center justify-between gap-4 max-w-lg mx-auto">
          <div>
            <span className="font-heading font-bold text-gray-900 text-xl">${property.pricePerNight}</span>
            <span className="text-sm text-gray-400"> / night</span>
            <div className="flex items-center gap-1 mt-0.5">
              <Star size={12} weight="fill" className="text-amber-400" />
              <span className="text-xs text-gray-600">{property.rating.toFixed(1)} ({property.reviewsCount})</span>
            </div>
          </div>
          <button className="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-hover transition-colors cursor-pointer text-sm shadow-sm">
            {property.instantBooking ? "⚡ Book Now" : "Reserve"}
          </button>
        </div>
      </div>

      <Footer />
    </div>
  );
}
