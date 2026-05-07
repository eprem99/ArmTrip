import React, { useRef } from "react";
import { motion, useInView } from "framer-motion";
import { Footprints, Wine, Coffee, Camera, Bicycle, Campfire } from "@phosphor-icons/react";

const experiences = [
  {
    id: 1,
    icon: <Footprints size={36} weight="regular" />,
    title: "Mountain Hiking",
    description: "Trek through Armenia's stunning mountain trails with expert local guides.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png",
    alt: "Hikers on Armenian mountain trail",
  },
  {
    id: 2,
    icon: <Wine size={36} weight="regular" />,
    title: "Wine Tours",
    description: "Discover Armenia's ancient winemaking tradition in the Ararat Valley vineyards.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png",
    alt: "Yerevan city skyline with Mount Ararat",
  },
  {
    id: 3,
    icon: <Coffee size={36} weight="regular" />,
    title: "Food Tours",
    description: "Savor authentic Armenian cuisine — from lavash to khorovats — on guided food walks.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png",
    alt: "Cozy stone cottage in Armenian mountains",
  },
  {
    id: 4,
    icon: <Camera size={36} weight="regular" />,
    title: "Cultural Immersion",
    description: "Immerse yourself in Armenian history, art, and traditions with local cultural experts.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_6.png",
    alt: "Garni Temple in Armenia",
  },
  {
    id: 5,
    icon: <Bicycle size={36} weight="regular" />,
    title: "Cycling Adventures",
    description: "Explore scenic routes through valleys and villages on two wheels.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_4.png",
    alt: "Dilijan forest trail in Armenia",
  },
  {
    id: 6,
    icon: <Campfire size={36} weight="regular" />,
    title: "Camping & Stargazing",
    description: "Spend nights under Armenia's crystal-clear skies in remote mountain camps.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_3.png",
    alt: "Lake Sevan shoreline panorama",
  },
];

export default function ExperiencesSection() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });

  return (
    <section id="experiences" className="py-24 px-8 bg-gray-50" aria-labelledby="experiences-heading">
      <div className="max-w-7xl mx-auto">
        <motion.div
          ref={ref}
          initial={{ opacity: 0, y: 24 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, ease: "easeOut" }}
          className="text-center mb-12"
        >
          <span className="text-primary font-medium text-sm uppercase tracking-widest">Activities</span>
          <h2 id="experiences-heading" className="font-heading text-h1 font-medium text-foreground mt-2 mb-4">
            Experiences & Activities
          </h2>
          <p className="text-muted-foreground text-body max-w-xl mx-auto">
            From mountain hikes to wine tours — craft your perfect Armenian adventure.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {experiences.map((exp, i) => (
            <motion.div
              key={exp.id}
              initial={{ opacity: 0, y: 32 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: i * 0.1, ease: "easeOut" }}
            >
              <ExperienceCard exp={exp} />
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}

function ExperienceCard({ exp }: { exp: typeof experiences[0] }) {
  return (
    <div
      className="group rounded-xl overflow-hidden border border-border bg-card cursor-pointer"
      role="article"
      tabIndex={0}
      aria-label={`Experience: ${exp.title}`}
    >
      <div className="relative h-44 overflow-hidden">
        <img
          src={exp.image}
          alt={exp.alt}
          loading="lazy"
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350 ease-in-out"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" aria-hidden="true" />
      </div>
      <div className="p-5">
        <div className="flex items-center gap-3 mb-3">
          <span className="text-primary">{exp.icon}</span>
          <h3 className="font-heading text-base font-medium text-foreground group-hover:text-primary transition-colors duration-250">
            {exp.title}
          </h3>
        </div>
        <p className="text-muted-foreground text-sm leading-relaxed">{exp.description}</p>
      </div>
    </div>
  );
}