import React from 'react';
import { Target } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { sectionVariants, staggerContainer } from './animations/CommonAnimations';

export const VisionMission: React.FC = () => {
  const { t } = useTranslation('home');
  const features = t('visionMission.features', { returnObjects: true });
  const featureList = Array.isArray(features) ? features : [];

  return (
    <section id="vision" className="py-28 lg:py-36 bg-gray-50 relative overflow-hidden">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        <motion.div
          className="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20 items-center"
          variants={staggerContainer}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
        >
          {/* Text Side - leads the reading flow */}
          <motion.div variants={sectionVariants}>
            <span className="text-primary font-bold tracking-wider text-xs uppercase mb-5 block">{t('visionMission.whyWeExistBadge')}</span>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-8 leading-tight">
              {t('visionMission.whyWeExistTitle')}{' '}
              <span className="text-primary">{t('visionMission.whyWeExistTitleHighlight')}</span>
            </h2>
            <p className="text-lg text-gray-600 mb-10 leading-relaxed max-w-xl">
              {t('visionMission.whyWeExistDescription')}
            </p>

            <div className="flex flex-col gap-5">
              {featureList.map((item, i) => (
                <div key={i} className="flex items-center gap-4">
                  <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0">
                    <Target className="w-4 h-4" />
                  </div>
                  <span className="font-semibold text-gray-800">{item}</span>
                </div>
              ))}
            </div>
          </motion.div>

          {/* Vision & Mission Card */}
          <motion.div className="relative" variants={sectionVariants}>
            <div className="relative bg-white rounded-2xl p-10 md:p-14 border border-gray-200/80 shadow-sm">
              <div className="flex flex-col gap-0">
                <div>
                  <h3 className="text-xl font-bold text-gray-900 mb-3">{t('visionMission.vision')}</h3>
                  <p className="text-gray-600 leading-relaxed">
                    {t('visionMission.visionText')}
                  </p>
                </div>

                <div className="w-full h-px bg-gray-200 my-8" />

                <div>
                  <h3 className="text-xl font-bold text-gray-900 mb-3">{t('visionMission.mission')}</h3>
                  <p className="text-gray-600 leading-relaxed">
                    {t('visionMission.missionText')}
                  </p>
                </div>
              </div>
            </div>
          </motion.div>

        </motion.div>
      </div>
    </section>
  );
};