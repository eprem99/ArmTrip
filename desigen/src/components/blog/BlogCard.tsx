import React from "react";
import { CalendarBlank, Clock, MapPin } from "@phosphor-icons/react";
import { BlogPost } from "../../data/blogData";
import { Link } from "react-router-dom";

interface BlogCardProps {
  post: BlogPost;
  variant?: "default" | "featured";
}

const categoryColors: Record<string, string> = {
  Guides: "bg-sky-100 text-sky-700",
  Food: "bg-amber-100 text-amber-700",
  Culture: "bg-rose-100 text-rose-700",
  Nature: "bg-green-100 text-green-700",
};

export default function BlogCard({ post, variant = "default" }: BlogCardProps) {
  return (
    <Link
      to={`/blog/${post.slug}`}
      className="group flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 border border-gray-100 h-full"
      aria-label={`Read article: ${post.title}`}
    >
      {/* Image */}
      <div className="relative overflow-hidden" style={{ height: variant === "featured" ? "240px" : "200px" }}>
        <img
          src={post.featuredImage}
          alt={post.title}
          className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          loading="lazy"
        />
        {/* Category badge */}
        <span className={`absolute top-3 left-3 text-xs font-medium px-2.5 py-1 rounded-full ${categoryColors[post.category]}`}>
          {post.category}
        </span>
      </div>

      {/* Body */}
      <div className="flex flex-col flex-1 p-5 gap-3">
        {/* Tags */}
        <div className="flex flex-wrap gap-1.5">
          <span className="inline-flex items-center gap-1 text-xs text-gray-500">
            <MapPin size={12} weight="bold" />
            {post.location}
          </span>
          <span className="text-xs text-gray-300">·</span>
          <span className="inline-flex items-center gap-1 text-xs text-gray-500">
            <Clock size={12} weight="bold" />
            {post.duration}
          </span>
        </div>

        {/* Title */}
        <h3 className="font-heading font-semibold text-gray-800 text-[16px] leading-snug group-hover:text-primary transition-colors duration-200 line-clamp-2">
          {post.title}
        </h3>

        {/* Excerpt */}
        <p className="text-sm text-gray-500 leading-relaxed line-clamp-2 flex-1">
          {post.excerpt}
        </p>

        {/* Footer */}
        <div className="flex items-center justify-between pt-3 border-t border-gray-100">
          <div className="flex items-center gap-2">
            <img
              src={post.author.avatar}
              alt={post.author.name}
              className="w-7 h-7 rounded-full object-cover"
            />
            <span className="text-xs text-gray-600 font-medium">{post.author.name}</span>
          </div>
          <div className="flex items-center gap-1.5 text-xs text-gray-400">
            <CalendarBlank size={12} weight="regular" />
            <span>{new Date(post.publishDate).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</span>
          </div>
        </div>
      </div>
    </Link>
  );
}
