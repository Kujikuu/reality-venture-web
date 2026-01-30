import { Button } from '../Components/ui/Button';
import { Mail, CheckCircle2 } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { heroContainerVariants, heroItemVariants } from '../Components/animations/HeroAnimations';
import { useForm } from '@inertiajs/react';

export default function Apply() {
  const { t } = useTranslation(['common', 'navigation', 'apply']);

  const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    linkedin_profile: '',
    program_interest: '',
    description: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/applications', {
      preserveState: true,
      preserveScroll: true,
    });
  };

  return (
    <div className="flex flex-col min-h-screen bg-white">
      {/* Hero Section */}
      <section className="relative overflow-hidden pt-24 pb-20 lg:pt-32 lg:pb-24">
        {/* Animated Gradient Background */}
        <div className="absolute inset-0 hero-gradient -z-10" />

        {/* Floating Geometric Shapes */}
        <div className="absolute inset-0 overflow-hidden pointer-events-none -z-10">
          <div className="shape absolute top-20 left-10 w-20 h-20 border-2 border-primary/10 rotate-45" />
          <div className="shape absolute top-40 right-20 w-16 h-16 rounded-full bg-primary-50/50" />
          <div className="shape absolute bottom-20 left-1/4 w-12 h-12 border border-primary/20" />
        </div>

        <div className="relative max-w-[1440px] mx-auto px-6 lg:px-12">
          <motion.div
            className="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-12 items-center"
            variants={heroContainerVariants}
            initial="hidden"
            animate="visible"
          >
            {/* Left Content */}
            <div>
              <motion.span
                variants={heroItemVariants}
                className="inline-block py-1 px-3 rounded-md bg-primary-50 text-primary text-xs font-bold tracking-wide mb-6 w-fit uppercase"
              >
                {t('apply:hero.badge')}
              </motion.span>

              <motion.h1
                variants={heroItemVariants}
                className="text-5xl md:text-7xl font-bold tracking-tight text-gray-900 leading-[1.1] mb-6"
              >
                {t('apply:hero.title')} <br />
                <span className="text-primary">{t('apply:hero.titleHighlighted')}</span>
              </motion.h1>

              <motion.p
                variants={heroItemVariants}
                className="text-gray-500 text-lg max-w-2xl mb-8 leading-relaxed"
              >
                {t('apply:hero.subtitle')}
              </motion.p>

              <motion.div
                variants={heroItemVariants}
                className="flex flex-wrap gap-6 text-xs font-bold uppercase tracking-widest text-gray-400"
              >
                <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('apply:hero.rollingReview')}</span>
                <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('apply:hero.responseTime')}</span>
                <span className="flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-primary/60"></span>{t('apply:hero.founderFirst')}</span>
              </motion.div>
            </div>

            {/* Right Content (Card) */}
            <motion.div
              variants={heroItemVariants}
              className="bg-white/80 backdrop-blur-sm border border-gray-100 shadow-xl shadow-primary-500/5 p-8 lg:p-10 rounded-3xl relative overflow-hidden"
            >
              <div className="absolute top-0 right-0 w-32 h-32 bg-primary-50 rounded-full blur-3xl -z-10 opacity-50"></div>
              <h2 className="text-xl font-bold uppercase tracking-tight mb-6 text-gray-900">{t('apply:whatWeLookFor.title')}</h2>
              <ul className="space-y-4 text-sm text-gray-600">
                <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('apply:whatWeLookFor.innovative.description')}</li>
                <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('apply:whatWeLookFor.committed.description')}</li>
                <li className="flex items-start gap-3"><span className="mt-1.5 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>{t('apply:whatWeLookFor.scalable.description')}</li>
              </ul>
              <div className="mt-8 pt-6 border-t border-gray-100 flex items-center gap-3 text-xs uppercase tracking-widest text-gray-400">
                <Mail className="w-4 h-4 text-primary" /> {t('apply:sidebar.email')}
              </div>
            </motion.div>
          </motion.div>
        </div>
      </section>

      {/* Form Section */}
      <section className="flex-1 py-20 px-6 lg:px-12 bg-gray-50/50">
        <div className="max-w-[1440px] mx-auto grid grid-cols-1 lg:grid-cols-[1.4fr_0.6fr] gap-10">
          <div className="bg-white border border-gray-200 p-8 md:p-12 rounded-xl shadow-sm">
            <div className="flex items-center justify-between mb-10">
              <h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight text-gray-900">{t('apply:form.title')}</h2>
              {recentlySuccessful && (
                <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg text-sm">
                  ✓ {t('apply:form.success', 'Application submitted successfully!')}
                </div>
              )}
            </div>
            <form onSubmit={handleSubmit} className="space-y-8">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-2">
                  <label htmlFor="firstName" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('apply:form.firstName')}</label>
                  <input
                    type="text"
                    id="firstName"
                    value={data.first_name}
                    onChange={(e) => setData('first_name', e.target.value)}
                    className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                    placeholder={t('apply:form.firstName')}
                  />
                  {errors.first_name && <p className="text-red-500 text-xs mt-1">{errors.first_name}</p>}
                </div>
                <div className="space-y-2">
                  <label htmlFor="lastName" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('apply:form.lastName')}</label>
                  <input
                    type="text"
                    id="lastName"
                    value={data.last_name}
                    onChange={(e) => setData('last_name', e.target.value)}
                    className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                    placeholder={t('apply:form.lastName')}
                  />
                  {errors.last_name && <p className="text-red-500 text-xs mt-1">{errors.last_name}</p>}
                </div>
              </div>

              <div className="space-y-2">
                <label htmlFor="email" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('apply:form.email')}</label>
                <input
                  type="email"
                  id="email"
                  value={data.email}
                  onChange={(e) => setData('email', e.target.value)}
                  className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                  placeholder={t('apply:form.emailPlaceholder')}
                />
                {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
              </div>

              <div className="space-y-2">
                <label htmlFor="linkedin" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('apply:form.linkedin')}</label>
                <input
                  type="text"
                  id="linkedin"
                  value={data.linkedin_profile}
                  onChange={(e) => setData('linkedin_profile', e.target.value)}
                  className="w-full h-14 px-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900"
                  placeholder={t('apply:form.linkedinPlaceholder')}
                />
                {errors.linkedin_profile && <p className="text-red-500 text-xs mt-1">{errors.linkedin_profile}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-bold uppercase tracking-wide mb-3 block text-gray-500">{t('apply:form.program')}</label>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <label className="group relative flex items-center gap-3 p-4 border border-gray-200 cursor-pointer hover:border-primary transition-all rounded-lg hover:bg-primary-50/30">
                    <input
                      type="radio"
                      name="interest"
                      checked={data.program_interest === 'accelerator'}
                      onChange={(e) => setData('program_interest', 'accelerator')}
                      className="accent-primary w-5 h-5"
                    />
                    <span className="font-medium text-sm text-gray-700">{t('apply:form.programAccelerator')}</span>
                  </label>
                  <label className="group relative flex items-center gap-3 p-4 border border-gray-200 cursor-pointer hover:border-primary transition-all rounded-lg hover:bg-primary-50/30">
                    <input
                      type="radio"
                      name="interest"
                      checked={data.program_interest === 'venture'}
                      onChange={(e) => setData('program_interest', 'venture')}
                      className="accent-primary w-5 h-5"
                    />
                    <span className="font-medium text-sm text-gray-700">{t('apply:form.programVenture')}</span>
                  </label>
                  <label className="group relative flex items-center gap-3 p-4 border border-gray-200 cursor-pointer hover:border-primary transition-all rounded-lg hover:bg-primary-50/30">
                    <input
                      type="radio"
                      name="interest"
                      checked={data.program_interest === 'corporate'}
                      onChange={(e) => setData('program_interest', 'corporate')}
                      className="accent-primary w-5 h-5"
                    />
                    <span className="font-medium text-sm text-gray-700">{t('apply:form.programCorporate')}</span>
                  </label>
                </div>
                {errors.program_interest && <p className="text-red-500 text-xs mt-1">{errors.program_interest}</p>}
              </div>

              <div className="space-y-2">
                <label htmlFor="message" className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('apply:form.describe')}</label>
                <textarea
                  id="message"
                  rows={6}
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                  className="w-full p-6 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none rounded-lg text-gray-900"
                  placeholder={t('apply:form.messagePlaceholder')}
                ></textarea>
                {errors.description && <p className="text-red-500 text-xs mt-1">{errors.description}</p>}
              </div>

              <div className="pt-4 flex flex-col gap-4">
                <Button type="submit" className="w-full md:w-auto h-14 px-8" withArrow disabled={processing}>
                  {processing ? t('apply:form.submitting', 'Submitting...') : t('apply:form.submit')}
                </Button>
                <p className="text-xs text-gray-400">
                  {t('apply:form.agreement')} {t('common:footer.privacyPolicy')} {t('common:and')} {t('common:footer.termsOfService')}.
                </p>
              </div>
            </form>
          </div>

          <aside className="space-y-6">
            <div className="border border-gray-200 bg-white p-8 rounded-xl shadow-sm">
              <h3 className="text-lg font-bold uppercase tracking-tight mb-6 text-gray-900">{t('apply:sidebar.title')}</h3>
              <div className="space-y-5">
                <div className="flex items-start gap-4">
                  <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                  <p className="text-sm text-gray-600 leading-relaxed">{t('apply:sidebar.reviewProcess')}</p>
                </div>
                <div className="flex items-start gap-4">
                  <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                  <p className="text-sm text-gray-600 leading-relaxed">{t('apply:sidebar.ndaPolicy')}</p>
                </div>
                <div className="flex items-start gap-4">
                  <CheckCircle2 className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                  <p className="text-sm text-gray-600 leading-relaxed">{t('apply:sidebar.responseTime')}</p>
                </div>
              </div>
            </div>

            <div className="bg-primary text-white p-8 rounded-xl shadow-lg">
              <h3 className="text-lg font-bold uppercase tracking-tight mb-3">{t('apply:sidebar.title')}</h3>
              <p className="text-sm text-gray-50 mb-6 leading-relaxed">{t('apply:sidebar.description')}</p>
              <div className="flex items-center gap-3 text-sm font-bold uppercase tracking-widest text-white">
                <Mail className="w-4 h-4" /> <a href="mailto:be@rv.com.sa">{t('apply:sidebar.email')}</a>
              </div>
            </div>
          </aside>
        </div>
      </section>
    </div>
  );
}
