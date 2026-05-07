import React, { useRef, useState } from "react";
import { motion, useInView } from "framer-motion";
import { Star, WifiX, ForkKnife, Mountains, ArrowLeft, ArrowRight } from "@phosphor-icons/react";

const accommodations = [
  {
    id: 1,
    name: "Mountain View Retreat",
    location: "Dilijan, Armenia",
    price: 85,
    rating: 4.9,
    reviews: 128,
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png",
    alt: "Cozy stone cottage in Armenian mountains",
    amenities: ["wifi", "kitchen", "mountain"],
  },
  {
    id: 2,
    name: "Sevan Lakeside Villa",
    location: "Lake Sevan, Armenia",
    price: 120,
    rating: 4.8,
    reviews: 94,
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png",
    alt: "Lake Sevan shoreline panorama",
    amenities: ["wifi", "kitchen"],
  },
  {
    id: 3,
    name: "Yerevan Heritage Suite",
    location: "Yerevan, Armenia",
    price: 95,
    rating: 4.7,
    reviews: 211,
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png",
    alt: "Yerevan city skyline with Mount Ararat",
    amenities: ["wifi", "mountain"],
  },
  {
    id: 4,
    name: "Forest Cabin Escape",
    location: "Dilijan Forest, Armenia",
    price: 70,
    rating: 4.9,
    reviews: 67,
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_4.png",
    alt: "Dilijan forest trail in Armenia",
    amenities: ["wifi", "kitchen", "mountain"],
  },
  {
    id: 5,
    name: "Ararat Valley Guesthouse",
    location: "Ararat Valley, Armenia",
    price: 60,
    rating: 4.6,
    reviews: 45,
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png",
    alt: "Hikers on Armenian mountain trail",
    amenities: ["wifi", "mountain"],
  },
];

const amenityIcons: Record<string, React.ReactNode> = {
  wifi: <WifiX size={16} weight="regular" />,
  kitchen: <ForkKnife size={16} weight="regular" />,
  mountain: <Mountains size={16} weight="regular" />,
};

const amenityLabels: Record<string, string> = {
  wifi: "Wi-Fi",
  kitchen: "Kitchen",
  mountain: "Mountain View",
};

export default function FeaturedAccommodations() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });
  const [current, setCurrent] = useState(0);
  const visible = 3;
  const max = accommodations.length - visible;

  const prev = () => setCurrent((c) => Math.max(0, c - 1));
  const next = () => setCurrent((c) => Math.min(max, c + 1));

  return (
    <section id="stays" className="py-24 px-8 bg-gray-50" aria-labelledby="stays-heading">
      <div className="max-w-7xl mx-auto">
        <motion.div
          ref={ref}
          initial={{ opacity: 0, y: 24 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, ease: "easeOut" }}
          className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4"
        >
          <div>
            <span className="text-primary font-medium text-sm uppercase tracking-widest">Book Your Stay</span>
            <h2 id="stays-heading" className="font-heading text-h1 font-medium text-foreground mt-2 mb-2">
              Featured Accommodations
            </h2>
            <p className="text-muted-foreground text-body max-w-lg">
              Handpicked stays that blend comfort with authentic Armenian hospitality.
            </p>
          </div>
          <div className="flex gap-2">
            <button
              onClick={prev}
              disabled={current === 0}
              className="p-3 rounded-full border border-border bg-background text-foreground hover:bg-primary hover:text-primary-foreground hover:border-primary disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-250 cursor-pointer"
              aria-label="Previous accommodations"
            >
              <ArrowLeft size={20} weight="regular" />
            </button>
            <button
              onClick={next}
              disabled={current >= max}
              className="p-3 rounded-full border border-border bg-background text-foreground hover:bg-primary hover:text-primary-foreground hover:border-primary disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-250 cursor-pointer"
              aria-label="Next accommodations"
            >
              <ArrowRight size={20} weight="regular" />
            </button>
          </div>
        </motion.div>

        {/* Carousel */}
        <div className="overflow-hidden">
          <motion.div
            className="flex gap-6"
            animate={{ x: `calc(-${current} * (100% / ${visible} + 24px / ${visible}))` }}
            transition={{ duration: 0.4, ease: "easeInOut" }}
          >
            {accommodations.map((acc, i) => (
              <motion.div
                key={acc.id}
                className="flex-shrink-0 w-full md:w-[calc(33.333%-16px)]"
                initial={{ opacity: 0, y: 32 }}
                animate={inView ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.6, delay: i * 0.1, ease: "easeOut" }}
              >
                <AccommodationCard acc={acc} />
              </motion.div>
            ))}
          </motion.div>
        </div>
      </div>
    </section>
  );
}

function AccommodationCard({ acc }: { acc: typeof accommodations[0] }) {
  return (
    <div
      className="group rounded-xl overflow-hidden border border-border bg-card cursor-pointer relative"
      role="article"
      tabIndex={0}
      aria-label={`Accommodation: ${acc.name}`}
    >
      <div className="relative h-56 overflow-hidden">
        <img
          src={acc.image}
          alt={acc.alt}
          loading="lazy"
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350 ease-in-out"
        />
        {/* Gradient overlay on hover */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true" />
        <div className="absolute top-3 right-3 bg-background/90 rounded-full px-3 py-1 text-sm font-medium text-foreground">
          ${acc.price}<span className="text-muted-foreground font-normal">/night</span>
        </div>
      </div>
      <div className="p-5 group-hover:-translate-y-1 transition-transform duration-300">
        <div className="flex items-start justify-between gap-2 mb-1">
          <h3 className="font-heading text-base font-medium text-foreground">{acc.name}</h3>
          <div className="flex items-center gap-1 flex-shrink-0">
            <Star size={14} weight="fill" className="text-warning" />
            <span className="text-sm text-foreground font-medium">{acc.rating}</span>
            <span className="text-xs text-muted-foreground">({acc.reviews})</span>
          </div>
        </div>
        <p className="text-muted-foreground text-sm mb-3">{acc.location}</p>
        <div className="flex flex-wrap gap-2">
          {acc.amenities.map((a) => (
            <span
              key={a}
              className="inline-flex items-center gap-1 text-xs text-secondary bg-secondary/10 px-2 py-1 rounded-full"
            >
              {amenityIcons[a]}
              {amenityLabels[a]}
            </span>
          ))}
        </div>
      </div>
    </div>
  );
}