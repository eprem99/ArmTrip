import React, { useRef } from "react";
import { motion, useInView } from "framer-motion";
import { Tree, Buildings, HandHeart, BowlFood } from "@phosphor-icons/react";

const reasons = [
  {
    id: 1,
    icon: <Tree size={40} weight="regular" />,
    title: "Pristine Nature",
    description: "From alpine lakes to ancient forests and volcanic peaks — Armenia's landscapes are truly awe-inspiring.",
  },
  {
    id: 2,
    icon: <Buildings size={40} weight="regular" />,
    title: "Rich Heritage",
    description: "One of the world's oldest civilizations with thousands of years of history, art, and architecture.",
  },
  {
    id: 3,
    icon: <HandHeart size={40} weight="regular" />,
    title: "Warm Hospitality",
    description: "Armenians are renowned for their legendary hospitality — guests are treated like family.",
  },
  {
    id: 4,
    icon: <BowlFood size={40} weight="regular" />,
    title: "Exquisite Cuisine",
    description: "A culinary tradition spanning millennia — from fresh lavash to world-class wines and brandy.",
  },
];

export default function WhyChooseArmenia() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });

  return (
    <section id="about" className="py-24 px-8 bg-gray-50" aria-labelledby="why-heading">
      <div className="max-w-7xl mx-auto">
        <motion.div
          ref={ref}
          initial={{ opacity: 0, y: 24 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, ease: "easeOut" }}
          className="text-center mb-12"
        >
          <span className="text-primary font-medium text-sm uppercase tracking-widest">Why Armenia</span>
          <h2 id="why-heading" className="font-heading text-h1 font-medium text-foreground mt-2 mb-4">
            Why Choose Armenia?
          </h2>
          <p className="text-muted-foreground text-body max-w-xl mx-auto">
            A destination unlike any other — where ancient history meets breathtaking nature.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          {reasons.map((reason, i) => (
            <motion.div
              key={reason.id}
              initial={{ opacity: 0, x: -32 }}
              animate={inView ? { opacity: 1, x: 0 } : {}}
              transition={{ duration: 0.6, delay: i * 0.12, ease: "easeOut" }}
              className="flex flex-col items-center text-center p-6 rounded-xl bg-background border border-border"
            >
              <span className="text-primary mb-4">{reason.icon}</span>
              <h3 className="font-heading text-h3 font-medium text-foreground mb-3">{reason.title}</h3>
              <p className="text-muted-foreground text-sm leading-relaxed">{reason.description}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}