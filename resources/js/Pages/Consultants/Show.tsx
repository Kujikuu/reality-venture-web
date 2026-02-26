import { Head, Link, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Star, Clock, Globe, MapPin, MessageCircle, Info } from 'lucide-react';
import CalendlyWidget from '../../Components/CalendlyWidget';
import type { ConsultantDetail, ReviewItem, PageProps } from '../../types/marketplace';
import i18next from 'i18next';

interface Props {
  consultant: ConsultantDetail;
  reviews: ReviewItem[];
}

const languageNames: Record<string, { en: string; ar: string }> = {
  en: { en: 'English', ar: 'الإنجليزية' },
  ar: { en: 'Arabic', ar: 'العربية' },
  fr: { en: 'French', ar: 'الفرنسية' },
  es: { en: 'Spanish', ar: 'الإسبانية' },
  de: { en: 'German', ar: 'الألمانية' },
  zh: { en: 'Chinese', ar: 'الصينية' },
  ja: { en: 'Japanese', ar: 'اليابانية' },
  ko: { en: 'Korean', ar: 'الكورية' },
  hi: { en: 'Hindi', ar: 'الهندية' },
  ur: { en: 'Urdu', ar: 'الأردية' },
  tr: { en: 'Turkish', ar: 'التركية' },
  pt: { en: 'Portuguese', ar: 'البرتغالية' },
  ru: { en: 'Russian', ar: 'الروسية' },
  it: { en: 'Italian', ar: 'الإيطالية' },
};

const getLanguageName = (code: string, locale: string): string => {
  const entry = languageNames[code.toLowerCase()];
  if (!entry) return code;
  return locale === 'ar' ? entry.ar : entry.en;
};

