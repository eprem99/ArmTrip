import React from "react";
import { HelmetProvider } from "react-helmet-async";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Navbar from "./components/Navbar";
import HeroSection from "./components/HeroSection";
import PopularDestinations from "./components/PopularDestinations";
import FeaturedAccommodations from "./components/FeaturedAccommodations";
import TouristAttractions from "./components/TouristAttractions";
import ExperiencesSection from "./components/ExperiencesSection";
import TravelGuides from "./components/TravelGuides";
import WhyChooseArmenia from "./components/WhyChooseArmenia";
import Newsletter from "./components/Newsletter";
import Footer from "./components/Footer";
import BlogListingPage from "./pages/BlogListingPage";
import BlogPostPage from "./pages/BlogPostPage";
import RentalsListingPage from "./pages/RentalsListingPage";
import RentalPropertyPage from "./pages/RentalPropertyPage";

function HomePage() {
  return (
    <div className="min-h-screen bg-background text-foreground font-sans">
      <Navbar />
      <main>
        <HeroSection />
        <PopularDestinations />
        <FeaturedAccommodations />
        <TouristAttractions />
        <ExperiencesSection />
        <TravelGuides />
        <WhyChooseArmenia />
        <Newsletter />
      </main>
      <Footer />
    </div>
  );
}

export default function App() {
  return (
    <HelmetProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/blog" element={<BlogListingPage />} />
          <Route path="/blog/:slug" element={<BlogPostPage />} />
          <Route path="/rentals" element={<RentalsListingPage />} />
          <Route path="/rentals/:slug" element={<RentalPropertyPage />} />
        </Routes>
      </BrowserRouter>
    </HelmetProvider>
  );
}
