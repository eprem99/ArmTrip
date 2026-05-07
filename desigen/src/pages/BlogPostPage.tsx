import React, { useState, useEffect, useRef } from "react";
import { Link, useParams, Navigate } from "react-router-dom";
import {
  CalendarBlank, Clock, MapPin, ArrowLeft, Tag, BookmarkSimple,
  TwitterLogo, FacebookLogo, LinkSimple, CheckCircle,
} from "@phosphor-icons/react";
import { getPostBySlug, getRelatedPosts, BlogContentBlock } from "../data/blogData";
import BlogCard from "../components/blog/BlogCard";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const categoryColors: Record<string, string> = {
  Guides: "bg-sky-100 text-sky-700",
  Food: "bg-amber-100 text-amber-700",
  Culture: "bg-rose-100 text-rose-700",
  Nature: "bg-green-100 text-green-700",
};

function ContentBlock({ block }: { block: BlogContentBlock }) {
  switch (block.type) {
    case "h2":
      return <h2 id={block.id} className="font-heading font-semibold text-2xl text-gray-900 mt-10 mb-4 scroll-mt-24">{block.text}</h2>;
    case "h3":
      return <h3 id={block.id} className="font-heading font-semibold text-lg text-gray-800 mt-7 mb-3 scroll-mt-24">{block.text}</h3>;
    case "p":
      return <p className="text-gray-600 leading-[1.85] text-[16px] mb-5">{block.text}</p>;
    case "quote":
      return (
        <blockquote className="border-l-4 border-primary bg-orange-50 rounded-r-xl px-6 py-5 my-8">
          <p className="text-gray-700 italic text-[17px] leading-relaxed mb-2">&#8220;{block.text}&#8221;</p>
          {block.attribution && (
            <footer className="text-sm text-gray-500 font-medium">— {block.attribution}</footer>
          )}
        </blockquote>
      );
    case "image":
      return (
        <figure className="my-8 rounded-2xl overflow-hidden shadow-md">
          <img src={block.src} alt={block.alt} className="w-full object-cover" style={{ maxHeight: "460px" }} loading="lazy" />
          {block.caption && (
            <figcaption className="text-center text-sm text-gray-500 bg-gray-50 py-2.5 px-4">{block.caption}</figcaption>
          )}
        </figure>
      );
    case "list":
      return (
        <ul className="space-y-2.5 mb-6 pl-1">
          {block.items.map((item, i) => (
            <li key={i} className="flex items-start gap-3 text-gray-600 text-[16px] leading-relaxed">
              <span className="w-5 h-5 rounded-full bg-primary/15 text-primary text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{i + 1}</span>
              <span>{item}</span>
            </li>
          ))}
        </ul>
      );
    default:
      return null;
  }
}

