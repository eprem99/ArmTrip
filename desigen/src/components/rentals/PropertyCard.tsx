import React, { useState } from "react";
import { Link } from "react-router-dom";
import {
  MapPin, Star, Heart, Lightning, Users, Bathtub, Bed,
  ArrowLeft, ArrowRight, CheckCircle,
} from "@phosphor-icons/react";
import { RentalProperty, propertyTypes } from "../../data/rentalsData";

interface PropertyCardProps {
  property: RentalProperty;
  isHighlighted?: boolean;
  viewMode?: "grid" | "list";
}

const typeBadgeColors: Record<string, string> = {
  apartment: "bg-sky-50 text-sky-700 border-sky-200",
  hotel: "bg-violet-50 text-violet-700 border-violet-200",
  house: "bg-emerald-50 text-emerald-700 border-emerald-200",
  cottage: "bg-amber-50 text-amber-700 border-amber-200",
  villa: "bg-rose-50 text-rose-700 border-rose-200",
};

const amenityIcons: Record<string, string> = {
  wifi: "📶",
  parking: "🅿️",
  pool: "🏊",
  ac: "❄️",
  kitchen: "🍳",
  washing: "🫧",
  tv: "📺",
  heating: "🔥",
  balcony: "🌿",
  gym: "💪",
  breakfast: "☕",
  petFriendly: "🐾",
  bbq: "🥩",
  fireplace: "🪵",
  garden: "🌳",
  hotTub: "🛁",
};

const amenityLabels: Record<string, string> = {
  wifi: "WiFi",
  parking: "Parking",
  pool: "Pool",
  ac: "A/C",
  kitchen: "Kitchen",
  washing: "Washer",
  tv: "TV",
  heating: "Heating",
  balcony: "Balcony",
  gym: "Gym",
  breakfast: "Breakfast",
  petFriendly: "Pets OK",
  bbq: "BBQ",
  fireplace: "Fireplace",
  garden: "Garden",
  hotTub: "Hot Tub",
};