export default function ConsultantShow({ consultant, reviews }: Props) {
  const { t } = useTranslation('consultants');
  const { auth } = usePage<PageProps>().props;
  const lang = i18next.language;

  const bio = lang === 'ar' && consultant.bio_ar ? consultant.bio_ar : consultant.bio_en;
  const isClient = auth.user?.role === 'client';
  const isLoggedIn = !!auth.user;

  const handleBooked = (eventUuid: string) => {
    router.visit(`/bookings/${eventUuid}/pay`);
  };

  return (
    <>
      <Head title={consultant.name} />
      <div className="bg-gray-50 min-h-screen">
        <div className="max-w-[1440px] mx-auto px-6 lg:px-12 py-10">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {/* Left Column */}
            <div className="lg:col-span-7 space-y-8">
              {/* Hero Card */}
              <div className="bg-white border border-gray-200 rounded-2xl p-8">
                <div className="flex items-start gap-5">
                  {consultant.avatar_url ? (
                    <img
                      src={consultant.avatar_url}
                      alt={consultant.name}
                      className="w-20 h-20 rounded-full object-cover ring-3 ring-primary/20 shrink-0"
                    />
                  ) : (
                    <div className="w-20 h-20 rounded-full bg-primary/10 ring-3 ring-primary/20 flex items-center justify-center text-primary font-bold text-2xl shrink-0">
                      {consultant.name.charAt(0)}
                    </div>
                  )}
                  <div>
                    <h1 className="text-2xl font-bold text-gray-900">{consultant.name}</h1>
                    <div className="flex items-center gap-3 mt-2 flex-wrap">
                      <div className="flex items-center gap-1">
                        <Star className="w-4 h-4 text-secondary fill-secondary" />
                        <span className="text-sm font-semibold">{consultant.average_rating}</span>
                        <span className="text-xs text-gray-400">({consultant.total_reviews})</span>
                      </div>
                      <span className="text-gray-300">|</span>
                      <div className="flex items-center gap-1 text-sm text-gray-500">
                        <Clock className="w-3.5 h-3.5" />
                        {consultant.years_experience} {t('show.experience')}
                      </div>
                      <span className="text-gray-300">|</span>
                      <div className="flex items-center gap-1 text-sm text-gray-500">
                        <MapPin className="w-3.5 h-3.5" />
                        {consultant.timezone}
                      </div>
                    </div>
                    <div className="flex flex-wrap gap-1.5 mt-3">
                      {consultant.specializations.map((spec) => (
                        <span key={spec.id} className="px-2.5 py-1 bg-primary/5 text-primary text-xs font-medium rounded-full">
                          {lang === 'ar' && spec.name_ar ? spec.name_ar : spec.name_en}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              {/* Bio */}
              <div className="bg-white border border-gray-200 rounded-2xl p-8">
                <h2 className="text-lg font-bold text-gray-900 mb-4">{t('show.about')}</h2>
                <p className="text-gray-600 leading-relaxed whitespace-pre-line">{bio}</p>

                {consultant.languages && consultant.languages.length > 0 && (
                  <div className="mt-6 pt-6 border-t border-gray-100">
                    <div className="flex items-center gap-2 text-sm text-gray-500">
                      <Globe className="w-4 h-4" />
                      <span className="font-medium">{t('show.languages')}:</span>
                      {consultant.languages.map(l => getLanguageName(l, lang)).join(', ')}
                    </div>
                  </div>
                )}

                <div className="mt-4 flex items-center gap-2 text-sm text-gray-500">
                  <MessageCircle className="w-4 h-4" />
                  <span className="font-medium">{t('show.responseTime')}:</span>
                  {consultant.response_time_hours}h
                </div>
              </div>

              {/* Reviews */}
              <div className="bg-white border border-gray-200 rounded-2xl p-8">
                <h2 className="text-lg font-bold text-gray-900 mb-6">{t('show.reviews')} ({consultant.total_reviews})</h2>
                {reviews.length === 0 ? (
                  <p className="text-gray-400 text-sm">{t('show.noReviews')}</p>
                ) : (
                  <div className="space-y-6">
                    {reviews.map((review) => (
                      <div key={review.id} className="border-b border-gray-100 pb-5 last:border-0 last:pb-0">
                        <div className="flex items-center justify-between mb-2">
                          <div className="flex items-center gap-2">
                            <span className="font-semibold text-sm text-gray-900">{review.reviewer_name}</span>
                            <div className="flex items-center gap-0.5">
                              {Array.from({ length: 5 }).map((_, i) => (
                                <Star key={i} className={`w-3.5 h-3.5 ${i < review.rating ? 'text-secondary fill-secondary' : 'text-gray-200'}`} />
                              ))}
                            </div>
                          </div>
                          <span className="text-xs text-gray-400">
                            {new Date(review.created_at).toLocaleDateString()}
                          </span>
                        </div>
                        {review.comment && <p className="text-sm text-gray-600">{review.comment}</p>}
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>

            {/* Right Column - Booking Card */}
            <div className="lg:col-span-5">
              <div className="sticky top-24">
                <div className="bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
                  <div className="p-6 border-b border-gray-100">
                    <div className="flex items-center justify-between">
                      <h3 className="font-bold text-gray-900">{t('show.bookingCard')}</h3>
                      <div className="text-end">
                        <span className="text-2xl font-bold text-secondary">{consultant.hourly_rate}</span>
                        <span className="text-sm text-gray-400"> SAR/hr</span>
                      </div>
                    </div>
                  </div>

                  <div className="p-6">
                    {isClient && consultant.calendly_event_type_url ? (
                      <CalendlyWidget
                        url={consultant.calendly_event_type_url}
                        onBooked={handleBooked}
                        prefillName={auth.user?.name}
                        prefillEmail={auth.user?.email}
                      />
                    ) : auth.user?.role === 'consultant' ? (
                      <div className="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
                        <Info className="w-5 h-5 shrink-0" />
                        {t('show.consultantNotice')}
                      </div>
                    ) : (
                      <div className="text-center py-8">
                        <p className="text-gray-500 mb-4">{t('show.loginToBook')}</p>
                        <Link
                          href={`/login?intended=/consultants/${consultant.slug}`}
                          className="inline-flex h-12 px-8 items-center justify-center bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors"
                        >
                          Login
                        </Link>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