export default function BlogPostPage() {
  const { slug } = useParams<{ slug: string }>();
  const post = getPostBySlug(slug ?? "");
  const [activeToc, setActiveToc] = useState("");
  const [copied, setCopied] = useState(false);
  const contentRef = useRef<HTMLDivElement>(null);

  if (!post) return <Navigate to="/blog" replace />;

  const related = getRelatedPosts(post, 3);

  // Scroll spy for TOC
  useEffect(() => {
    const headingIds = post.tableOfContents.map((t) => t.id);
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) setActiveToc(entry.target.id);
        });
      },
      { rootMargin: "-20% 0px -70% 0px" }
    );
    headingIds.forEach((id) => {
      const el = document.getElementById(id);
      if (el) observer.observe(el);
    });
    return () => observer.disconnect();
  }, [post]);

  const handleCopyLink = () => {
    navigator.clipboard.writeText(window.location.href);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="min-h-screen bg-background text-foreground font-sans">
      <Navbar />

      {/* ── Post Hero ── */}
      <section className="relative overflow-hidden" style={{ minHeight: "520px" }} aria-label="Post hero">
        <img
          src={post.featuredImage}
          alt={post.title}
          className="absolute inset-0 w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20" />
        <div className="relative z-10 flex flex-col justify-end h-full max-w-4xl mx-auto px-6 pb-12 pt-28">
          {/* Breadcrumb */}
          <nav className="flex items-center gap-2 text-sm text-white/60 mb-5" aria-label="Breadcrumb">
            <Link to="/" className="hover:text-white transition-colors">Home</Link>
            <span>/</span>
            <Link to="/blog" className="hover:text-white transition-colors">Blog</Link>
            <span>/</span>
            <span className={`text-xs font-medium px-2.5 py-1 rounded-full ${categoryColors[post.category]}`}>{post.category}</span>
            <span>/</span>
            <span className="text-white/80 truncate max-w-xs">{post.title}</span>
          </nav>

          <span className={`self-start text-xs font-semibold px-3 py-1 rounded-full mb-4 ${categoryColors[post.category]}`}>
            {post.category}
          </span>
          <h1 className="font-heading font-bold text-white text-3xl md:text-4xl lg:text-5xl leading-tight mb-5 max-w-3xl">
            {post.title}
          </h1>
          {/* Meta */}
          <div className="flex flex-wrap items-center gap-4 text-sm text-white/75">
            <div className="flex items-center gap-2">
              <img src={post.author.avatar} alt={post.author.name} className="w-8 h-8 rounded-full object-cover border-2 border-white/30" />
              <span className="text-white font-medium">{post.author.name}</span>
            </div>
            <div className="flex items-center gap-1.5">
              <CalendarBlank size={14} />
              <span>{new Date(post.publishDate).toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" })}</span>
            </div>
            <div className="flex items-center gap-1.5">
              <Clock size={14} />
              <span>{post.readingTime} min read</span>
            </div>
            <div className="flex items-center gap-1.5">
              <MapPin size={14} />
              <span>{post.location}</span>
            </div>
          </div>
        </div>
      </section>

      {/* ── Body ── */}
      <main className="max-w-7xl mx-auto px-6 py-12">
        <div className="flex gap-10 items-start">

          {/* ── Article column ── */}
          <article className="flex-1 min-w-0 max-w-3xl" ref={contentRef}>
            {/* Back link */}
            <Link
              to="/blog"
              className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition-colors mb-8 cursor-pointer"
            >
              <ArrowLeft size={16} />
              Back to Blog
            </Link>

            {/* Quick Facts */}
            <div className="bg-amber-50 border border-amber-100 rounded-2xl p-6 mb-8">
              <h2 className="font-heading font-semibold text-[15px] text-amber-800 mb-4">⚡ Quick Facts</h2>
              <dl className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                {post.quickFacts.map((fact) => (
                  <div key={fact.label}>
                    <dt className="text-xs text-amber-600 font-medium uppercase tracking-wide mb-1">{fact.label}</dt>
                    <dd className="text-sm text-gray-700 font-medium">{fact.value}</dd>
                  </div>
                ))}
              </dl>
            </div>

            {/* Article content */}
            <div className="prose-custom">
              {post.content.map((block, i) => (
                <ContentBlock key={i} block={block} />
              ))}
            </div>

            {/* Tags */}
            <div className="flex flex-wrap gap-2 mt-10 pt-8 border-t border-gray-100">
              <Tag size={16} className="text-gray-400 mt-0.5" />
              {post.tags.map((tag) => (
                <Link
                  key={tag}
                  to={`/blog?q=${encodeURIComponent(tag)}`}
                  className="text-xs bg-gray-100 text-gray-600 hover:bg-primary hover:text-white px-3 py-1 rounded-full transition-colors duration-200 cursor-pointer"
                >
                  {tag}
                </Link>
              ))}
            </div>

            {/* Share */}
            <div className="flex items-center gap-3 mt-6">
              <span className="text-sm text-gray-500 font-medium">Share:</span>
              <a
                href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(post.title)}&url=${encodeURIComponent(typeof window !== "undefined" ? window.location.href : "")}`}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center gap-1.5 text-sm text-gray-500 hover:text-sky-500 transition-colors cursor-pointer"
                aria-label="Share on Twitter"
              >
                <TwitterLogo size={18} />
              </a>
              <a
                href={`https://facebook.com/sharer/sharer.php?u=${encodeURIComponent(typeof window !== "undefined" ? window.location.href : "")}`}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors cursor-pointer"
                aria-label="Share on Facebook"
              >
                <FacebookLogo size={18} />
              </a>
              <button
                onClick={handleCopyLink}
                className="flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary transition-colors cursor-pointer"
                aria-label="Copy link"
              >
                {copied ? <CheckCircle size={18} className="text-green-500" /> : <LinkSimple size={18} />}
                <span className="text-xs">{copied ? "Copied!" : "Copy link"}</span>
              </button>
            </div>

            {/* Map section */}
            <div className="mt-10">
              <h2 className="font-heading font-semibold text-xl text-gray-900 mb-4">📍 Location</h2>
              <div className="w-full rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100 flex items-center justify-center" style={{ height: "280px" }}>
                <div className="text-center text-gray-400">
                  <MapPin size={40} className="mx-auto mb-3 text-gray-300" />
                  <p className="text-sm font-medium text-gray-500">{post.location}, Armenia</p>
                  <p className="text-xs text-gray-400 mt-1">Map coming soon</p>
                </div>
              </div>
            </div>

            {/* Author Box */}
            <div className="mt-10 bg-gray-50 rounded-2xl p-6 flex items-start gap-5 border border-gray-100">
              <img
                src={post.author.avatar}
                alt={post.author.name}
                className="w-16 h-16 rounded-full object-cover flex-shrink-0 border-2 border-white shadow-sm"
              />
              <div>
                <p className="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Written by</p>
                <h3 className="font-heading font-semibold text-gray-900 text-[16px] mb-2">{post.author.name}</h3>
                <p className="text-sm text-gray-500 leading-relaxed">{post.author.bio}</p>
              </div>
            </div>

            {/* Comments placeholder */}
            <div className="mt-10">
              <h2 className="font-heading font-semibold text-xl text-gray-900 mb-6">Comments</h2>
              <div className="space-y-4">
                {[{ name: "Nare K.", text: "Visited Yerevan last spring and this guide was spot on! The Cascade at sunset is magical.", date: "March 20, 2026" }, { name: "Alex M.", text: "Super helpful itinerary. Would add a stop at the Matenadaran manuscript museum — definitely worth a few hours.", date: "March 18, 2026" }].map((c) => (
                  <div key={c.name} className="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div className="flex items-center gap-3 mb-3">
                      <div className="w-9 h-9 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm flex-shrink-0">
                        {c.name[0]}
                      </div>
                      <div>
                        <p className="font-medium text-gray-800 text-sm">{c.name}</p>
                        <p className="text-xs text-gray-400">{c.date}</p>
                      </div>
                    </div>
                    <p className="text-sm text-gray-600 leading-relaxed">{c.text}</p>
                  </div>
                ))}
              </div>
              {/* Comment form */}
              <div className="mt-6 bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                <h3 className="font-heading font-semibold text-gray-800 text-[15px] mb-4">Leave a comment</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                  <input type="text" placeholder="Your name" className="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30" aria-label="Your name" />
                  <input type="email" placeholder="your@email.com" className="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30" aria-label="Your email" />
                </div>
                <textarea rows={4} placeholder="Share your thoughts..." className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 mb-4 resize-none" aria-label="Comment" />
                <button className="px-6 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-hover transition-colors cursor-pointer">
                  Post Comment
                </button>
              </div>
            </div>
          </article>

          {/* ── Sticky TOC sidebar ── */}
          <aside className="hidden xl:block w-64 flex-shrink-0 sticky top-28 self-start" aria-label="Table of contents">
            <div className="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 mb-6">
              <h2 className="font-heading font-semibold text-[13px] uppercase tracking-widest text-gray-400 mb-4">Contents</h2>
              <nav>
                <ul className="space-y-1">
                  {post.tableOfContents.map((item) => (
                    <li key={item.id}>
                      <a
                        href={`#${item.id}`}
                        onClick={(e) => {
                          e.preventDefault();
                          document.getElementById(item.id)?.scrollIntoView({ behavior: "smooth" });
                        }}
                        className={`block text-sm leading-snug py-1 px-3 rounded-lg transition-all duration-200 cursor-pointer border-l-2 ${
                          item.level === 3 ? "ml-3 text-xs" : ""
                        } ${
                          activeToc === item.id
                            ? "border-primary text-primary font-medium bg-primary/5"
                            : "border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-200"
                        }`}
                      >
                        {item.title}
                      </a>
                    </li>
                  ))}
                </ul>
              </nav>
            </div>

            {/* Bookmark */}
            <button className="w-full flex items-center justify-center gap-2 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-primary hover:text-primary transition-colors cursor-pointer shadow-sm">
              <BookmarkSimple size={16} />
              Save article
            </button>
          </aside>
        </div>

        {/* ── Related Posts ── */}
        {related.length > 0 && (
          <section className="mt-16 pt-10 border-t border-gray-100" aria-label="Related articles">
            <h2 className="font-heading font-semibold text-2xl text-gray-900 mb-8">Related Articles</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {related.map((p) => (
                <BlogCard key={p.slug} post={p} />
              ))}
            </div>
          </section>
        )}
      </main>

      <Footer />
    </div>
  );
}
