import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion, AnimatePresence } from 'framer-motion';
import { Check, ArrowRight, ArrowLeft, ExternalLink } from 'lucide-react';
import type { Specialization } from '../../types/marketplace';

interface Props {
  specializations: Specialization[];
}

const LANGUAGES = [
  { value: 'en', label: 'English' },
  { value: 'ar', label: 'العربية' },
  { value: 'fr', label: 'Français' },
  { value: 'es', label: 'Español' },
  { value: 'de', label: 'Deutsch' },
  { value: 'zh', label: '中文' },
  { value: 'hi', label: 'हिन्दी' },
  { value: 'ur', label: 'اردو' },
];

const BIO_MIN = 50;

export default function Onboarding({ specializations }: Props) {
  const { t } = useTranslation('dashboard');
  const [step, setStep] = useState(1);

  const { data, setData, post, processing, errors } = useForm({
    bio_en: '',
    bio_ar: '',
    specialization_ids: [] as number[],
    years_experience: 0,
    hourly_rate: 200,
    languages: ['en'] as string[],
    timezone: 'Asia/Riyadh',
    response_time_hours: 24,
    calendly_event_type_url: '',
  });

  const steps = [
    { number: 1, label: t('onboarding.step1') },
    { number: 2, label: t('onboarding.step2') },
    { number: 3, label: t('onboarding.step3') },
  ];

  // Step validation
  const bioCount = data.bio_en.length;
  const step1Valid = bioCount >= BIO_MIN && data.specialization_ids.length > 0;
  const step2Valid = data.hourly_rate >= 50 && data.languages.length > 0;
  const step3Valid = data.calendly_event_type_url.length > 0;

  const canProceed = step === 1 ? step1Valid : step === 2 ? step2Valid : step3Valid;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/consultant/onboarding', {
      onError: (errors) => {
        if (errors.bio_en || errors.bio_ar || errors.specialization_ids || errors.years_experience) {
          setStep(1);
        } else if (errors.hourly_rate || errors.languages) {
          setStep(2);
        } else if (errors.calendly_event_type_url) {
          setStep(3);
        }
      },
    });
  };

  const toggleSpecialization = (id: number) => {
    setData('specialization_ids',
      data.specialization_ids.includes(id)
        ? data.specialization_ids.filter((s) => s !== id)
        : [...data.specialization_ids, id]
    );
  };

  const toggleLanguage = (lang: string) => {
    setData('languages',
      data.languages.includes(lang)
        ? data.languages.filter((l) => l !== lang)
        : [...data.languages, lang]
    );
  };

  const bioCountColor = bioCount === 0
    ? 'text-gray-400'
    : bioCount < BIO_MIN
      ? 'text-amber-500'
      : 'text-green-500';

  return (
    <>
      <Head title={t('onboarding.title')} />
      <div className="min-h-screen bg-gray-50 py-12 px-6">
        <div className="max-w-2xl mx-auto">
          {/* Step Indicators */}
          <div className="flex items-center justify-center mb-12">
            {steps.map((s, i) => (
              <div key={s.number} className="flex items-center">
                <div className="flex flex-col items-center">
                  <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors ${
                    step > s.number
                      ? 'bg-primary text-white'
                      : step === s.number
                        ? 'bg-primary text-white ring-4 ring-primary/20'
                        : 'bg-gray-200 text-gray-500'
                  }`}>
                    {step > s.number ? <Check className="w-5 h-5" /> : s.number}
                  </div>
                  <span className="text-xs font-medium text-gray-500 mt-2 whitespace-nowrap">{s.label}</span>
                </div>
                {i < steps.length - 1 && (
                  <div className={`w-20 h-0.5 mx-3 mb-6 ${step > s.number ? 'bg-secondary' : 'bg-gray-200'}`} />
                )}
              </div>
            ))}
          </div>

          <form onSubmit={handleSubmit}>
            {Object.keys(errors).length > 0 && (
              <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <p className="text-sm font-medium text-red-800 mb-2">{t('onboarding.fixErrors', 'Please fix the following errors:')}</p>
                <ul className="list-disc list-inside text-sm text-red-600 space-y-1">
                  {Object.values(errors).map((error, i) => (
                    <li key={i}>{error}</li>
                  ))}
                </ul>
              </div>
            )}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
              <AnimatePresence mode="wait">
                {/* Step 1: Profile Info */}
                {step === 1 && (
                  <motion.div
                    key="step1"
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: -20 }}
                    className="space-y-6"
                  >
                    <div className="space-y-1.5">
                      <div className="flex items-center justify-between">
                        <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.bioEn')} *</label>
                        <span className={`text-xs font-medium tabular-nums transition-colors ${bioCountColor}`}>
                          {bioCount}/{BIO_MIN} min
                          {bioCount >= BIO_MIN && <Check className="w-3 h-3 inline ms-1 -mt-0.5" />}
                        </span>
                      </div>
                      <textarea
                        rows={5}
                        value={data.bio_en}
                        onChange={(e) => setData('bio_en', e.target.value)}
                        placeholder={t('onboarding.bioPlaceholder', 'Tell clients about your expertise, experience, and what makes you unique...')}
                        className={`w-full p-4 bg-gray-50 border rounded-lg text-gray-900 focus:ring-1 focus:outline-none transition-all resize-none ${
                          bioCount > 0 && bioCount < BIO_MIN
                            ? 'border-amber-300 focus:border-amber-400 focus:ring-amber-200'
                            : bioCount >= BIO_MIN
                              ? 'border-green-300 focus:border-green-400 focus:ring-green-200'
                              : 'border-gray-200 focus:border-primary focus:ring-primary'
                        }`}
                      />
                      {bioCount > 0 && bioCount < BIO_MIN && (
                        <p className="text-amber-500 text-xs">{BIO_MIN - bioCount} more characters needed</p>
                      )}
                      {errors.bio_en && <p className="text-red-500 text-xs">{errors.bio_en}</p>}
                    </div>

                    <div className="space-y-1.5">
                      <label className="text-xs font-bold uppercase tracking-wide text-gray-500">
                        {t('onboarding.bioAr')} <span className="text-gray-400 normal-case">({t('onboarding.bioArOptional')})</span>
                      </label>
                      <textarea
                        rows={5}
                        value={data.bio_ar}
                        onChange={(e) => setData('bio_ar', e.target.value)}
                        className="w-full p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none"
                        dir="rtl"
                      />
                    </div>

                    <div className="space-y-1.5">
                      <div className="flex items-center justify-between">
                        <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.specializations')} *</label>
                        {data.specialization_ids.length > 0 && (
                          <span className="text-xs font-medium text-green-500">
                            {data.specialization_ids.length} selected <Check className="w-3 h-3 inline ms-0.5 -mt-0.5" />
                          </span>
                        )}
                      </div>
                      <div className="flex flex-wrap gap-2">
                        {specializations.map((spec) => (
                          <button
                            key={spec.id}
                            type="button"
                            onClick={() => toggleSpecialization(spec.id)}
                            className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                              data.specialization_ids.includes(spec.id)
                                ? 'bg-primary text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            }`}
                          >
                            {spec.name_en}
                          </button>
                        ))}
                      </div>
                      {data.specialization_ids.length === 0 && (
                        <p className="text-gray-400 text-xs">{t('onboarding.selectAtLeastOne', 'Select at least one specialization to continue')}</p>
                      )}
                      {errors.specialization_ids && <p className="text-red-500 text-xs">{errors.specialization_ids}</p>}
                    </div>

                    <div className="space-y-1.5">
                      <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.yearsExperience')}</label>
                      <input
                        type="number"
                        min={0}
                        max={50}
                        value={data.years_experience}
                        onChange={(e) => setData('years_experience', parseInt(e.target.value) || 0)}
                        className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                      />
                    </div>
                  </motion.div>
                )}

                {/* Step 2: Rates & Languages */}
                {step === 2 && (
                  <motion.div
                    key="step2"
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: -20 }}
                    className="space-y-6"
                  >
                    <div className="space-y-1.5">
                      <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.hourlyRate')}</label>
                      <div className="relative">
                        <input
                          type="number"
                          min={50}
                          max={10000}
                          value={data.hourly_rate}
                          onChange={(e) => setData('hourly_rate', parseInt(e.target.value) || 0)}
                          className="w-full h-12 px-4 pe-16 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                        />
                        <span className="absolute end-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">SAR</span>
                      </div>
                      {errors.hourly_rate && <p className="text-red-500 text-xs">{errors.hourly_rate}</p>}
                    </div>

                    <div className="space-y-1.5">
                      <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.languages')}</label>
                      <div className="flex flex-wrap gap-2">
                        {LANGUAGES.map((lang) => (
                          <button
                            key={lang.value}
                            type="button"
                            onClick={() => toggleLanguage(lang.value)}
                            className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                              data.languages.includes(lang.value)
                                ? 'bg-primary text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            }`}
                          >
                            {lang.label}
                          </button>
                        ))}
                      </div>
                      {errors.languages && <p className="text-red-500 text-xs">{errors.languages}</p>}
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div className="space-y-1.5">
                        <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.timezone')}</label>
                        <select
                          value={data.timezone}
                          onChange={(e) => setData('timezone', e.target.value)}
                          className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                        >
                          <option value="Asia/Riyadh">Asia/Riyadh (GMT+3)</option>
                          <option value="Asia/Dubai">Asia/Dubai (GMT+4)</option>
                          <option value="Asia/Bahrain">Asia/Bahrain (GMT+3)</option>
                          <option value="Asia/Qatar">Asia/Qatar (GMT+3)</option>
                          <option value="Asia/Kuwait">Asia/Kuwait (GMT+3)</option>
                          <option value="Asia/Muscat">Asia/Muscat (GMT+4)</option>
                          <option value="Asia/Baghdad">Asia/Baghdad (GMT+3)</option>
                          <option value="Asia/Amman">Asia/Amman (GMT+3)</option>
                          <option value="Asia/Beirut">Asia/Beirut (GMT+2)</option>
                          <option value="Africa/Cairo">Africa/Cairo (GMT+2)</option>
                        </select>
                      </div>
                      <div className="space-y-1.5">
                        <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.responseTime')}</label>
                        <input
                          type="number"
                          min={1}
                          max={72}
                          value={data.response_time_hours}
                          onChange={(e) => setData('response_time_hours', parseInt(e.target.value) || 24)}
                          className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                        />
                      </div>
                    </div>
                  </motion.div>
                )}

                {/* Step 3: Calendly Setup */}
                {step === 3 && (
                  <motion.div
                    key="step3"
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: -20 }}
                    className="space-y-6"
                  >
                    <div className="p-4 bg-primary/5 border border-primary/10 rounded-xl">
                      <p className="text-sm text-gray-700 leading-relaxed">
                        {t('onboarding.calendlyInstructions')}
                      </p>
                      <a
                        href="https://calendly.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center gap-1 mt-2 text-sm font-medium text-primary hover:text-primary-800"
                      >
                        Go to Calendly <ExternalLink className="w-3.5 h-3.5" />
                      </a>
                    </div>

                    <div className="space-y-1.5">
                      <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('onboarding.calendlyUrl')} *</label>
                      <input
                        type="url"
                        value={data.calendly_event_type_url}
                        onChange={(e) => setData('calendly_event_type_url', e.target.value)}
                        placeholder="https://calendly.com/yourname/30min"
                        className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                      />
                      {errors.calendly_event_type_url && <p className="text-red-500 text-xs">{errors.calendly_event_type_url}</p>}
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>

              {/* Navigation Buttons */}
              <div className="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                {step > 1 ? (
                  <button
                    type="button"
                    onClick={() => setStep(step - 1)}
                    className="flex items-center gap-2 px-6 h-12 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                  >
                    <ArrowLeft className="w-4 h-4 rtl:-scale-x-100" /> {t('onboarding.previous')}
                  </button>
                ) : <div />}

                {step < 3 ? (
                  <button
                    type="button"
                    disabled={!canProceed}
                    onClick={() => setStep(step + 1)}
                    className={`flex items-center gap-2 px-8 h-12 font-bold rounded-lg transition-colors ${
                      canProceed
                        ? 'bg-primary text-white hover:bg-primary-800'
                        : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    }`}
                  >
                    {t('onboarding.next')} <ArrowRight className="w-4 h-4 rtl:-scale-x-100" />
                  </button>
                ) : (
                  <button
                    type="submit"
                    disabled={processing || !step3Valid}
                    className={`flex items-center gap-2 px-8 h-12 font-bold rounded-lg transition-colors ${
                      !processing && step3Valid
                        ? 'bg-primary text-white hover:bg-primary-800'
                        : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    }`}
                  >
                    {t('onboarding.submit')}
                  </button>
                )}
              </div>
            </div>
          </form>
        </div>
      </div>
    </>
  );
}
