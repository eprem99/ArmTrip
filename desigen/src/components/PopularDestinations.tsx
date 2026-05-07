import React, { useRef } from "react";
import { motion, useInView } from "framer-motion";
import { ArrowRight } from "@phosphor-icons/react";

const destinations = [
  {
    id: 1,
    name: "Yerevan",
    description: "The vibrant capital city with stunning views of Mount Ararat and a rich cultural heritage.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png",
    alt: "Yerevan city skyline with Mount Ararat",
  },
  {
    id: 2,
    name: "Lake Sevan",
    description: "One of the largest freshwater high-altitude lakes in the world, surrounded by breathtaking scenery.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png",
    alt: "Lake Sevan shoreline panorama",
  },
  {
    id: 3,
    name: "Dilijan",
    description: "Armenia's little Switzerland — lush forests, fresh air, and charming architecture.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_4.png",
    alt: "Dilijan forest trail in Armenia",
  },
  {
    id: 4,
    name: "Garni & Geghard",
    description: "Ancient pagan temple and medieval monastery carved into the rock — a UNESCO World Heritage Site.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png",
    alt: "Garni Temple in Armenia",
  },
];

export default function PopularDestinations() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });

  return (
    <section id="destinations" className="py-24 px-8 bg-background" aria-labelledby="destinations-heading">
      <div className="max-w-7xl mx-auto">
        <motion.div
          ref={ref}
          initial={{ opacity: 0, y: 24 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, ease: "easeOut" }}
          className="text-center mb-12"
        >
          <span className="text-primary font-medium text-sm uppercase tracking-widest">Explore</span>
          <h2 id="destinations-heading" className="font-heading text-h1 font-medium text-foreground mt-2 mb-4">
            Popular Destinations
          </h2>
          <p className="text-muted-foreground text-body max-w-xl mx-auto">
            From ancient monasteries to alpine lakes, discover the most beloved corners of Armenia.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {destinations.map((dest, i) => (
            <motion.div
              key={dest.id}
              initial={{ opacity: 0, y: 32 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: i * 0.1, ease: "easeOut" }}
            >
              <DestinationCard dest={dest} />
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}

function DestinationCard({ dest }: { dest: typeof destinations[0] }) {
  return (
    <div
      className="group rounded-xl overflow-hidden border border-border bg-card cursor-pointer"
      role="article"
      tabIndex={0}
      aria-label={`Destination: ${dest.name}`}
    >
      <div className="img-zoom-container h-52 overflow-hidden">
        <img
          src={dest.image}
          alt={dest.alt}
          loading="lazy"
          className="img-zoom w-full h-full object-cover group-hover:scale-105 transition-transform duration-350 ease-in-out"
        />
      </div>
      <div className="p-5">
        <h3 className="font-heading text-h3 font-medium text-foreground group-hover:text-primary transition-colors duration-250 mb-2">
          {dest.name}
        </h3>
        <p className="text-muted-foreground text-sm leading-relaxed mb-4">{dest.description}</p>
        <span className="inline-flex items-center gap-1 text-primary text-sm font-normal group-hover:gap-2 transition-all duration-250">
          Explore <ArrowRight size={16} weight="regular" />
        </span>
      </div>
    </div>
  );
}