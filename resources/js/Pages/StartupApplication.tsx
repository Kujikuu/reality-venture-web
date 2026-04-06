import { useEffect } from 'react';
import { Button } from '../Components/ui/Button';
import { Select } from '../Components/ui/Select';
import { Mail, CheckCircle2, MessageCircle } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { heroContainerVariants, heroItemVariants } from '../Components/animations/HeroAnimations';
import { useForm } from '@inertiajs/react';
import { SEO } from '../Components/SEO';
import { COUNTRIES } from '../data/countries';

const INDUSTRY_KEYS = [
  'fintech',
  'healthtech',
  'edtech',
  'ecommerce',
  'saas',
  'ai',
  'logistics',
  'proptech',
  'foodtech',
  'cleantech',
  'gaming',
  'other',
];

const FUNDING_ROUND_KEYS = [
  'bootstrapped',
  'pre_seed',
  'seed',
  'series_a',
  'series_b',
  'series_c_plus',
];

const DISCOVERY_SOURCE_KEYS = [
  'linkedin',
  'referral',
  'event',
  'website',
  'social_media',
  'news_press',
  'other',
];

const CURRENT_YEAR = new Date().getFullYear();
const YEARS = Array.from({ length: 30 }, (_, i) => CURRENT_YEAR - i);
const MONTHS = Array.from({ length: 12 }, (_, i) => i + 1);

