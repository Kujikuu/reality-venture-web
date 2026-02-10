import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { sectionVariants, staggerContainer } from './animations/CommonAnimations';
import { BlogCard } from './BlogCard';
import type { BlogPost } from '../types';

interface PageProps {
  latestPosts: BlogPost[];
  [key: string]: unknown;
}

export const LatestPosts: React.FC = () => {
  const { t, i18n } = useTranslation('blog');
  const { latestPosts } = usePage<PageProps>().props;
  const isArabic = i18n.language === 'ar';

  if (!latestPosts || latestPosts.length === 0) {
    return null;
  }

  return (
    <section className="relative bg-gray-50 py-24 overflow-hidden">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        {/* Header */}
        <motion.div
          className="flex flex-col lg:flex-row lg:items-end justify-between mb-16 gap-8"
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
          variants={sectionVariants}
        >
          <div className="max-w-2xl">
            <div className="flex items-center gap-3 mb-4">
              <span className="flex h-2 w-2 rounded-full bg-primary"></span>
              <span className="font-bold text-xs uppercase tracking-widest text-primary">
                {t('latestPosts.badge')}
              </span>
            </div>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 leading-tight">
              {t('latestPosts.title')}
            </h2>
            <p className="text-lg text-gray-600 mt-2">
              {t('latestPosts.subtitle')}
            </p>
          </div>

          <Link
            href="/blog"
            className="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all group"
          >
            {t('latestPosts.viewAll')}
            <ArrowRight className={`w-4 h-4 transition-transform group-hover:translate-x-1 ${isArabic ? 'rotate-180 group-hover:-translate-x-1' : ''}`} />
          </Link>
        </motion.div>

        {/* Posts Grid */}
        <motion.div
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
          variants={staggerContainer}
        >
          {latestPosts.map((post) => (
            <BlogCard key={post.id} post={post} />
          ))}
        </motion.div>
      </div>
    </section>
  );
};