export default function PropertyCard({
  property,
  isHighlighted = false,
  viewMode = "grid",
}: PropertyCardProps) {
  const [wished, setWished] = useState(false);
  const [imgIndex, setImgIndex] = useState(0);
  const [wishAnim, setWishAnim] = useState(false);
  const typeInfo = propertyTypes.find((t) => t.value === property.type);

  const handleWish = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setWished((w) => !w);
    setWishAnim(true);
    setTimeout(() => setWishAnim(false), 400);
  };

  const cycleImg = (e: React.MouseEvent, dir: number) => {
    e.preventDefault();
    e.stopPropagation();
    setImgIndex((i) => (i + dir + property.images.length) % property.images.length);
  };

  const isNew = property.reviewsCount < 20;
  const isTopRated = property.rating >= 4.9;
  const hasDiscount = property.popularity > 90;

  if (viewMode === "list") {
    return (
      <Link
        to={`/rentals/${property.slug}`}
        className={`group flex bg-white rounded-2xl overflow-hidden border transition-all duration-300 ${
          isHighlighted
            ? "border-blue-400 shadow-lg shadow-blue-100 ring-2 ring-blue-200"
            : "border-gray-150 shadow-sm hover:shadow-xl hover:shadow-gray-200/60 hover:-translate-y-0.5"
        }`}
        style={{ borderColor: isHighlighted ? undefined : "hsl(210,14%,93%)" }}
        aria-label={`View ${property.title}`}
      >
        {/* Image */}
        <div className="relative flex-shrink-0 w-56 md:w-72" style={{ minHeight: "200px" }}>
          <img
            src={property.images[imgIndex].src}
            alt={property.images[imgIndex].alt}
            className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
          />
          {/* Badges */}
          <div className="absolute top-3 left-3 flex flex-col gap-1.5">
            {isTopRated && (
              <span className="flex items-center gap-1 text-[11px] font-semibold bg-amber-400 text-white px-2 py-0.5 rounded-full shadow-sm">
                ⭐ Top Rated
              </span>
            )}
            {isNew && (
              <span className="text-[11px] font-semibold bg-emerald-500 text-white px-2 py-0.5 rounded-full shadow-sm">
                ✨ New
              </span>
            )}
          </div>
          <button
            onClick={handleWish}
            className={`absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 hover:bg-white shadow-md transition-all duration-200 cursor-pointer ${wishAnim ? "scale-125" : "scale-100"}`}
            aria-label={wished ? "Remove from wishlist" : "Save to wishlist"}
          >
            <Heart
              size={17}
              weight={wished ? "fill" : "regular"}
              className={wished ? "text-rose-500" : "text-gray-500 group-hover:text-gray-700"}
            />
          </button>
        </div>
        {/* Content */}
        <div className="flex flex-col flex-1 p-5">
          <div className="flex items-start justify-between gap-4 mb-2">
            <div>
              <div className="flex items-center gap-2 mb-1">
                <span className={`text-[11px] font-semibold px-2 py-0.5 rounded-full border ${typeBadgeColors[property.type]}`}>
                  {typeInfo?.emoji} {typeInfo?.label}
                </span>
                {property.instantBooking && (
                  <span className="flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                    <Lightning size={10} weight="fill" /> Instant
                  </span>
                )}
              </div>
              <h3 className="font-heading font-semibold text-gray-900 text-base leading-snug group-hover:text-blue-600 transition-colors duration-200">
                {property.title}
              </h3>
              <div className="flex items-center gap-1 text-xs text-gray-500 mt-1">
                <MapPin size={11} weight="bold" className="text-blue-400" />
                <span>{property.location.district}, {property.location.city}</span>
              </div>
            </div>
            <div className="text-right flex-shrink-0">
              <p className="font-heading font-bold text-gray-900 text-xl">${property.pricePerNight}</p>
              <p className="text-xs text-gray-400">/ night</p>
            </div>
          </div>
          <p className="text-sm text-gray-500 line-clamp-2 mb-3">{property.shortDescription}</p>
          <div className="flex items-center gap-4 text-xs text-gray-500 mb-3">
            <span className="flex items-center gap-1"><Bed size={13} /> {property.beds} bed{property.beds !== 1 ? "s" : ""}</span>
            <span className="flex items-center gap-1"><Bathtub size={13} /> {property.bathrooms} bath</span>
            <span className="flex items-center gap-1"><Users size={13} /> {property.maxGuests} guests</span>
            <span>{property.areaSqm} m²</span>
          </div>
          <div className="flex gap-1.5 flex-wrap mb-auto">
            {property.amenities.slice(0, 5).map((a) => (
              <span key={a} className="text-xs bg-gray-50 text-gray-500 px-2 py-0.5 rounded-full border border-gray-100">
                {amenityIcons[a]} {amenityLabels[a]}
              </span>
            ))}
          </div>
          <div className="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
            <div className="flex items-center gap-1.5">
              <Star size={14} weight="fill" className="text-amber-400" />
              <span className="text-sm font-bold text-gray-800">{property.rating.toFixed(1)}</span>
              <span className="text-xs text-gray-400">({property.reviewsCount} reviews)</span>
            </div>
            <span className="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full font-medium border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
              View Details →
            </span>
          </div>
        </div>
      </Link>
    );
  }

  // ── GRID CARD ──
  return (
    <div
      className={`group flex flex-col bg-white rounded-2xl overflow-hidden border transition-all duration-300 h-full ${
        isHighlighted
          ? "border-blue-400 shadow-xl shadow-blue-100 ring-2 ring-blue-200"
          : "border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-200/70 hover:-translate-y-1"
      }`}
      style={{ borderColor: isHighlighted ? undefined : "hsl(210,14%,93%)" }}
    >
      {/* Image gallery */}
      <div className="relative overflow-hidden flex-shrink-0" style={{ height: "226px" }}>
        <Link to={`/rentals/${property.slug}`} aria-label={`View ${property.title}`}>
          <img
            key={imgIndex}
            src={property.images[imgIndex].src}
            alt={property.images[imgIndex].alt}
            className="w-full h-full object-cover transition-transform duration-600 group-hover:scale-105"
            loading="lazy"
          />
          {/* Dark gradient bottom */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        </Link>

        {/* Prev / Next arrows on hover */}
        {property.images.length > 1 && (
          <>
            <button
              onClick={(e) => cycleImg(e, -1)}
              className="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-gray-700 shadow-md transition-all duration-200 opacity-0 group-hover:opacity-100 cursor-pointer -translate-x-1 group-hover:translate-x-0"
              aria-label="Previous image"
            >
              <ArrowLeft size={14} weight="bold" />
            </button>
            <button
              onClick={(e) => cycleImg(e, 1)}
              className="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-gray-700 shadow-md transition-all duration-200 opacity-0 group-hover:opacity-100 cursor-pointer translate-x-1 group-hover:translate-x-0"
              aria-label="Next image"
            >
              <ArrowRight size={14} weight="bold" />
            </button>
          </>
        )}

        {/* Image dots */}
        {property.images.length > 1 && (
          <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            {property.images.map((_, i) => (
              <button
                key={i}
                onClick={(e) => { e.preventDefault(); e.stopPropagation(); setImgIndex(i); }}
                className={`rounded-full transition-all duration-200 cursor-pointer ${
                  i === imgIndex ? "bg-white w-4 h-1.5" : "bg-white/60 hover:bg-white/90 w-1.5 h-1.5"
                }`}
                aria-label={`Image ${i + 1}`}
              />
            ))}
          </div>
        )}

        {/* Top left badges */}
        <div className="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
          {isTopRated && (
            <span className="flex items-center gap-1 text-[10px] font-bold bg-amber-400 text-white px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wide">
              ⭐ Top Rated
            </span>
          )}
          {isNew && !isTopRated && (
            <span className="text-[10px] font-bold bg-emerald-500 text-white px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wide">
              ✨ New
            </span>
          )}
          {hasDiscount && !isNew && !isTopRated && (
            <span className="text-[10px] font-bold bg-rose-500 text-white px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wide">
              🔥 Popular
            </span>
          )}
        </div>

        {/* Instant Booking */}
        {property.instantBooking && (
          <span className="absolute bottom-3 left-3 flex items-center gap-1 text-[10px] font-semibold bg-white/95 text-amber-600 px-2 py-0.5 rounded-full shadow-sm">
            <Lightning size={10} weight="fill" />
            Instant Book
          </span>
        )}

        {/* Wishlist */}
        <button
          onClick={handleWish}
          className={`absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 hover:bg-white shadow-md transition-all duration-200 cursor-pointer ${
            wishAnim ? "scale-125" : "scale-100"
          }`}
          aria-label={wished ? "Remove from wishlist" : "Save to wishlist"}
        >
          <Heart
            size={16}
            weight={wished ? "fill" : "regular"}
            className={wished ? "text-rose-500" : "text-gray-500 group-hover:text-gray-700"}
          />
        </button>
      </div>

      {/* Card body */}
      <Link to={`/rentals/${property.slug}`} className="flex flex-col flex-1 p-4 gap-2">
        {/* Type + rating row */}
        <div className="flex items-center justify-between">
          <span className={`text-[11px] font-semibold px-2 py-0.5 rounded-full border ${typeBadgeColors[property.type]}`}>
            {typeInfo?.emoji} {typeInfo?.label}
          </span>
          <div className="flex items-center gap-1">
            <Star size={12} weight="fill" className="text-amber-400" />
            <span className="text-xs font-bold text-gray-800">{property.rating.toFixed(1)}</span>
            <span className="text-xs text-gray-400">({property.reviewsCount})</span>
          </div>
        </div>

        {/* Title */}
        <h3 className="font-heading font-semibold text-gray-900 text-[15px] leading-snug group-hover:text-blue-600 transition-colors duration-200 line-clamp-2">
          {property.title}
        </h3>

        {/* Location */}
        <div className="flex items-center gap-1 text-xs text-gray-400">
          <MapPin size={11} weight="bold" className="text-blue-400 flex-shrink-0" />
          <span>{property.location.district}, {property.location.city}</span>
        </div>

        {/* Specs row */}
        <div className="flex items-center gap-2.5 text-xs text-gray-400 flex-wrap">
          <span className="flex items-center gap-1"><Bed size={12} /> {property.beds} beds</span>
          <span className="text-gray-200">·</span>
          <span className="flex items-center gap-1"><Bathtub size={12} /> {property.bathrooms} bath</span>
          <span className="text-gray-200">·</span>
          <span className="flex items-center gap-1"><Users size={12} /> up to {property.maxGuests}</span>
        </div>

        {/* Amenities chips */}
        <div className="flex gap-1 flex-wrap">
          {property.amenities.slice(0, 4).map((a) => (
            <span key={a} className="text-[11px] bg-gray-50 text-gray-500 px-2 py-0.5 rounded-full border border-gray-100">
              {amenityIcons[a]} {amenityLabels[a]}
            </span>
          ))}
          {property.amenities.length > 4 && (
            <span className="text-[11px] text-gray-400 px-1 py-0.5">+{property.amenities.length - 4}</span>
          )}
        </div>

        {/* Footer */}
        <div className="flex items-center justify-between pt-3 mt-auto border-t border-gray-50">
          <div>
            <span className="font-heading font-bold text-gray-900 text-lg">${property.pricePerNight}</span>
            <span className="text-xs text-gray-400"> / night</span>
          </div>
          <span className="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full font-medium border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
            View →
          </span>
        </div>
      </Link>
    </div>
  );
}
