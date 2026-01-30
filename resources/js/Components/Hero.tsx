import React from 'react';
import { motion } from 'framer-motion';
import { Button } from './ui/Button';
import { InteractiveTiltCard } from './ui/InteractiveTiltCard';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { heroContainerVariants, heroItemVariants } from './animations/HeroAnimations';

export const Hero: React.FC = () => {
  const { t } = useTranslation(['common', 'home']);

  return (
    <section id="hero" className="relative overflow-hidden pt-20 pb-32 lg:pt-32 lg:pb-40 bg-white">
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
          className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"
          variants={heroContainerVariants}
          initial="hidden"
          animate="visible"
        >
          {/* Left Column: Content */}
          <div className="flex flex-col text-center lg:text-start">

            <motion.span variants={heroItemVariants} className="inline-block py-1 px-3 rounded-md bg-primary-50 text-primary text-xs font-bold tracking-wide mb-8 w-fit">
              {t('home:hero.badge')}
            </motion.span>

            <motion.h1 variants={heroItemVariants} className="max-w-4xl text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-gray-900 mb-8 leading-[1.1]">
              {t('home:hero.title').split('. ').map((part: string, i: number, arr: string[]) => (
                <React.Fragment key={i}>
                  {i === arr.length - 1 ? (
                    <span className="text-primary">{part}</span>
                  ) : (
                    <>{part}. </>
                  )}
                </React.Fragment>
              ))}
            </motion.h1>

            <motion.p variants={heroItemVariants} className="max-w-2xl text-lg sm:text-xl text-gray-500 mb-10 leading-relaxed">
              {t('home:hero.description')}
            </motion.p>

            <motion.div variants={heroItemVariants} className="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
              <Link href="/application-form">
                <Button withArrow className="w-full sm:w-auto px-10 h-14 text-lg">{t('common:buttons.getStarted')}</Button>
              </Link>
              <Link href="#programs">
                <Button variant="outline" className="w-full sm:w-auto px-10 h-14 text-lg border-gray-200 bg-white">{t('common:buttons.viewPrograms')}</Button>
              </Link>
            </motion.div>

          </div>

          {/* Right Column: Interactive Visual (Desktop Only) */}
          <motion.div
            variants={heroItemVariants}
            className="hidden lg:flex justify-center items-center"
          >
            <InteractiveTiltCard className="w-full max-w-lg aspect-square">
              <div className="relative w-full h-full">
                {/* Main Card */}
                <div className="absolute inset-0 rounded-3xl bg-gradient-to-br from-white via-primary-50/30 to-white border border-primary-100/50 shadow-2xl overflow-hidden">

                  {/* Animated gradient mesh background */}
                  <div className="absolute inset-0 opacity-40">
                    <div className="absolute top-0 left-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-pulse" style={{ animationDuration: '4s' }} />
                    <div className="absolute bottom-0 right-0 w-96 h-96 bg-p-200/20 rounded-full blur-3xl animate-pulse" style={{ animationDuration: '5s', animationDelay: '1s' }} />
                  </div>

                  {/* Floating decorative elements */}
                  <div className="absolute top-8 right-8 w-4 h-4 bg-primary/30 rounded-full animate-bounce" style={{ animationDuration: '3s' }} />
                  <div className="absolute top-20 right-20 w-2 h-2 bg-secondary/40 rounded-full animate-bounce" style={{ animationDuration: '4s', animationDelay: '0.5s' }} />
                  <div className="absolute bottom-12 left-8 w-3 h-3 bg-primary/25 rounded-full animate-bounce" style={{ animationDuration: '3.5s', animationDelay: '1s' }} />

                  {/* Geometric accent lines */}
                  <div className="absolute top-12 left-12 w-20 h-20 border-l-2 border-t-2 border-primary/10 rounded-tl-3xl" />
                  <div className="absolute bottom-12 right-12 w-20 h-20 border-r-2 border-b-2 border-primary/10 rounded-br-3xl" />

                  {/* Logo and Content */}
                  <div className="relative z-10 flex flex-col items-center justify-center h-full p-10">
                    {/* Logo Image */}
                    <div className="relative mb-8">
                      {/* Glowing ring effect */}
                      <div className="absolute inset-0 bg-gradient-to-br from-primary/20 to-primary-300/20 rounded-full blur-xl animate-pulse" style={{ animationDuration: '3s' }} />

                      <div className="relative w-48 h-48 flex items-center justify-center ">
                        <img
                          src="/assets/images/RV.png"
                          alt="Reality Venture Logo"
                          className="w-40 h-40 object-contain drop-shadow-2xl"
                        />
                      </div>

                      {/* Orbiting dots */}
                      <div className="absolute -top-2 -right-2 w-6 h-6 bg-primary rounded-full shadow-lg animate-ping" style={{ animationDuration: '2s' }} />
                      <div className="absolute -bottom-2 -left-2 w-4 h-4 bg-primary-300 rounded-full shadow-lg animate-ping" style={{ animationDuration: '2.5s', animationDelay: '0.5s' }} />
                    </div>

                    {/* Text Content */}
                    <div className="text-center space-y-3">
                      <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20">
                        <span className="w-2 h-2 bg-primary rounded-full animate-pulse" />
                        <span className="text-sm font-semibold text-primary tracking-wide">{t('home:hero.card.badge')}</span>
                      </div>
                      <h3 className="text-4xl font-black text-gray-900 tracking-tight">
                        {t('home:hero.card.building')}
                      </h3>
                      <p className="text-5xl font-black text-primary tracking-tight">
                        {t('home:hero.card.ventures')}
                      </p>
                      <div className="pt-4 flex items-center justify-center gap-6 text-sm text-gray-600">
                        <div className="flex items-center gap-2">
                          <svg className="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                          <span className="font-semibold">{t('common:company.ventureBuilder')}</span>
                        </div>
                        <div className="w-px h-4 bg-gray-300" />
                        <div className="flex items-center gap-2">
                          <svg className="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                          </svg>
                          <span className="font-semibold">{t('common:company.accelerator')}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Glassmorphism overlay */}
                  <div className="absolute inset-0 bg-gradient-to-tr from-white/10 via-transparent to-white/5 pointer-events-none" />

                  {/* Corner shine effect */}
                  <div className="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-white/40 to-transparent rounded-bl-full pointer-events-none" />
                </div>

                {/* Shadow/depth element */}
                <div className="absolute -bottom-4 left-1/2 -translate-x-1/2 w-4/5 h-4 bg-primary/10 rounded-full blur-2xl" />
              </div>
            </InteractiveTiltCard>
          </motion.div>

        </motion.div>
      </div>
    </section>
  );
};