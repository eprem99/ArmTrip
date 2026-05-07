import React, { useState, useEffect, useRef } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { List, X, Globe, CaretDown } from "@phosphor-icons/react";
import { Button } from "@/components/ui/button";
import { Link, useNavigate, useLocation } from "react-router-dom";

const navLinks = [
  { label: "Destinations", href: "#destinations", type: "scroll" },
  { label: "Rentals", href: "/rentals", type: "route" },
  { label: "Attractions", href: "#attractions", type: "scroll" },
  { label: "Experiences", href: "#experiences", type: "scroll" },
  { label: "Blog", href: "/blog", type: "route" },
  { label: "Contact", href: "#contact", type: "scroll" },
];

const languages = [
  { code: "en", label: "EN", full: "English" },
  { code: "ru", label: "RU", full: "Русский" },
  { code: "hy", label: "HY", full: "Հայերեն" },
];

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [langOpen, setLangOpen] = useState(false);
  const [activeLang, setActiveLang] = useState(languages[0]);
  const [activeSection, setActiveSection] = useState("");
  const langRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 60);
    };
    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (langRef.current && !langRef.current.contains(e.target as Node)) {
        setLangOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  useEffect(() => {
    const sectionIds = navLinks.map((l) => l.href.replace("#", ""));
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActiveSection(entry.target.id);
          }
        });
      },
      { threshold: 0.3 }
    );
    sectionIds.forEach((id) => {
      const el = document.getElementById(id);
      if (el) observer.observe(el);
    });
    return () => observer.disconnect();
  }, []);

  const navigate = useNavigate();
  const location = useLocation();

  const isLight = !scrolled;
  const bgClass = scrolled
    ? "bg-background border-b border-border"
    : "bg-transparent";

  const handleNavClick = (href: string, type: string) => {
    setMobileOpen(false);
    if (type === "route") {
      navigate(href);
      return;
    }
    if (location.pathname !== "/") {
      navigate("/");
      setTimeout(() => {
        const id = href.replace("#", "");
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: "smooth" });
      }, 300);
      return;
    }
    const id = href.replace("#", "");
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: "smooth" });
  };

  return (
    <>
      <header
        className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${bgClass}`}
        style={{ minHeight: "64px" }}
      >
        <div className="max-w-7xl mx-auto px-8 flex items-center justify-between h-16">
          {/* Logo */}
          <a
            href="#hero"
            onClick={(e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: "smooth" }); }}
            className="flex items-center cursor-pointer select-none group"
            aria-label="ARMTrip Home"
          >
            <img
              src={scrolled
                ? "https://c.animaapp.com/mmoxd21v67mhk5/img/generated-image-1777656649054.png"
                : "https://c.animaapp.com/mmoxd21v67mhk5/img/generated-image-1777656473304.png"
              }
              alt="ARMTrip logo"
              className="h-10 w-auto object-contain transition-all duration-300"
            />
          </a>

          {/* Desktop Nav */}
          <nav className="hidden lg:flex items-center gap-1" aria-label="Main navigation">
            {navLinks.map((link) => (
              <a
                key={link.label}
                href={link.href}
                onClick={(e) => { e.preventDefault(); handleNavClick(link.href, link.type); }}
                className={`nav-link-underline px-3 py-2 text-sm font-normal cursor-pointer transition-colors duration-250 rounded-md
                  ${scrolled ? "text-foreground hover:text-primary" : "text-white hover:text-white/80"}
                  ${link.type === "route" && location.pathname === link.href ? "text-primary" : ""}
                  ${link.type === "scroll" && activeSection === link.href.replace("#", "") ? "active" : ""}
                `}
              >
                {link.label}
              </a>
            ))}
          </nav>

          {/* Right side */}
          <div className="hidden lg:flex items-center gap-3">
            {/* Language selector */}
            <div className="relative" ref={langRef}>
              <button
                onClick={() => setLangOpen(!langOpen)}
                className={`flex items-center gap-1 px-3 py-2 rounded-md text-sm font-normal cursor-pointer transition-colors duration-250
                  ${scrolled ? "text-foreground hover:text-primary" : "text-white hover:text-white/80"}
                `}
                aria-label="Select language"
                aria-expanded={langOpen}
              >
                <Globe size={18} weight="regular" />
                <span>{activeLang.label}</span>
                <CaretDown size={14} weight="regular" className={`transition-transform duration-250 ${langOpen ? "rotate-180" : ""}`} />
              </button>
              <AnimatePresence>
                {langOpen && (
                  <motion.div
                    initial={{ opacity: 0, y: -8 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -8 }}
                    transition={{ duration: 0.2, ease: "easeInOut" }}
                    className="absolute right-0 top-full mt-1 bg-background border border-border rounded-md overflow-hidden min-w-[120px]"
                    role="listbox"
                    aria-label="Language options"
                  >
                    {languages.map((lang) => (
                      <button
                        key={lang.code}
                        onClick={() => { setActiveLang(lang); setLangOpen(false); }}
                        className={`w-full text-left px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors duration-200 cursor-pointer
                          ${activeLang.code === lang.code ? "text-primary font-medium" : ""}
                        `}
                        role="option"
                        aria-selected={activeLang.code === lang.code}
                      >
                        {lang.full}
                      </button>
                    ))}
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            <Button
              onClick={() => handleNavClick("#destinations", "scroll")}
              className="bg-primary text-primary-foreground hover:bg-primary-hover font-normal text-sm px-5 py-2 cursor-pointer transition-transform duration-250 hover:scale-[1.03]"
            >
              Explore
            </Button>
          </div>

          {/* Mobile hamburger */}
          <button
            className={`lg:hidden p-2 rounded-md cursor-pointer ${scrolled ? "text-foreground" : "text-white"}`}
            onClick={() => setMobileOpen(true)}
            aria-label="Open mobile menu"
          >
            <List size={28} weight="regular" />
          </button>
        </div>
      </header>

      {/* Mobile Drawer */}
      <AnimatePresence>
        {mobileOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 0.5 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 z-50 bg-gray-900"
              onClick={() => setMobileOpen(false)}
            />
            <motion.div
              initial={{ x: "100%" }}
              animate={{ x: 0 }}
              exit={{ x: "100%" }}
              transition={{ duration: 0.3, ease: "easeInOut" }}
              className="fixed top-0 right-0 bottom-0 z-50 w-72 bg-background flex flex-col"
              role="dialog"
              aria-modal="true"
              aria-label="Mobile navigation"
            >
              <div className="flex items-center justify-between px-6 py-4 border-b border-border">
                <div className="flex items-center gap-2">
                  <div className="flex items-center justify-center w-8 h-8 rounded-lg bg-primary">
                    <svg width="18" height="18" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="11" cy="7" r="3" fill="white" opacity="0.95"/>
                      <path d="M2 18L8 9L12 14" fill="white" opacity="0.85"/>
                      <path d="M8 18L14 8L20 18H8Z" fill="white"/>
                    </svg>
                  </div>
                  <div className="flex flex-col leading-none">
                    <span className="font-heading font-semibold text-[13px] text-foreground tracking-tight">Stay</span>
                    <span className="font-heading font-bold text-[15px] text-primary" style={{letterSpacing: "0.08em"}}>ARMENIA</span>
                  </div>
                </div>
                <button
                  onClick={() => setMobileOpen(false)}
                  className="p-2 text-foreground cursor-pointer"
                  aria-label="Close mobile menu"
                >
                  <X size={24} weight="regular" />
                </button>
              </div>
              <nav className="flex-1 overflow-y-auto px-4 py-4" aria-label="Mobile navigation">
                {navLinks.map((link) => (
                  <a
                    key={link.label}
                    href={link.href}
                    onClick={(e) => { e.preventDefault(); handleNavClick(link.href, link.type); }}
                    className={`block px-4 py-3 hover:text-primary hover:bg-muted rounded-md text-base font-normal cursor-pointer transition-colors duration-200 ${
                      link.type === "route" && location.pathname === link.href ? "text-primary bg-muted" : "text-foreground"
                    }`}
                  >
                    {link.label}
                  </a>
                ))}
              </nav>
              <div className="px-6 py-4 border-t border-border space-y-3">
                <div className="flex gap-2">
                  {languages.map((lang) => (
                    <button
                      key={lang.code}
                      onClick={() => setActiveLang(lang)}
                      className={`px-3 py-1.5 rounded-md text-sm font-normal cursor-pointer transition-colors duration-200
                        ${activeLang.code === lang.code ? "bg-primary text-primary-foreground" : "bg-muted text-foreground hover:bg-gray-200"}
                      `}
                    >
                      {lang.label}
                    </button>
                  ))}
                </div>
                <Button
                  onClick={() => handleNavClick("#destinations", "scroll")}
                  className="w-full bg-primary text-primary-foreground hover:bg-primary-hover font-normal text-sm cursor-pointer"
                >
                  Explore
                </Button>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </>
  );
}