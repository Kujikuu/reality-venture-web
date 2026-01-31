import React from 'react';
import { Eye, Flag, Target } from 'lucide-react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { sectionVariants, staggerContainer, cardVariants } from './animations/CommonAnimations';

export const VisionMission: React.FC = () => {
  const { t } = useTranslation('home');
  const features = t('visionMission.features', { returnObjects: true });
  const featureList = Array.isArray(features) ? features : [];

  return (
    <section id="vision" className="py-24 bg-gray-50 relative overflow-hidden">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <motion.div
          className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center"
          variants={staggerContainer}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
        >
          {/* Visual Side */}
          <motion.div className="relative" variants={sectionVariants}>

             <div className="relative bg-white rounded-lg p-8 md:p-12 border border-gray-100">
                <div className="flex flex-col gap-10">
                   <div>
                      <div className="w-12 h-12 bg-primary/10 rounded-md flex items-center justify-center mb-6 text-primary">
                        <Eye className="w-6 h-6" />
                      </div>
                      <h3 className="text-2xl font-bold text-gray-900 mb-3">{t('visionMission.vision')}</h3>
                      <p className="text-gray-500 leading-relaxed">
                        {t('visionMission.visionText')}
                      </p>
                   </div>

                   <div className="w-full h-px bg-gray-100"></div>

                   <div>
                       <div className="w-12 h-12 bg-secondary-50 rounded-md flex items-center justify-center mb-6 text-primary">
                        <Flag className="w-6 h-6" />
                      </div>
                      <h3 className="text-2xl font-bold text-gray-900 mb-3">{t('visionMission.mission')}</h3>
                      <p className="text-gray-500 leading-relaxed">
                        {t('visionMission.missionText')}
                      </p>
                   </div>
                </div>
             </div>
          </motion.div>

          {/* Text Side */}
          <motion.div variants={sectionVariants}>
            <span className="text-primary font-bold tracking-wider text-xs uppercase mb-4 block">{t('visionMission.whyWeExistBadge')}</span>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6 leading-tight" dangerouslySetInnerHTML={{ __html: t('visionMission.whyWeExistTitle') }} />
            <p className="text-lg text-gray-500 mb-8 leading-relaxed">
              {t('visionMission.whyWeExistDescription')}
            </p>

            <div className="flex flex-col gap-4">
              {featureList.map((item, i) => (
                <div key={i} className="flex items-center gap-3">
                  <div className="w-6 h-6 rounded-md bg-secondary-50 flex items-center justify-center text-secondary shrink-0">
                    <Target className="w-3 h-3" />
                  </div>
                  <span className="font-semibold text-gray-700">{item}</span>
                </div>
              ))}
            </div>
          </motion.div>

        </motion.div>
      </div>
    </section>
  );
};