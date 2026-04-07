import { Link, router } from '@inertiajs/react';
import { SEO } from '../../Components/SEO';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { Star, Clock, Users } from 'lucide-react';
import { staggerContainer, cardVariants } from '../../Components/animations/CommonAnimations';
import type { ConsultantCard, Specialization, PaginatedData } from '../../types/marketplace';
import i18next from 'i18next';
import { Button } from '../../Components/ui/Button';
import { SarIcon } from '../../Components/ui/SarIcon';

interface Props {
  consultants: PaginatedData<ConsultantCard>;
  specializations: Specialization[];
  filters: { specialization?: string };
}

export default function ConsultantsIndex({ consultants, specializations, filters }: Props) {
  const { t } = useTranslation('consultants');
  const lang = i18next.language;

  const handleFilter = (specializationId: string | null) => {
    router.get('/consultants', specializationId ? { specialization: specializationId } : {}, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  return (
    <>
      <SEO />
      <div className="bg-white">
        {/* Hero */}
        <section className="bg-linear-to-br from-primary/5 via-white to-secondary/5 py-16 lg:py-24">
          <div className="max-w-7xl mx-auto px-6 lg:px-12 text-center">
            <h1 className="text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 mb-4">{t('index.title')}</h1>
            <p className="text-lg text-gray-500 max-w-2xl mx-auto">{t('index.subtitle')}</p>
          </div>
        </section>

        {/* Advisor CTA Banner */}
        <section className="bg-gray-50 border-y border-gray-200">
          <div className="max-w-7xl mx-auto px-6 lg:px-12 py-8 lg:py-10 flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-12">
            <div className="flex-1 min-w-0">
              <h2 className="text-xl lg:text-2xl font-bold text-gray-900 mb-1">{t('advisorCta.title')}</h2>
              <p className="text-sm lg:text-base text-gray-500 leading-relaxed">{t('advisorCta.description')}</p>
            </div>
            <Link href="/register" className="shrink-0">
              <Button variant="primary" className="rounded-xl px-8 py-4" withArrow>
                {t('advisorCta.cta')}
              </Button>
            </Link>
          </div>
        </section>

        {/* Content */}
        <section className="max-w-7xl mx-auto px-6 lg:px-12 py-12">
          <div className="flex flex-col lg:flex-row gap-8">
            {/* Sidebar Filters */}
            <aside className="lg:w-64 shrink-0">
              <h3 className="text-xs font-bold uppercase tracking-wide text-gray-500 mb-3">{t('index.filterBySpecialization')}</h3>
              <div className="flex flex-wrap lg:flex-col gap-2">
                <button
                  onClick={() => handleFilter(null)}
                  className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                    !filters.specialization
                      ? 'bg-primary text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {t('index.allSpecializations')}
                </button>
                {specializations.map((spec) => (
                  <button
                    key={spec.id}
                    onClick={() => handleFilter(String(spec.id))}
                    className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                      filters.specialization === String(spec.id)
                        ? 'bg-primary text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                    }`}
                  >
                    {lang === 'ar' && spec.name_ar ? spec.name_ar : spec.name_en}
                  </button>
                ))}
              </div>
            </aside>

            {/* Consultant Grid */}
            <div className="flex-1">
              {consultants.data.length === 0 ? (
                <div className="text-center py-20">
                  <Users className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                  <h3 className="text-lg font-semibold text-gray-600">{t('index.noResults')}</h3>
                  <p className="text-sm text-gray-400">{t('index.noResultsDesc')}</p>
                </div>
              ) : (
                <motion.div
                  className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6"
                  variants={staggerContainer}
                  initial="hidden"
                  animate="visible"
                >
                  {consultants.data.map((consultant) => (
                    <motion.div key={consultant.id} variants={cardVariants} className="h-full">
                      <Link
                        href={`/consultants/${consultant.slug}`}
                        className="flex h-full flex-col bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-primary/20 transition-all group"
                      >
                        {/* Avatar + Name */}
                        <div className="flex items-start gap-4 mb-4">
                          {consultant.avatar_url ? (
                            <img
                              src={consultant.avatar_url}
                              alt={consultant.user.name}
                              className="w-14 h-14 rounded-full object-cover ring-2 ring-primary/20 shrink-0"
                            />
                          ) : (
                            <div className="w-14 h-14 rounded-full bg-primary/10 ring-2 ring-primary/20 flex items-center justify-center text-primary font-bold text-lg shrink-0">
                              {consultant.user.name.charAt(0)}
                            </div>
                          )}
                          <div className="min-w-0">
                            <h3 className="font-bold text-gray-900 group-hover:text-primary transition-colors truncate">
                              {consultant.user.name}
                            </h3>
                            <div className="flex items-center gap-1 mt-0.5">
                              <Star className="w-3.5 h-3.5 text-secondary fill-secondary" />
                              <span className="text-sm font-medium text-gray-700">{consultant.average_rating}</span>
                              <span className="text-xs text-gray-400">({consultant.total_reviews} {t('index.reviews')})</span>
                            </div>
                          </div>
                        </div>

                        {/* Specializations */}
                        <div className="flex flex-wrap gap-1.5 mb-4 grow content-start">
                          {consultant.specializations.slice(0, 3).map((spec) => (
                            <span key={spec.id} className="px-2.5 py-1 bg-primary/5 text-primary text-xs font-medium rounded-full">
                              {lang === 'ar' && spec.name_ar ? spec.name_ar : spec.name_en}
                            </span>
                          ))}
                        </div>

                        {/* Footer */}
                        <div className="flex items-center justify-between pt-4 border-t border-gray-100">
                          <div className="flex items-center gap-1 text-xs text-gray-500">
                            <Clock className="w-3.5 h-3.5" />
                            <span>{consultant.years_experience} {t('index.yearsExp')}</span>
                          </div>
                          <span className="flex justify-center items-center gap-1 px-3 py-1 bg-secondary/10 text-secondary font-bold text-sm rounded-full">
                            <SarIcon className='h-[0.9em]!' /> {consultant.hourly_rate}{t('index.perHour')}
                          </span>
                        </div>
                      </Link>
                    </motion.div>
                  ))}
                </motion.div>
              )}

              {/* Pagination */}
              {consultants.last_page > 1 && (
                <div className="flex justify-center gap-2 mt-10">
                  {Array.from({ length: consultants.last_page }, (_, i) => i + 1).map((page) => (
                    <Link
                      key={page}
                      href={`/consultants?page=${page}${filters.specialization ? `&specialization=${filters.specialization}` : ''}`}
                      className={`w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors ${
                        page === consultants.current_page
                          ? 'bg-primary text-white'
                          : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                      }`}
                    >
                      {page}
                    </Link>
                  ))}
                </div>
              )}
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
