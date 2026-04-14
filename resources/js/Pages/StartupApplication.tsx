import { useEffect, useState } from 'react';
import { Button } from '../Components/ui/Button';
import { Input } from '../Components/ui/Input';
import { Select } from '../Components/ui/Select';
import { Textarea } from '../Components/ui/Textarea';
import { FileUpload } from '../Components/ui/FileUpload';
import { CurrencyInput } from '../Components/ui/CurrencyInput';
import { Mail, CheckCircle2, MessageCircle, Check } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { heroContainerVariants, heroItemVariants } from '../Components/animations/HeroAnimations';
import { useForm, Link, usePage } from '@inertiajs/react';
import { SEO } from '../Components/SEO';
import { SarIcon } from '../Components/ui/SarIcon';
import { COUNTRIES } from '../data/countries';
import { SAUDI_CITIES } from '../data/saudi-cities';

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
  'none',
  'bootstrapped',
  'pre_seed',
  'seed',
  'series_a',
  'series_b',
  'series_c_plus',
];

const BUSINESS_STAGE_KEYS = ['idea', 'mvp', 'growth'];

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

  const { flash } = usePage<any>().props;
  const [isSuccess, setIsSuccess] = useState(flash?.success === 'submitted');
  const { data, setData, post, processing, errors, reset } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    city: '',
    social_profile: '',
    business_stage: '',
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
    attachment: null as File | null,
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
      
      fetch(`/applications/lookup/${ref}`)
        .then(res => {
            if (!res.ok) return Promise.reject(new Error('Not found'));
            return res.json();
        })
        .then(json => {
            if (json.uid) {
                setData(prev => ({
                    ...prev,
                    first_name: json.first_name || '',
                    last_name: json.last_name || '',
                    email: json.email || '',
                    phone: json.phone || '',
                    city: json.city || '',
                    social_profile: json.social_profile || '',
                    referral_param: ref,
                }));
            }
        })
        .catch(() => {});
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
      forceFormData: true,
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        reset();
        setIsSuccess(true);
      },
    });
  };

  const countryOptions = COUNTRIES.map((c) => ({
    value: c.code,
    label: isArabic ? c.name_ar : c.name_en,
  }));

  const cityOptions = SAUDI_CITIES.map((c) => ({
    value: c.code,
    label: isArabic ? c.name_ar : c.name_en,
  }));

  const businessStageOptions = BUSINESS_STAGE_KEYS.map((key) => ({
    value: key,
    label: t(`startup-application:businessStages.${key}`),
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

  if (isSuccess) {
    return (
      <>
        <SEO />
        <div className="min-h-screen bg-gray-50 flex items-center justify-center p-6">
          <motion.div
            initial={{ scale: 0.9, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            className="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 text-center border border-gray-100"
          >
            <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <Check className="w-10 h-10 text-green-600" />
            </div>
            <h1 className="text-3xl font-extrabold text-gray-900 mb-4">
              {t('startup-application:form.successTitle')}
            </h1>
            <p className="text-gray-600 mb-8 leading-relaxed">
              {t('startup-application:form.success')}
            </p>
            <Link
              href="/"
              className="w-full h-14 bg-primary text-white hover:bg-primary-700 px-10 text-base font-bold tracking-tight rounded-xl transition-all duration-300 flex items-center justify-center gap-2 active:scale-95 shadow-lg shadow-primary/20"
            >
              {t('startup-application:form.returnHome')}
            </Link>
          </motion.div>
        </div>
      </>
    );
  }

  return (
    <>
      <SEO />
      <div className="flex flex-col min-h-screen bg-white">
        {/* Hero Section */}
        <section className="relative overflow-hidden pt-24 pb-16">
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
              </div>
              <form onSubmit={handleSubmit} className="space-y-12">
                {/* Section 1: Founder Info */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.founder')}
                  </h3>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Input
                      label={t('startup-application:form.firstName')}
                      value={data.first_name}
                      onChange={(e) => setData('first_name', e.target.value)}
                      placeholder={t('startup-application:form.firstName')}
                      error={errorText('first_name', errors.first_name)}
                    />
                    <Input
                      label={t('startup-application:form.lastName')}
                      value={data.last_name}
                      onChange={(e) => setData('last_name', e.target.value)}
                      placeholder={t('startup-application:form.lastName')}
                      error={errorText('last_name', errors.last_name)}
                    />
                  </div>

                  <Input
                    type="email"
                    label={t('startup-application:form.email')}
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    placeholder={t('startup-application:form.emailPlaceholder')}
                    error={errorText('email', errors.email)}
                  />

                  <Input
                    type="tel"
                    dir="ltr"
                    label={t('startup-application:form.phone')}
                    value={data.phone}
                    onChange={(e) => setData('phone', e.target.value)}
                    placeholder={t('startup-application:form.phonePlaceholder')}
                    error={errorText('phone', errors.phone)}
                  />

                  <Select
                    label={t('startup-application:form.city')}
                    value={data.city}
                    onChange={(e) => setData('city', e.target.value)}
                    options={cityOptions}
                    placeholder={t('startup-application:form.cityPlaceholder')}
                    error={errorText('city', errors.city)}
                  />

                  <Input
                    label={t('startup-application:form.linkedin')}
                    value={data.social_profile}
                    onChange={(e) => setData('social_profile', e.target.value)}
                    placeholder={t('startup-application:form.linkedinPlaceholder')}
                    error={errorText('social_profile', errors.social_profile)}
                  />
                </div>

                {/* Section 2: Company Details */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.company')}
                  </h3>

                  <Select
                    label={t('startup-application:form.businessStage')}
                    value={data.business_stage}
                    onChange={(e) => setData('business_stage', e.target.value)}
                    options={businessStageOptions}
                    placeholder={t('startup-application:form.businessStagePlaceholder')}
                    error={errorText('business_stage', errors.business_stage)}
                  />

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Input
                      label={t('startup-application:form.companyName')}
                      value={data.company_name}
                      onChange={(e) => setData('company_name', e.target.value)}
                      placeholder={t('startup-application:form.companyNamePlaceholder')}
                      error={errorText('company_name', errors.company_name)}
                    />
                    <Input
                      type="number"
                      min={1}
                      max={20}
                      label={t('startup-application:form.numberOfFounders')}
                      value={String(data.number_of_founders)}
                      onChange={(e) => setData('number_of_founders', Number(e.target.value))}
                      error={errorText('number_of_founders', errors.number_of_founders)}
                    />
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Select
                      label={t('startup-application:form.hqCountry')}
                      value={data.hq_country}
                      onChange={(e) => setData('hq_country', e.target.value)}
                      options={countryOptions}
                      placeholder={t('startup-application:form.hqCountryPlaceholder')}
                      error={errorText('hq_country', errors.hq_country)}
                    />
                    <Input
                      label={t('startup-application:form.websiteLink')}
                      value={data.website_link}
                      onChange={(e) => setData('website_link', e.target.value)}
                      placeholder={t('startup-application:form.websiteLinkPlaceholder')}
                      error={errorText('website_link', errors.website_link)}
                    />
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

                  <Select
                    label={t('startup-application:form.industry')}
                    value={data.industry}
                    onChange={(e) => setData('industry', e.target.value)}
                    options={industryOptions}
                    placeholder={t('startup-application:form.industryPlaceholder')}
                    error={errorText('industry', errors.industry)}
                  />

                  {data.industry === 'other' && (
                    <Input
                      label={t('startup-application:form.industryOther')}
                      value={data.industry_other}
                      onChange={(e) => setData('industry_other', e.target.value)}
                      placeholder={t('startup-application:form.industryOtherPlaceholder')}
                      error={errorText('industry_other', errors.industry_other)}
                    />
                  )}

                  <div>
                    <Textarea
                        label={t('startup-application:form.companyDescription')}
                        rows={4}
                        maxLength={600}
                        value={data.company_description}
                        onChange={(e) => setData('company_description', e.target.value)}
                        placeholder={t('startup-application:form.companyDescriptionPlaceholder')}
                    />
                    <p className="text-xs text-gray-400 text-end mt-1">{descriptionLength} / 600</p>
                    {errors.company_description && <p className="text-red-500 text-xs mt-1">{errorText('company_description', errors.company_description)}</p>}
                </div>

                  <FileUpload
                    label={t('startup-application:form.attachment')}
                    value={data.attachment}
                    onChange={(file) => setData('attachment', file)}
                    accept=".pdf,.jpg,.jpeg,.png"
                    placeholder={t('startup-application:form.attachmentBrowse')}
                    helpText={t('startup-application:form.attachmentHelp')}
                    error={errorText('attachment', errors.attachment)}
                  />
                </div>

                {/* Section 3: Investment Details */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.investment')}
                  </h3>

                  <Select
                    label={t('startup-application:form.currentFundingRound')}
                    value={data.current_funding_round}
                    onChange={(e) => setData('current_funding_round', e.target.value)}
                    options={fundingRoundOptions}
                    placeholder={t('startup-application:form.currentFundingRoundPlaceholder')}
                    error={errorText('current_funding_round', errors.current_funding_round)}
                  />

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <CurrencyInput
                      min={1}
                      label={t('startup-application:form.investmentAsk')}
                      value={data.investment_ask_sar}
                      onChange={(e) => setData('investment_ask_sar', e.target.value)}
                      placeholder={t('startup-application:form.investmentAskPlaceholder')}
                      error={errorText('investment_ask_sar', errors.investment_ask_sar)}
                    />
                    <CurrencyInput
                      min={1}
                      label={t('startup-application:form.valuation')}
                      value={data.valuation_sar}
                      onChange={(e) => setData('valuation_sar', e.target.value)}
                      placeholder={t('startup-application:form.valuationPlaceholder')}
                      error={errorText('valuation_sar', errors.valuation_sar)}
                    />
                  </div>

                  <Textarea
                    label={t('startup-application:form.previousFunding')}
                    rows={3}
                    value={data.previous_funding}
                    onChange={(e) => setData('previous_funding', e.target.value)}
                    placeholder={t('startup-application:form.previousFundingPlaceholder')}
                    error={errorText('previous_funding', errors.previous_funding)}
                  />

                  <Input
                    label={t('startup-application:form.demoLink')}
                    value={data.demo_link}
                    onChange={(e) => setData('demo_link', e.target.value)}
                    placeholder={t('startup-application:form.demoLinkPlaceholder')}
                    error={errorText('demo_link', errors.demo_link)}
                  />
                </div>

                {/* Section 4: Discovery */}
                <div className="space-y-6">
                  <h3 className="text-sm font-bold uppercase tracking-widest text-primary border-b border-gray-100 pb-3">
                    {t('startup-application:sections.discovery')}
                  </h3>

                  <Select
                    label={t('startup-application:form.discoverySource')}
                    value={data.discovery_source}
                    onChange={(e) => setData('discovery_source', e.target.value)}
                    options={discoverySourceOptions}
                    placeholder={t('startup-application:form.discoverySourcePlaceholder')}
                    error={errorText('discovery_source', errors.discovery_source)}
                  />

{data.discovery_source === 'referral' && (
                    <Input
                      label={t('startup-application:form.referralName')}
                      value={data.referral_name}
                      onChange={(e) => setData('referral_name', e.target.value)}
                      placeholder={t('startup-application:form.referralNamePlaceholder')}
                      error={errorText('referral_name', errors.referral_name)}
                    />
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
                      dir='ltr'
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
