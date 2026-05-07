import React, { useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { MagnifyingGlass, X, FunnelSimple } from "@phosphor-icons/react";
import { blogPosts, categories, locations, durations, BlogCategory, BlogLocation, BlogDuration } from "../data/blogData";
import BlogCard from "../components/blog/BlogCard";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const categoryColors: Record<string, string> = {
  Guides: "bg-sky-50 text-sky-700 border-sky-200 hover:bg-sky-100",
  Food: "bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100",
  Culture: "bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100",
  Nature: "bg-green-50 text-green-700 border-green-200 hover:bg-green-100",
};

const POSTS_PER_PAGE = 6;

export default function BlogListingPage() {
  const [searchQuery, setSearchQuery] = useState("");
  const [activeCategory, setActiveCategory] = useState<BlogCategory | "">("");
  const [activeLocation, setActiveLocation] = useState<BlogLocation | "">("");
  const [activeDuration, setActiveDuration] = useState<BlogDuration | "">("");
  const [page, setPage] = useState(1);

  const filtered = useMemo(() => {
    return blogPosts.filter((p) => {
      const q = searchQuery.toLowerCase();
      const matchSearch = !q || p.title.toLowerCase().includes(q) || p.excerpt.toLowerCase().includes(q) || p.tags.some((t) => t.toLowerCase().includes(q));
      const matchCategory = !activeCategory || p.category === activeCategory;
      const matchLocation = !activeLocation || p.location === activeLocation;
      const matchDuration = !activeDuration || p.duration === activeDuration;
      return matchSearch && matchCategory && matchLocation && matchDuration;
    });
  }, [searchQuery, activeCategory, activeLocation, activeDuration]);

  const paginated = filtered.slice(0, page * POSTS_PER_PAGE);
  const hasMore = paginated.length < filtered.length;

  const resetFilters = () => {
    setActiveCategory("");
    setActiveLocation("");
    setActiveDuration("");
    setSearchQuery("");
    setPage(1);
  };

  const hasActiveFilters = activeCategory || activeLocation || activeDuration || searchQuery;

  return (
    <div className="min-h-screen bg-background text-foreground font-sans">
      <Navbar />

      {/* ── Hero ── */}
      <section
        className="relative flex items-center justify-center overflow-hidden"
        style={{ minHeight: "420px" }}
        aria-label="Blog hero"
      >
        <img
          src="https://images.unsplash.com/photo-1586177571965-2e45e9f8cedf?w=1600&h=600&fit=crop"
          alt="Armenian landscape"
          className="absolute inset-0 w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/70" />
        <div className="relative z-10 text-center text-white px-6 max-w-3xl mx-auto">
          {/* Breadcrumb */}
          <nav className="flex items-center justify-center gap-2 text-sm text-white/70 mb-6" aria-label="Breadcrumb">
            <Link to="/" className="hover:text-white transition-colors">Home</Link>
            <span>/</span>
            <span className="text-white">Blog</span>
          </nav>
          <h1 className="font-heading font-semibold text-4xl md:text-5xl leading-tight mb-4">
            Travel Blog about Armenia
          </h1>
          <p className="text-lg text-white/80 leading-relaxed">
            Tips, guides, itineraries, and stories from the Land of Mountains
          </p>
        </div>
      </section>

      {/* ── Filters bar ── */}
      <section className="sticky top-16 z-40 bg-white border-b border-gray-100 shadow-sm" aria-label="Blog filters">
        <div className="max-w-7xl mx-auto px-6 py-4">
          <div className="flex flex-wrap items-center gap-3">
            {/* Search */}
            <div className="relative flex-1 min-w-[200px] max-w-xs">
              <MagnifyingGlass size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                type="search"
                placeholder="Search articles..."
                value={searchQuery}
                onChange={(e) => { setSearchQuery(e.target.value); setPage(1); }}
                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                aria-label="Search blog posts"
              />
            </div>

            {/* Category filters */}
            <div className="flex flex-wrap gap-2" role="group" aria-label="Category filters">
              {categories.map((cat) => (
                <button
                  key={cat}
                  onClick={() => { setActiveCategory(activeCategory === cat ? "" : cat); setPage(1); }}
                  className={`text-xs font-medium px-3 py-1.5 rounded-full border transition-all duration-200 cursor-pointer ${
                    activeCategory === cat
                      ? "bg-primary text-white border-primary"
                      : categoryColors[cat]
                  }`}
                  aria-pressed={activeCategory === cat}
                >
                  {cat}
                </button>
              ))}
            </div>

            {/* Location dropdown */}
            <select
              value={activeLocation}
              onChange={(e) => { setActiveLocation(e.target.value as BlogLocation | ""); setPage(1); }}
              className="text-sm border border-gray-200 rounded-full px-3 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 cursor-pointer"
              aria-label="Filter by location"
            >
              <option value="">All Locations</option>
              {locations.map((loc) => <option key={loc} value={loc}>{loc}</option>)}
            </select>

            {/* Duration dropdown */}
            <select
              value={activeDuration}
              onChange={(e) => { setActiveDuration(e.target.value as BlogDuration | ""); setPage(1); }}
              className="text-sm border border-gray-200 rounded-full px-3 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/30 cursor-pointer"
              aria-label="Filter by duration"
            >
              <option value="">All Durations</option>
              {durations.map((d) => <option key={d} value={d}>{d}</option>)}
            </select>

            {hasActiveFilters && (
              <button
                onClick={resetFilters}
                className="flex items-center gap-1.5 text-xs text-gray-500 hover:text-primary transition-colors cursor-pointer"
                aria-label="Clear all filters"
              >
                <X size={14} />
                Clear
              </button>
            )}
          </div>
        </div>
      </section>

      {/* ── Main content ── */}
      <main className="max-w-7xl mx-auto px-6 py-12">
        <div className="flex gap-10">
          {/* Left: Blog Grid */}
          <div className="flex-1 min-w-0">
            {/* Result count */}
            <div className="flex items-center gap-2 mb-8">
              <FunnelSimple size={16} className="text-gray-400" />
              <span className="text-sm text-gray-500">
                {filtered.length} article{filtered.length !== 1 ? "s" : ""}
                {hasActiveFilters ? " found" : " total"}
              </span>
            </div>

            {filtered.length === 0 ? (
              <div className="text-center py-24">
                <p className="text-3xl mb-3">🏔️</p>
                <p className="text-gray-500 text-lg font-medium">No articles match your filters</p>
                <button onClick={resetFilters} className="mt-4 text-primary text-sm hover:underline cursor-pointer">
                  Clear filters
                </button>
              </div>
            ) : (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                  {paginated.map((post) => (
                    <BlogCard key={post.slug} post={post} />
                  ))}
                </div>

                {/* Load more */}
                {hasMore && (
                  <div className="flex justify-center mt-12">
                    <button
                      onClick={() => setPage((p) => p + 1)}
                      className="px-8 py-3 bg-primary text-white font-medium rounded-full hover:bg-primary-hover transition-colors duration-200 cursor-pointer shadow-sm hover:shadow-md"
                    >
                      Load more articles
                    </button>
                  </div>
                )}
              </>
            )}
          </div>

          {/* Right: Sidebar */}
          <aside className="hidden xl:flex flex-col gap-8 w-72 flex-shrink-0" aria-label="Blog sidebar">
            {/* Popular Posts */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <h2 className="font-heading font-semibold text-[15px] text-gray-800 mb-4">Popular Posts</h2>
              <ul className="space-y-4">
                {blogPosts.slice(0, 4).map((post, i) => (
                  <li key={post.slug}>
                    <Link
                      to={`/blog/${post.slug}`}
                      className="flex items-start gap-3 group"
                      aria-label={post.title}
                    >
                      <span className="text-2xl font-heading font-bold text-gray-100 group-hover:text-primary/30 transition-colors w-6 text-center leading-none mt-0.5 flex-shrink-0">
                        {String(i + 1).padStart(2, "0")}
                      </span>
                      <span className="text-sm text-gray-600 leading-snug group-hover:text-primary transition-colors line-clamp-2">
                        {post.title}
                      </span>
                    </Link>
                  </li>
                ))}
              </ul>
            </div>

            {/* Categories */}
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <h2 className="font-heading font-semibold text-[15px] text-gray-800 mb-4">Categories</h2>
              <ul className="space-y-2">
                {categories.map((cat) => {
                  const count = blogPosts.filter((p) => p.category === cat).length;
                  return (
                    <li key={cat}>
                      <button
                        onClick={() => { setActiveCategory(activeCategory === cat ? "" : cat); setPage(1); window.scrollTo({ top: 0, behavior: "smooth" }); }}
                        className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors cursor-pointer ${
                          activeCategory === cat ? "bg-primary/10 text-primary font-medium" : "text-gray-600 hover:bg-gray-50"
                        }`}
                        aria-pressed={activeCategory === cat}
                      >
                        <span>{cat}</span>
                        <span className="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{count}</span>
                      </button>
                    </li>
                  );
                })}
              </ul>
            </div>

            {/* Newsletter */}
            <div className="bg-gradient-to-br from-primary to-orange-600 rounded-2xl p-6 text-white">
              <h2 className="font-heading font-semibold text-[15px] mb-2">Stay in the loop</h2>
              <p className="text-sm text-white/80 mb-4 leading-relaxed">Get the latest travel guides and stories about Armenia delivered to your inbox.</p>
              <input
                type="email"
                placeholder="your@email.com"
                className="w-full px-4 py-2.5 rounded-xl text-sm text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50 mb-3"
                aria-label="Email for newsletter"
              />
              <button className="w-full py-2.5 bg-white text-primary font-semibold text-sm rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                Subscribe
              </button>
            </div>
          </aside>
        </div>
      </main>

      <Footer />
    </div>
  );
}
