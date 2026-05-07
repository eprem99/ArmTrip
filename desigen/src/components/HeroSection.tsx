import React, { useRef, useEffect, useState } from "react";
import { motion } from "framer-motion";
import { MagnifyingGlass, MapPin, CalendarBlank, Users } from "@phosphor-icons/react";
import { Button } from "@/components/ui/button";

export default function HeroSection() {
  const videoRef = useRef<HTMLVideoElement>(null);
  const [checkIn, setCheckIn] = useState("");
  const [checkOut, setCheckOut] = useState("");
  const [location, setLocation] = useState("");
  const [guests, setGuests] = useState("1");

  const handleExplore = () => {
    const el = document.getElementById("destinations");
    if (el) el.scrollIntoView({ behavior: "smooth" });
  };

  return (
    <section
      id="hero"
      className="relative w-full h-screen min-h-[600px] flex flex-col items-center justify-center overflow-hidden"
      aria-label="Hero section"
    >
      {/* Background Video */}
      <motion.video
        alt="Armenian mountain landscape at sunrise"
        src="https://c.animaapp.com/mmoxd21v67mhk5/img/ai_1.mp4"
        poster="https://c.animaapp.com/mmoxd21v67mhk5/img/ai_1-poster.png"
        className="absolute inset-0 w-full h-full object-cover"
        autoPlay
        loop
        muted
        playsInline
        initial={{ opacity: 0, scale: 1.1 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 1.5, ease: "easeOut" }}
      />

      {/* Gradient overlay */}
      <div
        className="absolute inset-0"
        style={{ background: "linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.65) 100%)" }}
        aria-hidden="true"
      />

      {/* Content */}
      <div className="relative z-10 flex flex-col items-center text-center px-8 max-w-4xl mx-auto w-full">
        <motion.h1
          className="font-heading text-white text-4xl md:text-5xl lg:text-hero font-medium leading-tight mb-4"
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.3, ease: "easeOut" }}
        >
          Discover Armenia –<br />Land of Mountains and Legends
        </motion.h1>

        <motion.p
          className="text-white/90 text-body-lg font-light mb-8 max-w-xl"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.5, ease: "easeOut" }}
        >
          Find unique stays and unforgettable experiences.
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.7, ease: "easeOut" }}
          className="mb-8"
        >
          <Button
            onClick={handleExplore}
            className="bg-primary text-primary-foreground hover:bg-primary-hover font-normal text-base px-8 py-3 cursor-pointer transition-transform duration-250 hover:scale-[1.03]"
            size="lg"
          >
            Start Exploring
          </Button>
        </motion.div>

        {/* Search Bar */}
        <motion.div
          initial={{ opacity: 0, y: 24 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.9, ease: "easeOut" }}
          className="w-full max-w-3xl"
        >
          <div className="bg-white/95 rounded-xl p-3 flex flex-col md:flex-row gap-2 items-stretch md:items-end border border-white/30">
            {/* Location */}
            <div className="flex items-center gap-2 flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-border">
              <MapPin size={20} weight="fill" className="text-primary flex-shrink-0" />
              <input
                type="text"
                placeholder="Where to?"
                value={location}
                onChange={(e) => setLocation(e.target.value)}
                className="flex-1 bg-transparent text-foreground text-sm outline-none placeholder:text-gray-400 font-normal"
                aria-label="Location"
              />
            </div>
            {/* Check-in */}
            <div className="flex items-center gap-2 flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-border">
              <CalendarBlank size={20} weight="regular" className="text-primary flex-shrink-0" />
              <input
                type="date"
                value={checkIn}
                onChange={(e) => setCheckIn(e.target.value)}
                className="flex-1 bg-transparent text-foreground text-sm outline-none font-normal cursor-pointer"
                aria-label="Check-in date"
              />
            </div>
            {/* Check-out */}
            <div className="flex items-center gap-2 flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-border">
              <CalendarBlank size={20} weight="regular" className="text-primary flex-shrink-0" />
              <input
                type="date"
                value={checkOut}
                onChange={(e) => setCheckOut(e.target.value)}
                className="flex-1 bg-transparent text-foreground text-sm outline-none font-normal cursor-pointer"
                aria-label="Check-out date"
              />
            </div>
            {/* Guests */}
            <div className="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-border min-w-[100px]">
              <Users size={20} weight="regular" className="text-primary flex-shrink-0" />
              <select
                value={guests}
                onChange={(e) => setGuests(e.target.value)}
                className="flex-1 bg-transparent text-foreground text-sm outline-none font-normal cursor-pointer"
                aria-label="Number of guests"
              >
                {[1, 2, 3, 4, 5, 6].map((n) => (
                  <option key={n} value={n}>{n} Guest{n > 1 ? "s" : ""}</option>
                ))}
              </select>
            </div>
            {/* Search button */}
            <Button
              className="bg-primary text-primary-foreground hover:bg-primary-hover font-normal text-sm px-5 cursor-pointer flex items-center gap-2 flex-shrink-0 self-stretch md:self-auto"
              style={{ minHeight: "42px" }}
              aria-label="Search"
            >
              <MagnifyingGlass size={18} weight="bold" />
              <span>Search</span>
            </Button>
          </div>
        </motion.div>
      </div>

      {/* Bottom fade */}
      <div
        className="absolute bottom-0 left-0 right-0 h-24 pointer-events-none"
        style={{ background: "linear-gradient(to bottom, transparent, rgba(255,255,255,0.15))" }}
        aria-hidden="true"
      />
    </section>
  );
}