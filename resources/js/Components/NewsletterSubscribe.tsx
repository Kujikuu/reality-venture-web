import React from 'react';
import { CheckCircle2, Send } from 'lucide-react';
import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

interface NewsletterSubscribeProps {
  heading?: string;
  description?: string;
  badge?: string;
  backgroundImage?: string;
  className?: string;
  sectionId?: string;
}

const DEFAULT_BACKGROUND = '/assets/images/newsletter-bg.jpg';

export const NewsletterSubscribe = ({
  heading,
  description,
  badge,
  backgroundImage = DEFAULT_BACKGROUND,
  className = '',
  sectionId,
}: NewsletterSubscribeProps) => {
  const { t } = useTranslation(['navigation', 'common']);
  const { data, setData, post, processing, errors, recentlySuccessful, reset } = useForm({
    fullname: '',
    email: '',
    phone: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/newsletter/subscribe', {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => reset(),
    });
  };

  const displayHeading = heading ?? t('navigation:footer.newsletter.heading');
  const displayDescription = description ?? t('navigation:footer.newsletter.description');
  const displayBadge = badge ?? t('common:status.noSpam');

  return (
    <section id={sectionId} className={`scroll-mt-24 px-4 py-12 sm:px-8 sm:py-16 lg:p-16 ${className}`}>
      <div className="relative overflow-hidden rounded-2xl max-w-7xl mx-auto py-16 px-6 sm:py-20 sm:px-10 lg:py-24 lg:px-16">
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{ backgroundImage: `url(${backgroundImage})` }}
          aria-hidden="true"
        />
        {/* <div
          className="absolute inset-0 bg-linear-to-b from-black/60 via-black/50 to-black/60"
          aria-hidden="true"
        /> */}

        <div className="relative flex flex-col gap-4 backdrop-blur bg-white/10 border border-white/20 rounded-2xl p-6 sm:p-10 lg:p-12 text-center max-w-2xl mx-auto">

          {/* {displayBadge && (
            <div className="inline-flex items-center gap-2 text-white/90 font-bold text-xs tracking-widest uppercase mb-6 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-md border border-white/20">
              <Send className="w-3 h-3" /> {displayBadge}
            </div>
          )} */}

          <h2 className="text-base sm:text-lg font-semibold text-gray-600 mb-4 sm:mb-6 tracking-tight leading-tight">
            {displayHeading}
          </h2>

          {/* <p className="text-white/80 text-base sm:text-lg mb-8 sm:mb-10 max-w-lg mx-auto leading-relaxed">
            {displayDescription}
          </p> */}

          {recentlySuccessful ? (
            <div className="flex items-center justify-center gap-2 text-green-300 font-medium">
              <CheckCircle2 className="w-5 h-5" />
              {t('navigation:footer.newsletter.success')}
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="max-w-2xl w-full mx-auto">
              <div className="flex flex-col gap-3 w-full">
                <div>
                  <input
                    type="text"
                    value={data.fullname}
                    onChange={(e) => setData('fullname', e.target.value)}
                    placeholder={t('navigation:footer.newsletter.placeholder')}
                    aria-label={t('navigation:footer.newsletter.placeholder')}
                    className="w-full px-4 py-3 rounded-md bg-black/10 border border-black/20 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-black/40 outline-none text-sm backdrop-blur-sm"
                  />
                  {errors.fullname && (
                    <p className="text-red-500 text-sm mt-2 text-start">{errors.fullname}</p>
                  )}
                </div>
                <div className='flex flex-col sm:flex-row gap-3 w-full'>
                  <div className="flex-1">
                    <input
                      type="email"
                      value={data.email}
                      onChange={(e) => setData('email', e.target.value)}
                      placeholder={t('navigation:footer.newsletter.placeholder')}
                      aria-label={t('navigation:footer.newsletter.placeholder')}
                      className="w-full px-4 py-3 rounded-md bg-black/10 border border-black/20 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-black/40 outline-none text-sm backdrop-blur-sm"
                    />
                    {errors.email && (
                      <p className="text-red-500 text-sm mt-2 text-start">{errors.email}</p>
                    )}
                  </div>
                  <div className="flex-1">
                    <input
                      type="tel"
                      inputMode="tel"
                      value={data.phone}
                      onChange={(e) => setData('phone', e.target.value)}
                      placeholder={t('common:newsletter.phone.placeholder')}
                      aria-label={t('common:newsletter.phone.label')}
                      className="w-full px-4 py-3 rounded-md bg-black/10 border border-black/20 text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-black/40 outline-none text-sm backdrop-blur-sm"
                    />
                    {errors.phone && (
                      <p className="text-red-500 text-sm mt-2 text-start">{errors.phone}</p>
                    )}
                  </div>
                </div>
                <button
                  type="submit"
                  disabled={processing}
                  className="w-full px-8 py-3 bg-primary hover:bg-primary-800 text-white font-bold rounded-md transition-all whitespace-nowrap inline-flex items-center justify-center gap-2 disabled:opacity-50"
                >
                  <Send className="w-4 h-4" />
                  {t('navigation:footer.newsletter.subscribe')}
                </button>
              </div>
            </form>
          )}
        </div>
      </div>
    </section>
  );
};
