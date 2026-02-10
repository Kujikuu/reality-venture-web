import React from 'react';

export interface ServiceItem {
  icon: React.ElementType;
  title?: string;
  description?: string;
  titleKey?: string;
  descriptionKey?: string;
}

export interface ProgramItem {
  title: string;
  description: string;
  audience: string;
  details?: string[];
  cta: string;
}

export interface TeamMember {
  name: string;
  role: string;
  image?: string;
  bio?: string;
}

export interface ProcessStep {
  number: string;
  title: string;
  description: string;
}

export interface TargetAudienceItem {
  title: string;
  description: string;
}

export interface BlogPost {
  id: number;
  title_en: string;
  title_ar: string;
  slug: string;
  excerpt_en: string | null;
  excerpt_ar: string | null;
  content_en?: string;
  content_ar?: string;
  featured_image: string | null;
  meta_title?: string | null;
  meta_description?: string | null;
  og_image?: string | null;
  published_at: string;
  author: {
    name: string;
  };
  category: {
    name_en: string;
    name_ar: string;
    slug: string;
  } | null;
  tags?: {
    name_en: string;
    name_ar: string;
    slug: string;
  }[];
}

export interface BlogCategory {
  name_en: string;
  name_ar: string;
  slug: string;
  posts_count: number;
}

export interface PaginatedData<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  first_page_url: string | null;
  last_page_url: string | null;
  prev_page_url: string | null;
  next_page_url: string | null;
  path: string;
}