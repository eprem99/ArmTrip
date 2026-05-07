import React, { useRef, useState } from "react";
import { motion, useInView } from "framer-motion";
import { EnvelopeSimple, PaperPlaneTilt } from "@phosphor-icons/react";
import { Button } from "@/components/ui/button";

export default function Newsletter() {
  const ref = useRef(null);
  const inView = useInView(ref, { once: true, margin: "-80px" });
  const [email, setEmail] = useState("");
  const [submitted, setSubmitted] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (email.trim()) {
      setSubmitted(true);
    }
  };

  return (
    <section
      id="contact"
      className="py-24 px-8"
      style={{ background: "linear-gradient(135deg, hsl(145, 25%, 35%), hsl(150, 24%, 30%))" }}
      aria-labelledby="newsletter-heading"
    >
      <div className="max-w-2xl mx-auto text-center" ref={ref}>
        <motion.div
          initial={{ opacity: 0, y: 32 }}
          animate={inView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.7, ease: "easeOut" }}
        >
          <div className="flex justify-center mb-4">
            <EnvelopeSimple size={48} weight="regular" className="text-white/80" />
          </div>
          <h2 id="newsletter-heading" className="font-heading text-h1 font-medium text-white mb-3">
            Stay Inspired!
          </h2>
          <p className="text-white/80 text-body mb-8">
            Subscribe for travel tips, destination guides, and exclusive Armenia travel news.
          </p>

          {submitted ? (
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.4 }}
              className="flex items-center justify-center gap-3 bg-white/20 rounded-xl px-6 py-4"
            >
              <PaperPlaneTilt size={24} weight="fill" className="text-white" />
              <p className="text-white font-medium">Thank you! You're now subscribed.</p>
            </motion.div>
          ) : (
            <form
              onSubmit={handleSubmit}
              className="flex flex-col sm:flex-row gap-3 max-w-md mx-auto"
              aria-label="Newsletter signup form"
            >
              <div className="flex-1 relative">
                <label htmlFor="newsletter-email" className="sr-only">Email address</label>
                <input
                  id="newsletter-email"
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="Enter your email address"
                  required
                  className="w-full px-4 py-3 rounded-lg bg-white/20 border border-white/30 text-white placeholder:text-white/60 text-sm outline-none focus:border-white/60 transition-colors duration-250"
                  aria-required="true"
                />
              </div>
              <Button
                type="submit"
                className="bg-primary text-primary-foreground hover:bg-primary-hover font-normal text-sm px-6 py-3 cursor-pointer transition-transform duration-250 hover:scale-[1.03] flex-shrink-0"
              >
                Subscribe
              </Button>
            </form>
          )}

          <p className="text-white/50 text-xs mt-4">
            No spam, ever. Unsubscribe at any time.
          </p>
        </motion.div>
      </div>
    </section>
  );
}