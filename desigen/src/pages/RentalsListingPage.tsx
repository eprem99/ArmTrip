import React, { useState, useMemo, useCallback, useRef } from "react";
import { Link } from "react-router-dom";
import {
  MagnifyingGlass, X, SlidersHorizontal, Star, MapPin as MapPinIcon,
  CalendarBlank, Users, Plus, Minus, GridFour, List as ListIcon,
  Heart, Bell, CaretDown, ArrowUp, ArrowDown, Lightning,
  Funnel, MapTrifold,
} from "@phosphor-icons/react";
import {
  rentalProperties, propertyTypes, rentalLocations,
  amenitiesMeta, PropertyType, RentalLocation, SortOption,
} from "../data/rentalsData";
import PropertyCard from "../components/rentals/PropertyCard";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const ITEMS_PER_PAGE = 9;
const PRICE_MAX = 400;

// ── COMPONENTS ──────────────────────────────────────────────────────────────

function PriceRangeSlider({
  min, max, value, onChange,
}: {
  min: number; max: number; value: [number, number];
  onChange: (v: [number, number]) => void;
}) {
  const trackRef = useRef<HTMLDivElement>(null);
  const lowPct = ((value[0] - min) / (max - min)) * 100;
  const highPct = ((value[1] - min) / (max - min)) * 100;

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <div className="text-center">
          <p className="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Min</p>
          <p className="text-sm font-bold text-gray-800">${value[0]}</p>
        </div>
        <div className="w-px h-6 bg-gray-200" />
        <div className="text-center">
          <p className="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Max</p>
          <p className="text-sm font-bold text-gray-800">${value[1]}</p>
        </div>
      </div>
      <div ref={trackRef} className="relative h-1.5 bg-gray-100 rounded-full mx-2">
        <div
          className="absolute h-1.5 rounded-full"
          style={{
            left: `${lowPct}%`,
            right: `${100 - highPct}%`,
            background: "linear-gradient(90deg, #3b82f6, #06b6d4)",
          }}
        />
        {/* Low thumb */}
        <input
          type="range" min={min} max={max} value={value[0]}
          onChange={(e) => { const v = +e.target.value; if (v < value[1] - 10) onChange([v, value[1]]); }}
          className="absolute inset-0 w-full opacity-0 h-1.5 cursor-pointer"
          aria-label="Minimum price"
          style={{ zIndex: value[0] > max - 10 ? 5 : 3 }}
        />
        {/* High thumb */}
        <input
          type="range" min={min} max={max} value={value[1]}
          onChange={(e) => { const v = +e.target.value; if (v > value[0] + 10) onChange([value[0], v]); }}
          className="absolute inset-0 w-full opacity-0 h-1.5 cursor-pointer"
          aria-label="Maximum price"
          style={{ zIndex: 4 }}
        />
        <div
          className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-white border-2 border-blue-500 shadow-md pointer-events-none"
          style={{ left: `calc(${lowPct}% - 8px)` }}
        />
        <div
          className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-white border-2 border-blue-500 shadow-md pointer-events-none"
          style={{ left: `calc(${highPct}% - 8px)` }}
        />
      </div>
    </div>
  );
}

function SkeletonCard() {
  return (
    <div className="bg-white rounded-2xl overflow-hidden border border-gray-100 animate-pulse">
      <div className="bg-gray-200 h-56 w-full" />
      <div className="p-4 space-y-3">
        <div className="flex justify-between">
          <div className="bg-gray-200 h-4 w-20 rounded-full" />
          <div className="bg-gray-200 h-4 w-12 rounded-full" />
        </div>
        <div className="bg-gray-200 h-5 w-3/4 rounded" />
        <div className="bg-gray-200 h-3 w-1/2 rounded" />
        <div className="flex gap-2">
          <div className="bg-gray-200 h-6 w-14 rounded-full" />
          <div className="bg-gray-200 h-6 w-14 rounded-full" />
          <div className="bg-gray-200 h-6 w-14 rounded-full" />
        </div>
        <div className="flex justify-between items-center pt-2 border-t border-gray-100">
          <div className="bg-gray-200 h-6 w-16 rounded" />
          <div className="bg-gray-200 h-8 w-20 rounded-full" />
        </div>
      </div>
    </div>
  );
}

// ── MAIN PAGE ────────────────────────────────────────────────────────────────

