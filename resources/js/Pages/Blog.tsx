import { Head, Link, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { Search, ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';
import { sectionVariants, staggerContainer } from '../Components/animations/CommonAnimations';
import { BlogCard } from '../Components/BlogCard';
import type { BlogPost, BlogCategory, PaginatedData } from '../types';

interface BlogProps {
  posts: PaginatedData<BlogPost>;
  categories: BlogCategory[];
  filters: {
    category: string | null;
    tag: string | null;
    search: string | null;
  };
}

export default function Blog({ posts, categories, filters }: BlogProps) {
  const { t, i18n } = useTranslation('blog');
  const isArabic = i18n.language === 'ar';
  const [searchValue, setSearchValue] = useState(filters.search || '');

  const handleCategoryFilter = (categorySlug: string | null) => {
    router.get('/blog', {
      ...(categorySlug ? { category: categorySlug } : {}),
      ...(filters.search ? { search: filters.search } : {}),
    }, {
      preserveScroll: false,
    });
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get('/blog', {
      ...(searchValue ? { search: searchValue } : {}),
      ...(filters.category ? { category: filters.category } : {}),
    }, {
      preserveScroll: false,
    });
  };

  return (
    <>
      <Head title={t('pageTitle')} />

      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-primary-50 via-white to-secondary-50 py-20 lg:py-28 overflow-hidden">
        {/* Floating geometric shapes */}
        <div className="absolute top-10 start-10 w-20 h-20 rounded-full bg-primary/5 blur-xl" />
        <div className="absolute bottom-10 end-20 w-32 h-32 rounded-full bg-secondary/5 blur-xl" />
        <div className="absolute top-1/2 end-10 w-16 h-16 border border-secondary/10 rounded-lg rotate-12" />

        <div className="max-w-[1440px] mx-auto px-6 lg:px-8 text-center relative z-10">
          <motion.div
            initial="hidden"
            animate="visible"
            variants={sectionVariants}
          >
            <span className="text-primary font-bold tracking-wider text-xs uppercase mb-4 block">{t('heroTitle')}</span>
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 tracking-tight mb-6">
              {t('heroTitle')}
            </h1>
            <p className="text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
              {t('heroSubtitle')}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Filters & Content */}
      <section className="py-24">
        <div className="max-w-[1440px] mx-auto px-6 lg:px-8">

          {/* Search & Category Filters */}
          <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-10">

            {/* Category Pills */}
            <div className="flex flex-wrap items-center gap-2">
              <button
                onClick={() => handleCategoryFilter(null)}
                className={`px-4 py-2 rounded-full text-sm font-medium transition-all ${
                  !filters.category && !filters.tag
                    ? 'bg-primary text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                }`}
              >
                {t('allCategories')}
              </button>
              {categories.map((category) => (
                <button
                  key={category.slug}
                  onClick={() => handleCategoryFilter(category.slug)}
                  className={`px-4 py-2 rounded-full text-sm font-medium transition-all ${
                    filters.category === category.slug
                      ? 'bg-primary text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {isArabic ? category.name_ar : category.name_en}
                  <span className="ms-1.5 text-xs opacity-70">({category.posts_count})</span>
                </button>
              ))}
            </div>

            {/* Search */}
            <form onSubmit={handleSearch} className="relative w-full md:w-auto">
              <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input
                type="text"
                value={searchValue}
                onChange={(e) => setSearchValue(e.target.value)}
                placeholder={t('searchPlaceholder')}
                className="w-full md:w-64 h-10 ps-10 pe-4 rounded-full border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
              />
            </form>
          </div>

          {/* Posts Grid */}
          {posts.data.length > 0 ? (
            <motion.div
              initial="hidden"
              animate="visible"
              variants={staggerContainer}
              className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {posts.data.map((post) => (
                <BlogCard key={post.id} post={post} />
              ))}
            </motion.div>
          ) : (
            <div className="text-center py-20">
              <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <Search className="w-6 h-6 text-gray-400" />
              </div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">{t('noPostsFound')}</h3>
              <p className="text-gray-500">{t('noPostsDescription')}</p>
            </div>
          )}

          {/* Pagination */}
          {posts.last_page > 1 && (
            <div className="flex items-center justify-center gap-2 mt-12">
              {posts.prev_page_url ? (
                <Link
                  href={posts.prev_page_url}
                  className="flex items-center gap-1 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:border-primary/30 hover:text-primary transition-colors"
                >
                  <ChevronLeft className="w-4 h-4 rtl:rotate-180" />
                </Link>
              ) : (
                <span className="flex items-center gap-1 px-4 py-2 rounded-lg border border-gray-100 text-sm text-gray-300">
                  <ChevronLeft className="w-4 h-4 rtl:rotate-180" />
                </span>
              )}

              <span className="px-4 py-2 text-sm font-medium text-gray-600">
                {posts.current_page} / {posts.last_page}
              </span>

              {posts.next_page_url ? (
                <Link
                  href={posts.next_page_url}
                  className="flex items-center gap-1 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:border-primary/30 hover:text-primary transition-colors"
                >
                  <ChevronRight className="w-4 h-4 rtl:rotate-180" />
                </Link>
              ) : (
                <span className="flex items-center gap-1 px-4 py-2 rounded-lg border border-gray-100 text-sm text-gray-300">
                  <ChevronRight className="w-4 h-4 rtl:rotate-180" />
                </span>
              )}
            </div>
          )}
        </div>
      </section>
    </>
  );
}