export default function StartupApplication() {
  const { t, i18n } = useTranslation(['common', 'navigation', 'startup-application']);
  const isArabic = i18n.language === 'ar';

  const { data, setData, post, processing, errors, recentlySuccessful, reset } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    linkedin_profile: '',
    company_name: '',
    number_of_founders: 1,
    hq_country: '',
    website_link: '',
    founded_date: '',
    founded_month: '',
    founded_year: '',
    industry: '',
    industry_other: '',
    company_description: '',
    current_funding_round: '',
    investment_ask_sar: '',
    valuation_sar: '',
    previous_funding: '',
    demo_link: '',
    discovery_source: '',
    referral_name: '',
    referral_param: '',
  });

  useEffect(() => {
    const ref = new URLSearchParams(window.location.search).get('ref');
    if (ref) {
      setData('referral_param', ref);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    if (data.founded_month && data.founded_year) {
      const month = String(data.founded_month).padStart(2, '0');
      setData('founded_date', `${data.founded_year}-${month}-01`);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data.founded_month, data.founded_year]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/startup-applications', {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => reset(),
    });
  };

  const countryOptions = COUNTRIES.map((c) => ({
    value: c.code,
    label: isArabic ? c.name_ar : c.name_en,
  }));

  const industryOptions = INDUSTRY_KEYS.map((key) => ({
    value: key,
    label: t(`startup-application:industries.${key}`),
  }));

  const fundingRoundOptions = FUNDING_ROUND_KEYS.map((key) => ({
    value: key,
    label: t(`startup-application:fundingRounds.${key}`),
  }));

  const discoverySourceOptions = DISCOVERY_SOURCE_KEYS.map((key) => ({
    value: key,
    label: t(`startup-application:discoverySources.${key}`),
  }));

  const monthOptions = MONTHS.map((m) => ({
    value: String(m),
    label: t(`startup-application:months.${m}`),
  }));

  const yearOptions = YEARS.map((y) => ({ value: String(y), label: String(y) }));

  const descriptionLength = data.company_description.length;

  const errorText = (field: string, errorKey?: string) =>
    errorKey ? t('startup-application:' + errorKey, errorKey) : '';

  return (
    <>
      <SEO />
      <div className="flex flex-col min-h-screen bg-white">
        {/* Hero Section */}
        <section className="relative overflow-hidden pt-24 pb-20">
          {/* <div className="absolute inset-0 hero-gradient -z-10" />
          <div className="absolute inset-0 overflow-hidden pointer-events-none -z-10">
            <div className="shape absolute top-20 left-10 w-20 h-20 border-2 border-primary/10 rotate-45" />
            <div className="shape absolute top-40 right-20 w-16 h-16 rounded-full bg-primary-50/50" />
            <div className="shape absolute bottom-20 left-1/4 w-12 h-12 border border-primary/20" />
          </div> */}

          <div className="relative max-w-7xl mx-auto px-6 lg:px-12">
            <motion.div
              className="flex items-center"
              variants={heroContainerVariants}
              initial="hidden"
              animate="visible"
            >
              <div>
                {/* <motion.span
                  variants={heroItemVariants}
                  className="inline-block py-1 px-3 rounded-md bg-primary-50 text-primary text-xs font-bold tracking-wide mb-6 w-fit uppercase"
                >
                  {t('startup-application:hero.badge')}
                </motion.span> */}

                <motion.h1
                  variants={heroItemVariants}
                  className="flex flex-wrap gap-3 text-5xl md:text-7xl font-bold tracking-tight text-gray-900 leading-[1.1] mb-6"
                >
                  {t('startup-application:hero.title')}
                  <span className="text-primary">{t('startup-application:hero.titleHighlighted')}</span>
                </motion.h1>

                <motion.p
                  variants={heroItemVariants}
                  className="text-gray-500 text-lg max-w-2xl mb-8 leading-relaxed"
                >
                  {t('startup-application:hero.subtitle')}
                </motion.p>

                {/* <motion.div
                  variants={heroItemVariants}
                  className="flex flex-wrap gap-6 text-xs font-bold uppercase tracking-widest text-gray-400"
                >
                  <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('startup-application:hero.rollingReview')}</span>
                  <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('startup-application:hero.responseTime')}</span>
                  <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('startup-application:hero.founderFirst')}</span>
                </motion.div> */}
              </div>

              {/* <motion.div
                variants={heroItemVariants}
                className="bg-white/80 backdrop-blur-sm border border-gray-100 shadow-xl shadow-primary-500/5 p-8 lg:p-10 rounded-3xl relative overflow-hidden"
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-primary-50 rounded-full blur-3xl -z-10 opacity-50"></div>
                <h2 className="text-xl font-bold uppercase tracking-tight mb-6 text-gray-900">{t('startup-application:sidebar.title')}</h2>
                <ul className="space-y-4 text-sm text-gray-600">
                  <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('startup-application:sidebar.reviewProcess')}</li>
                  <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('startup-application:sidebar.ndaPolicy')}</li>
                  <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('startup-application:sidebar.responseTime')}</li>
                </ul>
                <div className="mt-8 pt-6 border-t border-gray-100 space-y-3 text-xs uppercase tracking-widest text-gray-400">
                  <div className="flex items-center gap-3">
                    <Mail className="w-4 h-4 text-primary" /> {t('startup-application:sidebar.email')}
                  </div>
                  <div className="flex items-center gap-3">
                    <MessageCircle className="w-4 h-4 text-primary" /> {t('startup-application:sidebar.whatsapp')}
                  </div>
                </div>
              </motion.div> */}
            </motion.div>
          </div>
        </section>

        {/* Form Section */}
        <section className="flex-1 py-20 px-6 lg:px-12 bg-gray-50/50">
          <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-[1.4fr_0.6fr] gap-10">
            <div className="bg-white border border-gray-200 p-8 md:p-12 rounded-xl shadow-sm">
              <div className="flex items-center justify-between mb-10">
                <h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight text-gray-900">{t('startup-application:form.title')}</h2>
                {recentlySuccessful && (
                  <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg text-sm">
                    {t('startup-application:form.success')}
                  </div>
                )}
              </div>
              <form onSubmit={handleSubmit} className="space-y-12">
                {/* Section 1: Founder Info */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.founder')}
                  </h3>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <label htmlFor="firstName" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.firstName')}</label>
                      <input
                        type="text"
                        id="firstName"
                        value={data.first_name}
                        onChange={(e) => setData('first_name', e.target.value)}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                      />
                      {errors.first_name && <p className="text-red-500 text-xs mt-1">{errorText('first_name', errors.first_name)}</p>}
                    </div>
                    <div className="space-y-2">
                      <label htmlFor="lastName" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.lastName')}</label>
                      <input
                        type="text"
                        id="lastName"
                        value={data.last_name}
                        onChange={(e) => setData('last_name', e.target.value)}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                      />
                      {errors.last_name && <p className="text-red-500 text-xs mt-1">{errorText('last_name', errors.last_name)}</p>}
                    </div>
                  </div>

                  <div className="space-y-2">
                    <label htmlFor="email" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.email')}</label>
                    <input
                      type="email"
                      id="email"
                      value={data.email}
                      onChange={(e) => setData('email', e.target.value)}
                      className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                      placeholder={t('startup-application:form.emailPlaceholder')}
                    />
                    {errors.email && <p className="text-red-500 text-xs mt-1">{errorText('email', errors.email)}</p>}
                  </div>

                  <div className="space-y-2">
                    <label htmlFor="linkedin" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.linkedin')}</label>
                    <input
                      type="text"
                      id="linkedin"
                      value={data.linkedin_profile}
                      onChange={(e) => setData('linkedin_profile', e.target.value)}
                      className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                      placeholder={t('startup-application:form.linkedinPlaceholder')}
                    />
                    {errors.linkedin_profile && <p className="text-red-500 text-xs mt-1">{errorText('linkedin_profile', errors.linkedin_profile)}</p>}
                  </div>
                </div>

                {/* Section 2: Company Details */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.company')}
                  </h3>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <label htmlFor="companyName" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.companyName')}</label>
                      <input
                        type="text"
                        id="companyName"
                        value={data.company_name}
                        onChange={(e) => setData('company_name', e.target.value)}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                        placeholder={t('startup-application:form.companyNamePlaceholder')}
                      />
                      {errors.company_name && <p className="text-red-500 text-xs mt-1">{errorText('company_name', errors.company_name)}</p>}
                    </div>
                    <div className="space-y-2">
                      <label htmlFor="numFounders" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.numberOfFounders')}</label>
                      <input
                        type="number"
                        id="numFounders"
                        min={1}
                        max={20}
                        value={data.number_of_founders}
                        onChange={(e) => setData('number_of_founders', Number(e.target.value))}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                      />
                      {errors.number_of_founders && <p className="text-red-500 text-xs mt-1">{errorText('number_of_founders', errors.number_of_founders)}</p>}
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <label htmlFor="hqCountry" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.hqCountry')}</label>
                      <Select
                        id="hqCountry"
                        value={data.hq_country}
                        onChange={(e) => setData('hq_country', e.target.value)}
                        options={countryOptions}
                        placeholder={t('startup-application:form.hqCountryPlaceholder')}
                      />
                      {errors.hq_country && <p className="text-red-500 text-xs mt-1">{errorText('hq_country', errors.hq_country)}</p>}
                    </div>
                    <div className="space-y-2">
                      <label htmlFor="website" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.websiteLink')}</label>
                      <input
                        type="text"
                        id="website"
                        value={data.website_link}
                        onChange={(e) => setData('website_link', e.target.value)}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                        placeholder={t('startup-application:form.websiteLinkPlaceholder')}
                      />
                      {errors.website_link && <p className="text-red-500 text-xs mt-1">{errorText('website_link', errors.website_link)}</p>}
                    </div>
                  </div>

                  <div className="space-y-2">
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.foundedDate')}</label>
                    <div className="grid grid-cols-2 gap-6">
                      <Select
                        value={data.founded_month}
                        onChange={(e) => setData('founded_month', e.target.value)}
                        options={monthOptions}
                        placeholder={t('startup-application:form.foundedDateMonth')}
                      />
                      <Select
                        value={data.founded_year}
                        onChange={(e) => setData('founded_year', e.target.value)}
                        options={yearOptions}
                        placeholder={t('startup-application:form.foundedDateYear')}
                      />
                    </div>
                    {errors.founded_date && <p className="text-red-500 text-xs mt-1">{errorText('founded_date', errors.founded_date)}</p>}
                  </div>

                  <div className="space-y-2">
                    <label htmlFor="industry" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.industry')}</label>
                    <Select
                      id="industry"
                      value={data.industry}
                      onChange={(e) => setData('industry', e.target.value)}
                      options={industryOptions}
                      placeholder={t('startup-application:form.industryPlaceholder')}
                    />
                    {errors.industry && <p className="text-red-500 text-xs mt-1">{errorText('industry', errors.industry)}</p>}
                  </div>

                  {data.industry === 'other' && (
                    <div className="space-y-2">
                      <label htmlFor="industryOther" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.industryOther')}</label>
                      <input
                        type="text"
                        id="industryOther"
                        value={data.industry_other}
                        onChange={(e) => setData('industry_other', e.target.value)}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                        placeholder={t('startup-application:form.industryOtherPlaceholder')}
                      />
                      {errors.industry_other && <p className="text-red-500 text-xs mt-1">{errorText('industry_other', errors.industry_other)}</p>}
                    </div>
                  )}

                  <div className="space-y-2">
                    <label htmlFor="companyDesc" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.companyDescription')}</label>
                    <textarea
                      id="companyDesc"
                      rows={4}
                      maxLength={600}
                      value={data.company_description}
                      onChange={(e) => setData('company_description', e.target.value)}
                      className="w-full p-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none rounded-lg text-gray-900"
                      placeholder={t('startup-application:form.companyDescriptionPlaceholder')}
                    ></textarea>
                    <p className="text-xs text-gray-400 text-right">{descriptionLength} / 600</p>
                    {errors.company_description && <p className="text-red-500 text-xs mt-1">{errorText('company_description', errors.company_description)}</p>}
                  </div>
                </div>

                {/* Section 3: Investment Details */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.investment')}
                  </h3>

                  <div className="space-y-2">
                    <label htmlFor="fundingRound" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.currentFundingRound')}</label>
                    <Select
                      id="fundingRound"
                      value={data.current_funding_round}
                      onChange={(e) => setData('current_funding_round', e.target.value)}
                      options={fundingRoundOptions}
                      placeholder={t('startup-application:form.currentFundingRoundPlaceholder')}
                    />
                    {errors.current_funding_round && <p className="text-red-500 text-xs mt-1">{errorText('current_funding_round', errors.current_funding_round)}</p>}
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <label htmlFor="investmentAsk" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.investmentAsk')}</label>
                      <div className="relative">
                        <input
                          type="number"
                          id="investmentAsk"
                          min={1}
                          value={data.investment_ask_sar}
                          onChange={(e) => setData('investment_ask_sar', e.target.value)}
                          className="w-full h-14 px-6 pe-16 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                          placeholder={t('startup-application:form.investmentAskPlaceholder')}
                        />
                        <span className="absolute end-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 uppercase">SAR</span>
                      </div>
                      {errors.investment_ask_sar && <p className="text-red-500 text-xs mt-1">{errorText('investment_ask_sar', errors.investment_ask_sar)}</p>}
                    </div>
                    <div className="space-y-2">
                      <label htmlFor="valuation" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.valuation')}</label>
                      <div className="relative">
                        <input
                          type="number"
                          id="valuation"
                          min={1}
                          value={data.valuation_sar}
                          onChange={(e) => setData('valuation_sar', e.target.value)}
                          className="w-full h-14 px-6 pe-16 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                          placeholder={t('startup-application:form.valuationPlaceholder')}
                        />
                        <span className="absolute end-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 uppercase">SAR</span>
                      </div>
                      {errors.valuation_sar && <p className="text-red-500 text-xs mt-1">{errorText('valuation_sar', errors.valuation_sar)}</p>}
                    </div>
                  </div>

                  <div className="space-y-2">
                    <label htmlFor="previousFunding" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.previousFunding')}</label>
                    <textarea
                      id="previousFunding"
                      rows={3}
                      value={data.previous_funding}
                      onChange={(e) => setData('previous_funding', e.target.value)}
                      className="w-full p-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none rounded-lg text-gray-900"
                      placeholder={t('startup-application:form.previousFundingPlaceholder')}
                    ></textarea>
                    {errors.previous_funding && <p className="text-red-500 text-xs mt-1">{errorText('previous_funding', errors.previous_funding)}</p>}
                  </div>

                  <div className="space-y-2">
                    <label htmlFor="demoLink" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.demoLink')}</label>
                    <input
                      type="text"
                      id="demoLink"
                      value={data.demo_link}
                      onChange={(e) => setData('demo_link', e.target.value)}
                      className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                      placeholder={t('startup-application:form.demoLinkPlaceholder')}
                    />
                    {errors.demo_link && <p className="text-red-500 text-xs mt-1">{errorText('demo_link', errors.demo_link)}</p>}
                  </div>
                </div>

                {/* Section 4: Discovery */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.discovery')}
                  </h3>

                  <div className="space-y-2">
                    <label htmlFor="discoverySource" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.discoverySource')}</label>
                    <Select
                      id="discoverySource"
                      value={data.discovery_source}
                      onChange={(e) => setData('discovery_source', e.target.value)}
                      options={discoverySourceOptions}
                      placeholder={t('startup-application:form.discoverySourcePlaceholder')}
                    />
                    {errors.discovery_source && <p className="text-red-500 text-xs mt-1">{errorText('discovery_source', errors.discovery_source)}</p>}
                  </div>

                  {data.discovery_source === 'referral' && (
                    <div className="space-y-2">
                      <label htmlFor="referralName" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('startup-application:form.referralName')}</label>
                      <input
                        type="text"
                        id="referralName"
                        value={data.referral_name}
                        onChange={(e) => setData('referral_name', e.target.value)}
                        className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                        placeholder={t('startup-application:form.referralNamePlaceholder')}
                      />
                      {errors.referral_name && <p className="text-red-500 text-xs mt-1">{errorText('referral_name', errors.referral_name)}</p>}
                    </div>
                  )}
                </div>

                <div className="pt-4 flex flex-col gap-4">
                  <Button type="submit" className="w-full md:w-auto h-14 px-8" withArrow disabled={processing}>
                    {processing ? t('startup-application:form.submitting') : t('startup-application:form.submit')}
                  </Button>
                  <p className="text-xs text-gray-400">
                    {t('startup-application:form.agreement')} {t('common:footer.privacyPolicy')} {t('common:and')} {t('common:footer.termsOfService')}.
                  </p>
                </div>
              </form>
            </div>

            <aside className="space-y-6">
              <div className="border border-gray-200 bg-white p-8 rounded-xl shadow-sm">
                <h3 className="text-lg font-bold uppercase tracking-tight mb-6 text-gray-900">{t('startup-application:sidebar.title')}</h3>
                <div className="space-y-5">
                  <div className="flex items-start gap-4">
                    <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <p className="text-sm text-gray-600 leading-relaxed">{t('startup-application:sidebar.reviewProcess')}</p>
                  </div>
                  <div className="flex items-start gap-4">
                    <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <p className="text-sm text-gray-600 leading-relaxed">{t('startup-application:sidebar.ndaPolicy')}</p>
                  </div>
                  <div className="flex items-start gap-4">
                    <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <p className="text-sm text-gray-600 leading-relaxed">{t('startup-application:sidebar.responseTime')}</p>
                  </div>
                </div>
              </div>

              <div className="bg-primary text-white p-8 rounded-xl shadow-lg">
                <h3 className="text-lg font-bold uppercase tracking-tight mb-3">{t('startup-application:sidebar.title')}</h3>
                <p className="text-sm text-gray-50 mb-6 leading-relaxed">{t('startup-application:sidebar.description')}</p>
                <div className="space-y-3">
                  <div className="flex items-center gap-3 text-sm font-bold uppercase tracking-widest text-white">
                    <Mail className="w-4 h-4" /> <a href="mailto:be@rv.com.sa">{t('startup-application:sidebar.email')}</a>
                  </div>
                  <div className="flex items-center gap-3 text-sm font-bold uppercase tracking-widest text-white">
                    <MessageCircle className="w-4 h-4" />
                    <a
                      href={`https://wa.me/${t('startup-application:sidebar.whatsappNumber').replace(/\D/g, '')}`}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      {t('startup-application:sidebar.whatsapp')}
                    </a>
                  </div>
                </div>
              </div>
            </aside>
          </div>
        </section>
      </div>
    </>
  );
}
