import React from 'react';
import { Box, Building2, Globe, Layers, LayoutGrid, Settings, Zap } from 'lucide-react';
import { motion } from 'framer-motion';
import { sectionVariants } from './animations/CommonAnimations';
import { useTranslation } from 'react-i18next';

export const Ventures: React.FC = () => {
  const { t } = useTranslation('home');

  return (
    <section id="proptech" className="relative bg-white py-24 overflow-hidden">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">

        {/* Header Section */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8">
          <div className="max-w-2xl">
            <div className="flex items-center gap-3 mb-4">
              <span className="flex h-2 w-2 rounded-full bg-primary"></span>
              <span className="font-bold text-xs uppercase tracking-widest text-primary">{t('ventures.badge')}</span>
            </div>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 leading-tight">
              {t('ventures.title')}
            </h2>
            <p className="text-lg text-gray-600 mt-2">{t('ventures.subtitle')}</p>
          </div>
          <div className="max-w-lg">
             <p className="text-gray-600 text-base leading-relaxed">
              {t('ventures.description')}
            </p>
          </div>
        </div>

        {/* Main Content Grid */}
        <motion.div
          className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center"
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
        >
          {/* Left Column: Image/Card */}
          <motion.div className="relative rounded-lg overflow-hidden group aspect-[4/3] lg:aspect-auto h-full" variants={sectionVariants}>
             <img
               src='/assets/images/saudi-ventures-bg.jpg'
               alt="PropTech"
               className="absolute inset-0 w-full h-full min-h-80 object-cover transition-transform duration-700 group-hover:scale-105"
             />
             <div className="absolute inset-0 bg-black/40"></div>

             <div className="absolute bottom-8 left-8 right-8 text-white">
                <div className="text-xs font-bold text-primary mb-2 tracking-widest uppercase">{t('ventures.focusSector')}</div>
                <div className="text-3xl font-bold tracking-tight mb-4">{t('ventures.sectorTitle')}</div>
                <div className="flex flex-wrap gap-2">
                  {(t('ventures.tags', { returnObjects: true }) as string[]).map((tag, i) => (
                    <span key={i} className="px-3 py-1 bg-white/20 backdrop-blur-md rounded-md text-xs font-medium border border-white/10">
                      {tag}
                    </span>
                  ))}
                </div>
             </div>
          </motion.div>

          {/* Right Column: Features/Focus */}
          <motion.div className="flex flex-col gap-10" variants={sectionVariants}>

            {/* Approach & Core Focus Areas */}
            <div>
              <p className="text-gray-600 text-base leading-relaxed mb-8">
                {t('ventures.approach')}
              </p>
              <h3 className="text-2xl font-bold text-gray-900 mb-6">{t('ventures.coreFocusTitle')}</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {[
                  { icon: Building2, textKey: 0 },
                  { icon: LayoutGrid, textKey: 1 },
                  { icon: Zap, textKey: 2 },
                  { icon: Settings, textKey: 3 },
                  { icon: Box, textKey: 4 },
                  { icon: Layers, textKey: 5 }
                ].map((item, i) => (
                  <div key={i} className="flex items-start p-3 rounded-md bg-gray-50 border border-gray-100 hover:border-primary/30 hover:bg-primary-50/50 transition-colors">
                    <item.icon className="w-5 h-5 text-primary ltr:mr-3 rtl:ml-3 flex-shrink-0 mt-0.5" />
                    <span className="text-sm text-gray-700 leading-relaxed">{t(`ventures.focusAreas.${i}.text`)}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-surface rounded-lg p-8 border border-gray-100">
               <h4 className="font-bold text-gray-900 mb-6 flex items-center gap-2">
                 <Globe className="w-5 h-5 text-primary" />
                 {t('ventures.servingTitle')}
               </h4>
               <ul className="space-y-2 text-sm text-gray-600">
                 {(t('ventures.serving', { returnObjects: true }) as string[]).map((item, i) => (
                   <li key={i} className="flex items-start gap-2"><span className="text-primary font-bold flex-shrink-0">•</span><span>{item}</span></li>
                 ))}
               </ul>
            </div>

          </motion.div>
        </motion.div>

        {/* Supporting Technology Verticals & Selection Criteria */}
        <div className="mt-24 pt-24 border-t border-gray-100">
          <motion.div
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, margin: "-100px" }}
            variants={sectionVariants}
          >
            {/* Supporting Technology Verticals */}
            <div className="mb-24">
              <div className="text-center mb-16 px-4">
                <h3 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{t('ventures.supportingTechTitle')}</h3>
                <p className="text-gray-600 text-lg max-w-3xl mx-auto leading-relaxed">
                  {t('ventures.supportingTechDesc')}
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                {(t('ventures.verticals', { returnObjects: true }) as Array<{title: string, desc: string}>).map((vertical, i) => (
                  <div key={i} className="flex flex-col items-center text-center space-y-4 p-4">
                    <div className="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-2 rotate-3 hover:rotate-6 transition-transform">
                       <Layers className="w-6 h-6" />
                    </div>
                    <h4 className="font-bold text-gray-900 text-lg">{vertical.title}</h4>
                    <p className="text-sm text-gray-600 leading-relaxed">{vertical.desc}</p>
                  </div>
                ))}
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
