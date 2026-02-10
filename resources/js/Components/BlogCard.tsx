import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Calendar, User } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { cardVariants } from './animations/CommonAnimations';
import type { BlogPost } from '../types';

interface BlogCardProps {
  post: BlogPost;
}

export const BlogCard: React.FC<BlogCardProps> = ({ post }) => {
  const { i18n, t } = useTranslation('blog');
  const isArabic = i18n.language === 'ar';

  const title = isArabic ? post.title_ar : post.title_en;
  const excerpt = isArabic ? (post.excerpt_ar || post.excerpt_en) : (post.excerpt_en || post.excerpt_ar);
  const categoryName = post.category
    ? (isArabic ? post.category.name_ar : post.category.name_en)
    : null;

  const formattedDate = new Date(post.published_at).toLocaleDateString(
    isArabic ? 'ar-SA' : 'en-US',
    { year: 'numeric', month: 'long', day: 'numeric' }
  );

  return (
    <motion.article
      variants={cardVariants}
      className="group bg-white rounded-xl overflow-hidden border border-gray-100 hover:border-primary/20 transition-all duration-300"
    >
      <Link href={`/blog/${post.slug}`} className="block">
        {/* Featured Image */}
        <div className="aspect-[16/10] overflow-hidden bg-gray-100">
          {post.featured_image ? (
            <img
              src={post.featured_image}
              alt={title}
              className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
              <span className="text-primary-300 text-4xl font-bold">RV</span>
            </div>
          )}
        </div>

        {/* Content */}
        <div className="p-5">
          {/* Category Badge */}
          {categoryName && (
            <span className="inline-block text-xs font-semibold text-primary bg-primary-50 px-3 py-1 rounded-full mb-3">
              {categoryName}
            </span>
          )}

          {/* Title */}
          <h3 className="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary transition-colors">
            {title}
          </h3>

          {/* Excerpt */}
          {excerpt && (
            <p className="text-sm text-gray-500 mb-4 line-clamp-2 leading-relaxed">
              {excerpt}
            </p>
          )}

          {/* Meta Row */}
          <div className="flex items-center gap-4 text-xs text-gray-400">
            <span className="flex items-center gap-1.5">
              <User className="w-3.5 h-3.5" />
              {post.author.name}
            </span>
            <span className="flex items-center gap-1.5">
              <Calendar className="w-3.5 h-3.5" />
              {formattedDate}
            </span>
          </div>
        </div>
      </Link>
    </motion.article>
  );
};