export default function RentalsListingPage() {
  // Search bar state
  const [searchLocation, setSearchLocation] = useState<RentalLocation | "">("");
  const [checkIn, setCheckIn] = useState("");
  const [checkOut, setCheckOut] = useState("");
  const [guestCount, setGuestCount] = useState(2);
  const [guestOpen, setGuestOpen] = useState(false);
  const [searchTypeFilter, setSearchTypeFilter] = useState<PropertyType | "">("");

  // Filter state
  const [textSearch, setTextSearch] = useState("");
  const [activeType, setActiveType] = useState<PropertyType | "">("");
  const [activeLocation, setActiveLocation] = useState<RentalLocation | "">("");
  const [priceRange, setPriceRange] = useState<[number, number]>([0, PRICE_MAX]);
  const [minRating, setMinRating] = useState(0);
  const [activeAmenities, setActiveAmenities] = useState<string[]>([]);
  const [instantOnly, setInstantOnly] = useState(false);
  const [sortBy, setSortBy] = useState<SortOption>("popularity");

  // UI state
  const [page, setPage] = useState(1);
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [viewMode, setViewMode] = useState<"grid" | "list">("grid");
  const [loading, setLoading] = useState(false);
  const [showMap, setShowMap] = useState(false);
  const guestRef = useRef<HTMLDivElement>(null);

  const toggleAmenity = useCallback((key: string) => {
    setActiveAmenities((prev) =>
      prev.includes(key) ? prev.filter((a) => a !== key) : [...prev, key]
    );
    setPage(1);
  }, []);

  // Apply search bar params to filters
  const handleSearch = () => {
    if (searchLocation) setActiveLocation(searchLocation);
    if (searchTypeFilter) setActiveType(searchTypeFilter);
    setPage(1);
    setLoading(true);
    setTimeout(() => setLoading(false), 800);
  };

  const filtered = useMemo(() => {
    let result = rentalProperties.filter((p) => {
      const q = textSearch.toLowerCase();
      const matchSearch = !q || p.title.toLowerCase().includes(q) || p.location.city.toLowerCase().includes(q) || p.tags.some((t) => t.toLowerCase().includes(q));
      const matchType = !activeType || p.type === activeType;
      const matchLocation = !activeLocation || p.location.city === activeLocation;
      const matchPrice = p.pricePerNight >= priceRange[0] && p.pricePerNight <= priceRange[1];
      const matchRating = p.rating >= minRating;
      const matchAmenities = activeAmenities.every((a) => p.amenities.includes(a));
      const matchInstant = !instantOnly || p.instantBooking;
      return matchSearch && matchType && matchLocation && matchPrice && matchRating && matchAmenities && matchInstant;
    });

    result = [...result].sort((a, b) => {
      if (sortBy === "price-asc") return a.pricePerNight - b.pricePerNight;
      if (sortBy === "price-desc") return b.pricePerNight - a.pricePerNight;
      if (sortBy === "rating") return b.rating - a.rating;
      return b.popularity - a.popularity;
    });

    return result;
  }, [textSearch, activeType, activeLocation, priceRange, minRating, activeAmenities, instantOnly, sortBy]);

  const paginated = filtered.slice(0, page * ITEMS_PER_PAGE);
  const hasMore = paginated.length < filtered.length;

  const activeFilterCount = [
    activeType, activeLocation, priceRange[0] > 0, priceRange[1] < PRICE_MAX,
    minRating > 0, instantOnly, ...activeAmenities,
  ].filter(Boolean).length;

  const resetFilters = () => {
    setTextSearch(""); setActiveType(""); setActiveLocation("");
    setPriceRange([0, PRICE_MAX]); setMinRating(0); setActiveAmenities([]);
    setInstantOnly(false); setSortBy("popularity"); setPage(1);
  };

  const LoadMore = () => (
    <button
      onClick={() => {
        setLoading(true);
        setTimeout(() => { setPage((p) => p + 1); setLoading(false); }, 600);
      }}
      className="w-full py-3.5 border-2 border-dashed border-gray-200 rounded-xl text-sm text-gray-500 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 cursor-pointer font-medium"
    >
      Load more properties ({filtered.length - paginated.length} remaining)
    </button>
  );

  // ── FILTER PANEL CONTENT ──────────────────────────────────────────────────
  const FilterPanel = () => (
    <div className="space-y-6">

      {/* Search properties text */}
      <div className="relative">
        <MagnifyingGlass size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
        <input
          type="search"
          placeholder="Search by name or keyword..."
          value={textSearch}
          onChange={(e) => { setTextSearch(e.target.value); setPage(1); }}
          className="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all"
          aria-label="Search properties"
        />
      </div>

      <div className="h-px bg-gray-100" />

      {/* Price range */}
      <div>
        <h3 className="font-heading font-semibold text-xs text-gray-500 uppercase tracking-wider mb-3">Price per Night</h3>
        <PriceRangeSlider min={0} max={PRICE_MAX} value={priceRange} onChange={(v) => { setPriceRange(v); setPage(1); }} />
      </div>

      <div className="h-px bg-gray-100" />

      {/* Property type */}
      <div>
        <h3 className="font-heading font-semibold text-xs text-gray-500 uppercase tracking-wider mb-3">Property Type</h3>
        <div className="space-y-1.5">
          {propertyTypes.filter((t) => t.value !== "").map((t) => {
            const count = rentalProperties.filter((p) => p.type === t.value).length;
            return (
              <label key={t.value} className="flex items-center justify-between cursor-pointer group py-0.5">
                <div className="flex items-center gap-3">
                  <div
                    onClick={() => { setActiveType(activeType === t.value ? "" : t.value as PropertyType); setPage(1); }}
                    className={`w-4 h-4 rounded border-2 flex items-center justify-center cursor-pointer transition-all ${
                      activeType === t.value
                        ? "bg-blue-500 border-blue-500"
                        : "border-gray-300 hover:border-blue-400 bg-white"
                    }`}
                  >
                    {activeType === t.value && (
                      <svg width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4L3.5 6.5L9 1" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
                    )}
                  </div>
                  <span
                    onClick={() => { setActiveType(activeType === t.value ? "" : t.value as PropertyType); setPage(1); }}
                    className="text-sm text-gray-600 group-hover:text-gray-900 cursor-pointer transition-colors select-none"
                  >
                    {t.emoji} {t.label}
                  </span>
                </div>
                <span className="text-xs text-gray-350 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-100">{count}</span>
              </label>
            );
          })}
        </div>
      </div>

      <div className="h-px bg-gray-100" />

      {/* Location */}
      <div>
        <h3 className="font-heading font-semibold text-xs text-gray-500 uppercase tracking-wider mb-3">Location</h3>
        <div className="space-y-1.5">
          {rentalLocations.map((loc) => {
            const count = rentalProperties.filter((p) => p.location.city === loc).length;
            if (count === 0) return null;
            return (
              <label key={loc} className="flex items-center justify-between cursor-pointer group py-0.5">
                <div className="flex items-center gap-3">
                  <div
                    onClick={() => { setActiveLocation(activeLocation === loc ? "" : loc); setPage(1); }}
                    className={`w-4 h-4 rounded border-2 flex items-center justify-center cursor-pointer transition-all ${
                      activeLocation === loc
                        ? "bg-blue-500 border-blue-500"
                        : "border-gray-300 hover:border-blue-400 bg-white"
                    }`}
                  >
                    {activeLocation === loc && (
                      <svg width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4L3.5 6.5L9 1" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
                    )}
                  </div>
                  <span
                    onClick={() => { setActiveLocation(activeLocation === loc ? "" : loc); setPage(1); }}
                    className="text-sm text-gray-600 group-hover:text-gray-900 cursor-pointer transition-colors select-none"
                  >
                    {loc}
                  </span>
                </div>
                <span className="text-xs text-gray-350 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-100">{count}</span>
              </label>
            );
          })}
        </div>
      </div>

      <div className="h-px bg-gray-100" />

      {/* Amenities */}
      <div>
        <h3 className="font-heading font-semibold text-xs text-gray-500 uppercase tracking-wider mb-3">Amenities</h3>
        <div className="grid grid-cols-2 gap-2">
          {Object.entries(amenitiesMeta).map(([key, meta]) => (
            <label
              key={key}
              onClick={() => toggleAmenity(key)}
              className={`flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-all select-none text-xs ${
                activeAmenities.includes(key)
                  ? "border-blue-400 bg-blue-50 text-blue-700"
                  : "border-gray-100 bg-gray-50 text-gray-600 hover:border-gray-200 hover:bg-white"
              }`}
            >
              <span>{meta.emoji}</span>
              <span className="leading-tight font-medium">{meta.label}</span>
            </label>
          ))}
        </div>
      </div>

      <div className="h-px bg-gray-100" />

      {/* Rating */}
      <div>
        <h3 className="font-heading font-semibold text-xs text-gray-500 uppercase tracking-wider mb-3">Guest Rating</h3>
        <div className="flex flex-wrap gap-2">
          {[{ v: 0, label: "Any" }, { v: 4, label: "4+" }, { v: 4.5, label: "4.5+" }, { v: 4.8, label: "4.8+" }].map(({ v, label }) => (
            <button
              key={v}
              onClick={() => { setMinRating(v); setPage(1); }}
              className={`px-3 py-1.5 rounded-full text-xs font-medium border cursor-pointer transition-all ${
                minRating === v
                  ? "bg-blue-500 text-white border-blue-500 shadow-sm"
                  : "bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600"
              }`}
            >
              {v > 0 && <Star size={10} weight="fill" className="inline mr-0.5 text-amber-400" />}
              {label}
            </button>
          ))}
        </div>
      </div>

      <div className="h-px bg-gray-100" />

      {/* Instant Booking */}
      <div>
        <label className="flex items-center justify-between cursor-pointer group">
          <div>
            <p className="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
              <Lightning size={14} className="text-amber-500" weight="fill" />
              Instant Booking
            </p>
            <p className="text-xs text-gray-400 mt-0.5">Reserve without waiting for approval</p>
          </div>
          <button
            onClick={() => { setInstantOnly(!instantOnly); setPage(1); }}
            className={`relative w-11 h-6 rounded-full transition-colors duration-200 cursor-pointer flex-shrink-0 ${
              instantOnly ? "bg-blue-500" : "bg-gray-200"
            }`}
            role="switch"
            aria-checked={instantOnly}
          >
            <span
              className={`absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 ${
                instantOnly ? "translate-x-6" : "translate-x-1"
              }`}
            />
          </button>
        </label>
      </div>

      {activeFilterCount > 0 && (
        <button
          onClick={resetFilters}
          className="w-full py-2.5 text-sm text-gray-500 hover:text-blue-600 border border-dashed border-gray-300 hover:border-blue-300 rounded-xl transition-all cursor-pointer font-medium hover:bg-blue-50"
        >
          Clear all filters ({activeFilterCount})
        </button>
      )}
    </div>
  );

  return (
    <div className="min-h-screen bg-gray-50 font-sans" style={{ background: "hsl(210,16%,98%)" }}>
      <Navbar />

      {/* ── TOP SEARCH HEADER ─────────────────────────────────────────────── */}
      <div className="bg-white border-b border-gray-100 shadow-sm pt-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 py-6">
          {/* Hero text */}
          <div className="mb-5">
            <nav className="flex items-center gap-2 text-xs text-gray-400 mb-2" aria-label="Breadcrumb">
              <Link to="/" className="hover:text-blue-500 transition-colors">Home</Link>
              <span>/</span>
              <span className="text-gray-600 font-medium">Rentals</span>
            </nav>
            <h1 className="font-heading font-bold text-2xl md:text-3xl text-gray-900 leading-tight">
              Find Your Perfect Stay in Armenia
            </h1>
            <p className="text-gray-500 text-sm mt-1">
              {rentalProperties.length} handpicked properties — apartments, villas, cottages &amp; more
            </p>
          </div>

          {/* ── AIRBNB-STYLE SEARCH BAR ── */}
          <div className="flex flex-col lg:flex-row items-stretch gap-2 bg-white border-2 border-gray-200 rounded-2xl p-2 shadow-md hover:shadow-lg transition-shadow duration-300 max-w-4xl">

            {/* Location */}
            <div className="flex-1 flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100 group">
              <MapPinIcon size={20} className="text-blue-500 flex-shrink-0" />
              <div className="min-w-0 flex-1">
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Where</p>
                <select
                  value={searchLocation}
                  onChange={(e) => setSearchLocation(e.target.value as RentalLocation | "")}
                  className="w-full text-sm font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer"
                  aria-label="Select location"
                >
                  <option value="">Anywhere in Armenia</option>
                  {rentalLocations.map((loc) => <option key={loc} value={loc}>{loc}</option>)}
                </select>
              </div>
            </div>

            <div className="hidden lg:block w-px bg-gray-200 self-stretch my-1" />

            {/* Check-in */}
            <div className="flex-1 flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
              <CalendarBlank size={20} className="text-blue-500 flex-shrink-0" />
              <div className="min-w-0 flex-1">
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Check in</p>
                <input
                  type="date"
                  value={checkIn}
                  onChange={(e) => setCheckIn(e.target.value)}
                  className="w-full text-sm font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer"
                  aria-label="Check-in date"
                />
              </div>
            </div>

            <div className="hidden lg:block w-px bg-gray-200 self-stretch my-1" />

            {/* Check-out */}
            <div className="flex-1 flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
              <CalendarBlank size={20} className="text-blue-500 flex-shrink-0" />
              <div className="min-w-0 flex-1">
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Check out</p>
                <input
                  type="date"
                  value={checkOut}
                  onChange={(e) => setCheckOut(e.target.value)}
                  className="w-full text-sm font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer"
                  aria-label="Check-out date"
                />
              </div>
            </div>

            <div className="hidden lg:block w-px bg-gray-200 self-stretch my-1" />

            {/* Guests */}
            <div className="relative" ref={guestRef}>
              <div
                onClick={() => setGuestOpen(!guestOpen)}
                className="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100 min-w-[130px]"
              >
                <Users size={20} className="text-blue-500 flex-shrink-0" />
                <div>
                  <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Guests</p>
                  <p className="text-sm font-semibold text-gray-800">{guestCount} guest{guestCount !== 1 ? "s" : ""}</p>
                </div>
                <CaretDown size={14} className={`text-gray-400 ml-auto transition-transform ${guestOpen ? "rotate-180" : ""}`} />
              </div>
              {guestOpen && (
                <div className="absolute top-full right-0 mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl p-5 w-56 z-50">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-semibold text-gray-800">Guests</p>
                      <p className="text-xs text-gray-400">Adults &amp; children</p>
                    </div>
                    <div className="flex items-center gap-3">
                      <button
                        onClick={() => setGuestCount((n) => Math.max(1, n - 1))}
                        className="w-8 h-8 rounded-full border-2 border-gray-200 hover:border-blue-400 flex items-center justify-center transition-colors cursor-pointer text-gray-600 hover:text-blue-600"
                      >
                        <Minus size={12} weight="bold" />
                      </button>
                      <span className="text-base font-bold text-gray-800 w-5 text-center">{guestCount}</span>
                      <button
                        onClick={() => setGuestCount((n) => Math.min(20, n + 1))}
                        className="w-8 h-8 rounded-full border-2 border-gray-200 hover:border-blue-400 flex items-center justify-center transition-colors cursor-pointer text-gray-600 hover:text-blue-600"
                      >
                        <Plus size={12} weight="bold" />
                      </button>
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* Property type selector */}
            <div className="hidden lg:flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
              <div className="min-w-0">
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Type</p>
                <select
                  value={searchTypeFilter}
                  onChange={(e) => setSearchTypeFilter(e.target.value as PropertyType | "")}
                  className="text-sm font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer"
                  aria-label="Property type"
                >
                  {propertyTypes.map((t) => <option key={t.value} value={t.value}>{t.emoji} {t.label}</option>)}
                </select>
              </div>
            </div>

            {/* Search button */}
            <button
              onClick={handleSearch}
              className="flex-shrink-0 px-7 py-3 font-bold text-sm rounded-xl text-white cursor-pointer shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 justify-center self-stretch lg:self-auto"
              style={{ background: "linear-gradient(135deg, #3b82f6, #06b6d4)" }}
              aria-label="Search properties"
            >
              <MagnifyingGlass size={17} weight="bold" />
              <span>Search</span>
            </button>
          </div>
        </div>
      </div>

      {/* ── STICKY FILTER BAR ─────────────────────────────────────────────── */}
      <div className="sticky top-16 z-40 bg-white border-b border-gray-100 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="flex items-center gap-2 py-3 overflow-x-auto scrollbar-hide">

            {/* Type pills */}
            {propertyTypes.map((t) => (
              <button
                key={t.value}
                onClick={() => { setActiveType(activeType === t.value ? "" : t.value as PropertyType); setPage(1); }}
                className={`flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 cursor-pointer border ${
                  activeType === t.value || (t.value === "" && !activeType)
                    ? "text-white border-blue-500 shadow-sm"
                    : "bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:bg-gray-50"
                }`}
                style={
                  activeType === t.value || (t.value === "" && !activeType)
                    ? { background: "linear-gradient(135deg, #3b82f6, #06b6d4)", borderColor: "transparent" }
                    : {}
                }
                aria-pressed={activeType === t.value}
              >
                <span>{t.emoji}</span>
                <span>{t.label}</span>
              </button>
            ))}

            {/* Spacer */}
            <div className="flex-1 min-w-4" />

            {/* Instant booking pill */}
            <button
              onClick={() => { setInstantOnly(!instantOnly); setPage(1); }}
              className={`flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium border cursor-pointer transition-all ${
                instantOnly
                  ? "bg-amber-50 text-amber-600 border-amber-300"
                  : "bg-white text-gray-500 border-gray-200 hover:border-amber-300 hover:text-amber-600"
              }`}
            >
              <Lightning size={13} weight="fill" className="text-amber-500" />
              Instant Book
            </button>

            {/* Sort */}
            <div className="flex-shrink-0 flex items-center gap-2 border-l border-gray-200 pl-3">
              <select
                value={sortBy}
                onChange={(e) => { setSortBy(e.target.value as SortOption); setPage(1); }}
                className="text-sm text-gray-600 bg-white border border-gray-200 rounded-xl px-3 py-1.5 focus:outline-none cursor-pointer font-medium hover:border-blue-300 transition-colors"
                aria-label="Sort properties"
              >
                <option value="popularity">🔥 Popular</option>
                <option value="price-asc">Price ↑</option>
                <option value="price-desc">Price ↓</option>
                <option value="rating">⭐ Rating</option>
              </select>
            </div>

            {/* View toggle */}
            <div className="flex-shrink-0 flex items-center gap-1 bg-gray-100 rounded-xl p-1">
              <button
                onClick={() => setViewMode("grid")}
                className={`p-1.5 rounded-lg transition-all cursor-pointer ${viewMode === "grid" ? "bg-white shadow-sm text-blue-600" : "text-gray-400 hover:text-gray-600"}`}
                aria-label="Grid view"
                aria-pressed={viewMode === "grid"}
              >
                <GridFour size={16} weight={viewMode === "grid" ? "fill" : "regular"} />
              </button>
              <button
                onClick={() => setViewMode("list")}
                className={`p-1.5 rounded-lg transition-all cursor-pointer ${viewMode === "list" ? "bg-white shadow-sm text-blue-600" : "text-gray-400 hover:text-gray-600"}`}
                aria-label="List view"
                aria-pressed={viewMode === "list"}
              >
                <ListIcon size={16} weight={viewMode === "list" ? "bold" : "regular"} />
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* ── MAIN CONTENT ──────────────────────────────────────────────────── */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div className={`flex gap-8 items-start ${showMap ? "lg:grid lg:grid-cols-2" : ""}`}>

          {/* ── SIDEBAR ────────────────────────────────────────────────────── */}
          {!showMap && (
            <aside
              className="hidden xl:block w-72 flex-shrink-0 sticky top-36 self-start"
              aria-label="Property filters"
            >
              <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {/* Sidebar header */}
                <div className="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                  <h2 className="font-heading font-bold text-gray-800 text-sm flex items-center gap-2">
                    <Funnel size={16} className="text-blue-500" weight="fill" />
                    Filters
                    {activeFilterCount > 0 && (
                      <span className="w-5 h-5 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                        {activeFilterCount}
                      </span>
                    )}
                  </h2>
                  {activeFilterCount > 0 && (
                    <button onClick={resetFilters} className="text-xs text-blue-500 hover:text-blue-700 font-medium cursor-pointer hover:underline">
                      Clear all
                    </button>
                  )}
                </div>
                <div className="p-5 max-h-[calc(100vh-200px)] overflow-y-auto">
                  <FilterPanel />
                </div>
              </div>
            </aside>
          )}

          {/* ── LISTINGS ─────────────────────────────────────────────────── */}
          <div className="flex-1 min-w-0">

            {/* Results bar */}
            <div className="flex items-center justify-between mb-5 flex-wrap gap-3">
              <div>
                <p className="text-sm text-gray-600">
                  <span className="font-bold text-gray-900 text-base">{filtered.length}</span>
                  <span className="text-gray-500"> stay{filtered.length !== 1 ? "s" : ""}</span>
                  {activeLocation && (
                    <span className="text-blue-600 font-medium"> in {activeLocation}</span>
                  )}
                  {activeType && (
                    <span className="text-gray-500 font-normal">
                      {" "}({propertyTypes.find((t) => t.value === activeType)?.label}s)
                    </span>
                  )}
                </p>
                {checkIn && checkOut && (
                  <p className="text-xs text-gray-400 mt-0.5">
                    {checkIn} → {checkOut}, {guestCount} guest{guestCount !== 1 ? "s" : ""}
                  </p>
                )}
              </div>

              {/* Mobile filter button */}
              <button
                onClick={() => setDrawerOpen(true)}
                className="xl:hidden flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all cursor-pointer shadow-sm font-medium"
                aria-label="Open filters"
              >
                <SlidersHorizontal size={16} />
                Filters
                {activeFilterCount > 0 && (
                  <span className="w-5 h-5 bg-blue-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">
                    {activeFilterCount}
                  </span>
                )}
              </button>
            </div>

            {/* Active filter chips */}
            {activeFilterCount > 0 && (
              <div className="flex flex-wrap gap-2 mb-5">
                {activeType && (
                  <span className="flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full font-medium border border-blue-200">
                    {propertyTypes.find((t) => t.value === activeType)?.emoji}{" "}
                    {propertyTypes.find((t) => t.value === activeType)?.label}
                    <button onClick={() => setActiveType("")} className="ml-0.5 hover:text-blue-900 cursor-pointer"><X size={11} /></button>
                  </span>
                )}
                {activeLocation && (
                  <span className="flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full font-medium border border-blue-200">
                    📍 {activeLocation}
                    <button onClick={() => setActiveLocation("")} className="ml-0.5 hover:text-blue-900 cursor-pointer"><X size={11} /></button>
                  </span>
                )}
                {(priceRange[0] > 0 || priceRange[1] < PRICE_MAX) && (
                  <span className="flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full font-medium border border-blue-200">
                    💰 ${priceRange[0]}–${priceRange[1]}
                    <button onClick={() => setPriceRange([0, PRICE_MAX])} className="ml-0.5 cursor-pointer"><X size={11} /></button>
                  </span>
                )}
                {minRating > 0 && (
                  <span className="flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full font-medium border border-blue-200">
                    ⭐ {minRating}+
                    <button onClick={() => setMinRating(0)} className="ml-0.5 cursor-pointer"><X size={11} /></button>
                  </span>
                )}
                {instantOnly && (
                  <span className="flex items-center gap-1.5 text-xs bg-amber-50 text-amber-700 px-3 py-1.5 rounded-full font-medium border border-amber-200">
                    ⚡ Instant only
                    <button onClick={() => setInstantOnly(false)} className="ml-0.5 cursor-pointer"><X size={11} /></button>
                  </span>
                )}
                {activeAmenities.map((a) => (
                  <span key={a} className="flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full font-medium border border-blue-200">
                    {amenitiesMeta[a]?.emoji} {amenitiesMeta[a]?.label}
                    <button onClick={() => toggleAmenity(a)} className="ml-0.5 cursor-pointer"><X size={11} /></button>
                  </span>
                ))}
                <button
                  onClick={resetFilters}
                  className="text-xs text-gray-400 hover:text-blue-600 px-3 py-1.5 rounded-full border border-dashed border-gray-300 hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer"
                >
                  Clear all
                </button>
              </div>
            )}

            {/* ── RESULTS ── */}
            {loading ? (
              <div className={`grid gap-5 ${viewMode === "grid" ? "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" : "grid-cols-1"}`}>
                {Array.from({ length: 6 }).map((_, i) => <SkeletonCard key={i} />)}
              </div>
            ) : filtered.length === 0 ? (
              /* Empty state */
              <div className="text-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div className="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-5">
                  <span className="text-4xl">🏠</span>
                </div>
                <h3 className="font-heading font-bold text-gray-800 text-xl mb-2">No properties found</h3>
                <p className="text-gray-500 text-sm mb-6 max-w-xs mx-auto">
                  Try adjusting your filters or search a different location in Armenia.
                </p>
                <button
                  onClick={resetFilters}
                  className="px-6 py-2.5 text-sm text-white font-semibold rounded-xl cursor-pointer transition-all duration-200 shadow-md hover:shadow-lg"
                  style={{ background: "linear-gradient(135deg, #3b82f6, #06b6d4)" }}
                >
                  Clear all filters
                </button>
              </div>
            ) : (
              <>
                <div className={`grid gap-5 ${
                  viewMode === "grid"
                    ? "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3"
                    : "grid-cols-1"
                }`}>
                  {paginated.map((property) => (
                    <PropertyCard
                      key={property.slug}
                      property={property}
                      viewMode={viewMode}
                    />
                  ))}
                </div>

                {/* Load more */}
                {hasMore && (
                  <div className="mt-10">
                    <LoadMore />
                  </div>
                )}

                {/* Count */}
                <p className="text-center text-xs text-gray-400 mt-4">
                  Showing {Math.min(paginated.length, filtered.length)} of {filtered.length} properties
                </p>
              </>
            )}
          </div>
        </div>
      </main>

      {/* ── MAP VIEW PLACEHOLDER ─────────────────────────────────────────── */}
      {showMap && (
        <div className="fixed inset-0 top-16 z-30 bg-white flex items-center justify-center" style={{ top: "128px" }}>
          <div
            className="w-full h-full flex flex-col items-center justify-center gap-4 text-gray-400"
            style={{ background: "linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%)" }}
          >
            <MapPinIcon size={48} className="text-blue-300" weight="light" />
            <p className="text-lg font-semibold text-blue-400">Interactive Map View</p>
            <p className="text-sm text-blue-300 text-center max-w-xs">
              Leaflet / Google Maps integration will render {filtered.length} property pins here
            </p>
            <button
              onClick={() => setShowMap(false)}
              className="mt-2 px-5 py-2 bg-white text-blue-600 rounded-xl border border-blue-200 text-sm font-semibold shadow cursor-pointer hover:bg-blue-50 transition-colors"
            >
              Back to list
            </button>
          </div>
        </div>
      )}

      {/* ── MOBILE BOTTOM BAR ────────────────────────────────────────────── */}
      <div className="xl:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-100 shadow-xl px-4 py-3 flex items-center gap-3">
        <button
          onClick={() => setDrawerOpen(true)}
          className="flex-1 flex items-center justify-center gap-2 py-3 border-2 border-gray-200 rounded-xl text-sm text-gray-700 font-semibold cursor-pointer hover:border-blue-400 hover:text-blue-600 transition-all bg-gray-50 hover:bg-blue-50"
        >
          <SlidersHorizontal size={17} />
          Filters
          {activeFilterCount > 0 && (
            <span className="w-5 h-5 bg-blue-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">{activeFilterCount}</span>
          )}
        </button>
        <button
          onClick={() => setShowMap(!showMap)}
          className="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-sm text-white font-semibold cursor-pointer transition-all shadow-md"
          style={{ background: "linear-gradient(135deg, #3b82f6, #06b6d4)" }}
        >
          <MapTrifold size={17} weight="bold" />
          {showMap ? "Show List" : "Show Map"}
        </button>
      </div>
      <div className="h-20 xl:hidden" />

      {/* ── MOBILE FILTER DRAWER ─────────────────────────────────────────── */}
      {drawerOpen && (
        <div className="fixed inset-0 z-50 flex" role="dialog" aria-modal="true" aria-label="Filters">
          <div
            className="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onClick={() => setDrawerOpen(false)}
          />
          <div className="relative ml-auto w-[340px] max-w-full bg-white flex flex-col h-full shadow-2xl">
            <div className="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
              <h2 className="font-heading font-bold text-gray-800 flex items-center gap-2">
                <Funnel size={18} className="text-blue-500" weight="fill" />
                Filters
                {activeFilterCount > 0 && (
                  <span className="w-5 h-5 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                    {activeFilterCount}
                  </span>
                )}
              </h2>
              <button onClick={() => setDrawerOpen(false)} className="p-2 text-gray-500 hover:text-gray-800 cursor-pointer rounded-lg hover:bg-gray-100 transition-colors">
                <X size={20} />
              </button>
            </div>
            <div className="flex-1 overflow-y-auto px-5 py-5">
              <FilterPanel />
            </div>
            <div className="px-5 py-4 border-t border-gray-100 bg-white">
              <button
                onClick={() => setDrawerOpen(false)}
                className="w-full py-3.5 text-white font-bold rounded-xl cursor-pointer text-sm shadow-md transition-all hover:shadow-lg active:scale-98"
                style={{ background: "linear-gradient(135deg, #3b82f6, #06b6d4)" }}
              >
                Show {filtered.length} propert{filtered.length !== 1 ? "ies" : "y"}
              </button>
            </div>
          </div>
        </div>
      )}

      <Footer />
    </div>
  );
}
