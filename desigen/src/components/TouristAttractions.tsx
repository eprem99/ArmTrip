import React, { useRef } from "react";
import { motion, useInView } from "framer-motion";
import { ArrowRight } from "@phosphor-icons/react";

const attractions = [
  {
    id: 1,
    name: "Garni Temple",
    description: "The only standing Greco-Roman colonnaded building in Armenia, dating back to the 1st century AD.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png",
    alt: "Garni Temple in Armenia",
  },
  {
    id: 2,
    name: "Mount Ararat",
    description: "The iconic snow-capped volcanic massif — the eternal symbol of Armenian identity and culture.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png",
    alt: "Yerevan city skyline with Mount Ararat",
  },
  {
    id: 3,
    name: "Lake Sevan",
    description: "One of the world's largest high-altitude freshwater lakes, a natural wonder of the Caucasus.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png",
    alt: "Lake Sevan shoreline panorama",
  },
  {
    id: 4,
    name: "Tatev Monastery",
    description: "A medieval monastery perched on a basalt plateau, accessible via the world's longest cable car.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png",
    alt: "Hikers on Armenian mountain trail",
  },
];

export default function TouristAttractions() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });

  return (
    <section id="attractions" className="py-24 px-8 bg-background" aria-labelledby="attractions-heading">
      <div className="max-w-7xl mx-auto">
        <motion.div
          ref={ref}
          initial={{ opacity: 0, y: 24 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, ease: "easeOut" }}
          className="text-center mb-12"
        >
          <span className="text-primary font-medium text-sm uppercase tracking-widest">Must-See</span>
          <h2 id="attractions-heading" className="font-heading text-h1 font-medium text-foreground mt-2 mb-4">
            Tourist Attractions
          </h2>
          <p className="text-muted-foreground text-body max-w-xl mx-auto">
            Explore Armenia's most iconic cultural and historical landmarks.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {attractions.map((attr, i) => (
            <motion.div
              key={attr.id}
              initial={{ opacity: 0, y: 32 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: i * 0.1, ease: "easeOut" }}
            >
              <AttractionCard attr={attr} />
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}

function AttractionCard({ attr }: { attr: typeof attractions[0] }) {
  return (
    <div
      className="group rounded-xl overflow-hidden border border-border bg-card"
      role="article"
      aria-label={`Attraction: ${attr.name}`}
    >
      <div className="relative h-52 overflow-hidden">
        <img
          src={attr.image}
          alt={attr.alt}
          loading="lazy"
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350 ease-in-out"
        />
        <div
          className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
          aria-hidden="true"
        />
        <div className="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
          <p className="text-white text-xs leading-relaxed">{attr.description}</p>
        </div>
      </div>
      <div className="p-4">
        <h3 className="font-heading text-base font-medium text-foreground mb-1">{attr.name}</h3>
        <p className="text-muted-foreground text-sm line-clamp-2 mb-3">{attr.description}</p>
        <button className="inline-flex items-center gap-1 text-primary text-sm font-normal hover:gap-2 transition-all duration-250 cursor-pointer">
          View more <ArrowRight size={14} weight="regular" />
        </button>
      </div>
    </div>
  );
}