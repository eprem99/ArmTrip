import React from "react";
import { InstagramLogo, FacebookLogo, YoutubeLogo, Star, MapPin, Phone, EnvelopeSimple } from "@phosphor-icons/react";

const footerLinks = {
  explore: [
    { label: "Destinations", href: "#destinations" },
    { label: "Stays & Rentals", href: "#stays" },
    { label: "Attractions", href: "#attractions" },
    { label: "Experiences", href: "#experiences" },
  ],
  guides: [
    { label: "Travel Guides", href: "#guides" },
    { label: "About Armenia", href: "#about" },
    { label: "Travel Tips", href: "#guides" },
    { label: "FAQ", href: "#contact" },
  ],
};

export default function Footer() {
  const handleNavClick = (href: string) => {
    const id = href.replace("#", "");
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: "smooth" });
  };

  return (
    <footer className="bg-gray-900 text-white" aria-label="Site footer">
      <div className="max-w-7xl mx-auto px-8 py-16">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
          {/* Brand + About */}
          <div>
            <div className="flex items-center gap-2 mb-4">
              <Star size={32} weight="fill" className="text-primary" />
              <span className="font-heading text-h3 font-semibold text-white">Armenia</span>
            </div>
            <p className="text-gray-400 text-sm leading-relaxed mb-6">
              Your gateway to discovering the Land of Mountains and Legends. Explore, stay, and experience Armenia like never before.
            </p>
            {/* Social icons */}
            <div className="flex gap-4">
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary transition-colors duration-250 cursor-pointer"
                aria-label="Instagram"
              >
                <InstagramLogo size={24} weight="regular" />
              </a>
              <a
                href="https://facebook.com"
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary transition-colors duration-250 cursor-pointer"
                aria-label="Facebook"
              >
                <FacebookLogo size={24} weight="regular" />
              </a>
              <a
                href="https://youtube.com"
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary transition-colors duration-250 cursor-pointer"
                aria-label="YouTube"
              >
                <YoutubeLogo size={24} weight="regular" />
              </a>
            </div>
          </div>

          {/* Navigation Links */}
          <div className="grid grid-cols-2 gap-8">
            <div>
              <h3 className="font-heading text-sm font-medium text-white uppercase tracking-widest mb-4">Explore</h3>
              <ul className="space-y-2">
                {footerLinks.explore.map((link) => (
                  <li key={link.label}>
                    <a
                      href={link.href}
                      onClick={(e) => { e.preventDefault(); handleNavClick(link.href); }}
                      className="text-gray-400 hover:text-primary text-sm transition-colors duration-250 cursor-pointer"
                    >
                      {link.label}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h3 className="font-heading text-sm font-medium text-white uppercase tracking-widest mb-4">Resources</h3>
              <ul className="space-y-2">
                {footerLinks.guides.map((link) => (
                  <li key={link.label}>
                    <a
                      href={link.href}
                      onClick={(e) => { e.preventDefault(); handleNavClick(link.href); }}
                      className="text-gray-400 hover:text-primary text-sm transition-colors duration-250 cursor-pointer"
                    >
                      {link.label}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Contact */}
          <div>
            <h3 className="font-heading text-sm font-medium text-white uppercase tracking-widest mb-4">Contact</h3>
            <ul className="space-y-3">
              <li className="flex items-start gap-3">
                <MapPin size={18} weight="regular" className="text-primary flex-shrink-0 mt-0.5" />
                <span className="text-gray-400 text-sm">Yerevan, Republic of Armenia</span>
              </li>
              <li className="flex items-center gap-3">
                <Phone size={18} weight="regular" className="text-primary flex-shrink-0" />
                <a href="tel:+37410000000" className="text-gray-400 hover:text-primary text-sm transition-colors duration-250 cursor-pointer">
                  +374 10 000 000
                </a>
              </li>
              <li className="flex items-center gap-3">
                <EnvelopeSimple size={18} weight="regular" className="text-primary flex-shrink-0" />
                <a href="mailto:hello@armenia.travel" className="text-gray-400 hover:text-primary text-sm transition-colors duration-250 cursor-pointer">
                  hello@armenia.travel
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div className="border-t border-gray-800">
        <div className="max-w-7xl mx-auto px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p className="text-gray-500 text-xs">
            &copy; {new Date().getFullYear()} Armenia Travel. All rights reserved.
          </p>
          <div className="flex gap-4">
            <a href="#" className="text-gray-500 hover:text-gray-300 text-xs transition-colors duration-250 cursor-pointer">Privacy Policy</a>
            <a href="#" className="text-gray-500 hover:text-gray-300 text-xs transition-colors duration-250 cursor-pointer">Terms of Service</a>
            <a href="#" className="text-gray-500 hover:text-gray-300 text-xs transition-colors duration-250 cursor-pointer">Cookie Policy</a>
          </div>
        </div>
      </div>
    </footer>
  );
}