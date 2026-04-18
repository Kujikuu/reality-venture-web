import { motion } from 'framer-motion';
import { heroContainerVariants, heroItemVariants } from '../Components/animations/HeroAnimations';
import { Calendar } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { SEO } from '../Components/SEO';

interface Definition {
  term: string;
  definition: string;
}

interface Subsection {
  subtitle: string;
  content: string;
}

interface Section {
  id: string;
  title: string;
  intro?: string;
  content?: string;
  definitions?: Definition[];
  items?: string[];
  subsections?: Subsection[];
}

export default function TermsOfService() {
  const { t } = useTranslation('legal');
  const sections = t('termsOfService.sections', { returnObjects: true }) as Section[];

  return (
    <>
      <SEO />
      <div className="flex flex-col min-h-screen bg-white">
        <section className="relative overflow-hidden pt-24 pb-20 lg:pt-32 lg:pb-24 bg-gray-50">
          <div className="absolute inset-0 hero-gradient -z-10" />
          <div className="absolute inset-0 overflow-hidden pointer-events-none -z-10">
            <div className="shape absolute top-20 left-10 w-20 h-20 border-2 border-primary/10 rotate-45" />
            <div className="shape absolute top-40 right-20 w-16 h-16 rounded-full bg-primary-50/50" />
            <div className="shape absolute bottom-20 left-1/4 w-12 h-12 border border-primary/20" />
          </div>

          <div className="relative max-w-7xl mx-auto px-6 lg:px-12">
            <motion.div
              className="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-12 items-center"
              variants={heroContainerVariants}
              initial="hidden"
              animate="visible"
            >
              <div>
                <motion.span
                  variants={heroItemVariants}
                  className="inline-block py-1 px-3 rounded-md bg-primary-50 text-primary text-xs font-bold tracking-wide mb-6 w-fit uppercase"
                  dangerouslySetInnerHTML={{ __html: t('termsOfService.hero.badge') }}
                />
                <motion.h1
                  variants={heroItemVariants}
                  className="text-5xl md:text-7xl font-bold tracking-tight text-gray-900 leading-[1.1] mb-6"
                  dangerouslySetInnerHTML={{ __html: t('termsOfService.hero.title') }}
                />
                <motion.p
                  variants={heroItemVariants}
                  className="text-gray-500 text-lg max-w-2xl leading-relaxed"
                >
                  {t('termsOfService.hero.description')}
                </motion.p>
              </div>

              <motion.div
                variants={heroItemVariants}
                className="bg-white/80 backdrop-blur-sm border border-gray-100 p-8 lg:p-10 rounded-3xl relative overflow-hidden"
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-primary-50 rounded-full blur-3xl -z-10 opacity-50"></div>
                <div className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2 flex items-center gap-2">
                  <Calendar className="w-4 h-4" /> {t('termsOfService.hero.lastUpdatedLabel')}
                </div>
                <div className="text-2xl font-bold tracking-tight text-gray-900 mb-6">{t('termsOfService.hero.lastUpdated')}</div>
                <p className="text-sm text-gray-600">{t('termsOfService.hero.contactLabel')} <a className="text-primary font-semibold hover:underline" href={`mailto:${t('termsOfService.hero.contactEmail')}`}>{t('termsOfService.hero.contactEmail')}</a>.</p>
              </motion.div>
            </motion.div>
          </div>
        </section>

        <section className="flex-1 py-20 px-6 lg:px-12 bg-gray-50/50">
          <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-[0.3fr_0.7fr] gap-12 lg:gap-24">
            <aside className="hidden lg:block h-fit sticky top-32">
              <nav className="space-y-1 border-l-2 border-gray-200">
                {sections.map((section) => (
                  <a
                    key={section.id}
                    href={`#${section.id}`}
                    className="block pl-4 py-2 text-sm font-medium text-gray-500 hover:text-primary hover:border-l-2 hover:border-primary -ml-[2px] transition-all"
                  >
                    {section.title.replace(/^\d+\.\s/, '')}
                  </a>
                ))}
              </nav>
            </aside>

            <div className="space-y-12">
              <div>
                <p className="text-xl text-gray-700 leading-relaxed mb-12">
                  {t('termsOfService.intro')}
                </p>
              </div>

              {sections.map((section, index) => (
                <motion.div
                  key={section.id}
                  id={section.id}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  className="scroll-mt-32"
                >
                  <div className="flex items-center gap-4 mb-6">
                    <h2 className="text-2xl font-bold text-gray-900">{section.title}</h2>
                  </div>

                  {section.intro && (
                    <p className="text-gray-600 text-lg leading-relaxed mb-6">{section.intro}</p>
                  )}

                  {section.definitions && section.definitions.length > 0 && (
                    <dl className="space-y-4 mb-6">
                      {section.definitions.map((def, i) => (
                        <div key={i} className="flex gap-4">
                          <dt className="text-gray-900 font-semibold min-w-fit">{def.term}:</dt>
                          <dd className="text-gray-600">{def.definition}</dd>
                        </div>
                      ))}
                    </dl>
                  )}

                  {section.items && section.items.length > 0 && (
                    <ul className="space-y-3 mb-6">
                      {section.items.map((item, i) => (
                        <li key={i} className="flex gap-3 text-gray-600">
                          <span className="text-primary mt-1.5 shrink-0">
                            <svg className="w-2 h-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" /></svg>
                          </span>
                          <span>{item}</span>
                        </li>
                      ))}
                    </ul>
                  )}

                  {section.subsections && section.subsections.length > 0 && (
                    <div className="space-y-5 mb-6">
                      {section.subsections.map((sub, i) => (
                        <div key={i}>
                          <h3 className="text-lg font-semibold text-gray-900 mb-1">{sub.subtitle}</h3>
                          <p className="text-gray-600">{sub.content}</p>
                        </div>
                      ))}
                    </div>
                  )}

                  {section.content && !section.items && !section.definitions && !section.subsections && (
                    <p className="text-gray-600 text-lg leading-relaxed">{section.content}</p>
                  )}
                </motion.div>
              ))}
            </div>
          </div>
        </section>
      </div>
    </>
  );
}