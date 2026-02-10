import { Head, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { ArrowLeft, Calendar, Hash, User, Share2 } from 'lucide-react';
import DOMPurify from 'dompurify';
import { sectionVariants, staggerContainer } from '../Components/animations/CommonAnimations';
import { BlogCard } from '../Components/BlogCard';
import type { BlogPost as BlogPostType } from '../types';

interface BlogPostPageProps {
  post: BlogPostType;
  relatedPosts: BlogPostType[];
}

export default function BlogPost({ post, relatedPosts }: BlogPostPageProps) {
  const { t, i18n } = useTranslation('blog');
  const isArabic = i18n.language === 'ar';

  const title = isArabic ? post.title_ar : post.title_en;
  const content = isArabic ? post.content_ar : post.content_en;
  const excerpt = isArabic ? (post.excerpt_ar || post.excerpt_en) : (post.excerpt_en || post.excerpt_ar);
  const categoryName = post.category
    ? (isArabic ? post.category.name_ar : post.category.name_en)
    : null;

  const formattedDate = new Date(post.published_at).toLocaleDateString(
    isArabic ? 'ar-SA' : 'en-US',
    { year: 'numeric', month: 'long', day: 'numeric' }
  );

  const sanitizedContent = DOMPurify.sanitize(content || '');

  const handleShare = async () => {
    try {
      const shareData = {
        title: title,
        text: excerpt || title,
        url: window.location.href,
      };

      if (navigator.share) {
        await navigator.share(shareData);
      } else {
        await navigator.clipboard.writeText(window.location.href);
      }
    } catch {
      // User cancelled share or clipboard access denied
    }
  };

  return (
    <>
      <Head>
        <title>{post.meta_title || title}</title>
        {post.meta_description && <meta name="description" content={post.meta_description} />}
        {post.og_image && <meta property="og:image" content={post.og_image} />}
        <meta property="og:title" content={post.meta_title || title} />
        {post.meta_description && <meta property="og:description" content={post.meta_description} />}
      </Head>

      {/* Hero / Header */}
      <article>
        <section className="relative bg-gradient-to-br from-primary-50 via-white to-secondary-50 py-16 lg:py-24 overflow-hidden">
          {/* Floating geometric shapes */}
          <div className="absolute top-10 start-10 w-20 h-20 rounded-full bg-primary/5 blur-xl" />
          <div className="absolute bottom-10 end-20 w-32 h-32 rounded-full bg-secondary/5 blur-xl" />
          <div className="absolute top-1/2 end-10 w-16 h-16 border border-secondary/10 rounded-lg rotate-12" />

          <div className="max-w-4xl mx-auto px-6 lg:px-8 relative z-10">
            <motion.div
              initial="hidden"
              animate="visible"
              variants={sectionVariants}
            >
              {/* Back Link & Category */}
              <div className="flex items-center justify-between mb-8">
                <Link
                  href="/blog"
                  className="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary transition-colors"
                >
                  <ArrowLeft className="w-4 h-4 rtl:rotate-180" />
                  {t('backToBlog')}
                </Link>

                {categoryName && (
                  <Link
                    href={`/blog?category=${post.category!.slug}`}
                    className="inline-block text-xs font-semibold text-primary bg-primary-50 px-3 py-1 rounded-full hover:bg-primary-100 transition-colors"
                  >
                    {categoryName}
                  </Link>
                )}
              </div>

              {/* Title */}
              <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 tracking-tight mb-6 leading-tight">
                {title}
              </h1>

              {/* Meta */}
              <div className="flex flex-wrap items-center gap-5 text-sm text-gray-500">
                <span className="flex items-center gap-1.5">
                  <User className="w-4 h-4" />
                  {post.author.name}
                </span>
                <span className="flex items-center gap-1.5">
                  <Calendar className="w-4 h-4" />
                  {formattedDate}
                </span>
                <button
                  onClick={handleShare}
                  className="flex items-center gap-1.5 hover:text-primary transition-colors"
                >
                  <Share2 className="w-4 h-4" />
                  {t('sharePost')}
                </button>
              </div>
            </motion.div>
          </div>
        </section>

        {/* Featured Image */}
        {post.featured_image && (
          <div className="max-w-5xl mx-auto px-6 lg:px-12 -mt-4">
            <div className="rounded-xl overflow-hidden">
              <img
                src={post.featured_image}
                alt={title}
                className="w-full aspect-[2/1] object-cover"
              />
            </div>
          </div>
        )}

        {/* Content */}
        <section className="py-16 lg:py-24">
          <div className="max-w-3xl mx-auto px-6 lg:px-8">
            <div
              className="prose prose-lg max-w-none prose-headings:text-gray-900 prose-headings:font-bold prose-p:text-gray-600 prose-p:leading-relaxed prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-img:rounded-lg"
              dangerouslySetInnerHTML={{ __html: sanitizedContent }}
            />

            {/* Tags */}
            {post.tags && post.tags.length > 0 && (
              <div className="mt-10 pt-8 border-t border-gray-100">
                <div className="flex items-center gap-2 flex-wrap">
                  <Hash className="w-4 h-4 text-gray-400" />
                  {post.tags.map((tag) => (
                    <Link
                      key={tag.slug}
                      href={`/blog?tag=${tag.slug}`}
                      className="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full hover:bg-primary-50 hover:text-primary transition-colors"
                    >
                      {isArabic ? tag.name_ar : tag.name_en}
                    </Link>
                  ))}
                </div>
              </div>
            )}
          </div>
        </section>
      </article>

      {/* Related Posts */}
      {relatedPosts.length > 0 && (
        <section className="py-24 bg-gray-50">
          <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
            <h2 className="text-2xl font-bold text-gray-900 mb-8">{t('relatedPosts')}</h2>
            <motion.div
              initial="hidden"
              whileInView="visible"
              viewport={{ once: true }}
              variants={staggerContainer}
              className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {relatedPosts.map((relatedPost) => (
                <BlogCard key={relatedPost.id} post={relatedPost} />
              ))}
            </motion.div>
          </div>
        </section>
      )}
    </>
  );
}
