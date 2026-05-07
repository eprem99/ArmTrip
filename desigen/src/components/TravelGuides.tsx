import React, { useRef } from "react";
import { motion, useInView } from "framer-motion";
import { ArrowRight, Clock } from "@phosphor-icons/react";

const guides = [
  {
    id: 1,
    title: "The Ultimate 7-Day Armenia Itinerary",
    excerpt: "From Yerevan's vibrant streets to the serene shores of Lake Sevan — plan your perfect week in Armenia with our expert guide.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_2.png",
    alt: "Yerevan city skyline with Mount Ararat",
    readTime: "8 min read",
    category: "Itineraries",
  },
  {
    id: 2,
    title: "Best Hiking Trails in the Armenian Highlands",
    excerpt: "Discover the most breathtaking mountain trails, from beginner-friendly walks to challenging summit routes.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_7.png",
    alt: "Hikers on Armenian mountain trail",
    readTime: "6 min read",
    category: "Adventure",
  },
  {
    id: 3,
    title: "Armenian Cuisine: A Food Lover's Guide",
    excerpt: "Explore the rich flavors of Armenian food culture — from traditional lavash to the legendary khorovats barbecue.",
    image: "https://c.animaapp.com/mmoxd21v67mhk5/img/ai_5.png",
    alt: "Cozy stone cottage in Armenian mountains",
    readTime: "5 min read",
    category: "Food & Culture",
  },
];

export default function TravelGuides() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });

  return (
    <section id="guides" className="py-24 px-8 bg-background" aria-labelledby="guides-heading">
      <div className="max-w-7xl mx-auto">
        <motion.div
          ref={ref}
          initial={{ opacity: 0, y: 24 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, ease: "easeOut" }}
          className="text-center mb-12"
        >
          <span className="text-primary font-medium text-sm uppercase tracking-widest">Travel Guides</span>
          <h2 id="guides-heading" className="font-heading text-h1 font-medium text-foreground mt-2 mb-2">
            Plan Your Perfect Armenia Trip
          </h2>
          <p className="text-muted-foreground text-body max-w-xl mx-auto">
            Expert travel guides to help you make the most of your Armenian adventure.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {guides.map((guide, i) => (
            <motion.div
              key={guide.id}
              initial={{ opacity: 0, y: 32 }}
              animate={inView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: i * 0.12, ease: "easeOut" }}
            >
              <BlogCard guide={guide} />
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}

function BlogCard({ guide }: { guide: typeof guides[0] }) {
  return (
    <div
      className="group rounded-xl overflow-hidden border border-border bg-card cursor-pointer flex flex-col h-full"
      role="article"
      tabIndex={0}
      aria-label={`Travel guide: ${guide.title}`}
    >
      <div className="relative h-52 overflow-hidden flex-shrink-0">
        <img
          src={guide.image}
          alt={guide.alt}
          loading="lazy"
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350 ease-in-out"
        />
        <div className="absolute top-3 left-3">
          <span className="bg-primary text-primary-foreground text-xs font-normal px-3 py-1 rounded-full">
            {guide.category}
          </span>
        </div>
      </div>
      <div className="p-5 flex flex-col flex-1">
        <div className="flex items-center gap-1 text-muted-foreground text-xs mb-3">
          <Clock size={14} weight="regular" />
          <span>{guide.readTime}</span>
        </div>
        <h3 className="font-heading text-base font-medium text-foreground group-hover:text-primary transition-colors duration-250 mb-2 leading-snug">
          {guide.title}
        </h3>
        <p className="text-muted-foreground text-sm leading-relaxed flex-1 mb-4">{guide.excerpt}</p>
        <button className="inline-flex items-center gap-1 text-primary text-sm font-normal hover:gap-2 transition-all duration-250 cursor-pointer self-start">
          Read more <ArrowRight size={14} weight="regular" />
        </button>
      </div>
    </div>
  );
}