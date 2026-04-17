import React from 'react';
import { Timer, ShieldAlert, Package, TrendingUp, Building2 } from 'lucide-react';
import { motion } from 'framer-motion';
import { staggerContainer, cardVariants } from './animations/CommonAnimations';
import { useTranslation } from 'react-i18next';

export const StrategicGoals: React.FC = () => {
  const { t } = useTranslation('home');

  const goals = [
    { icon: Timer },
    { icon: ShieldAlert },
    { icon: Package },
    { icon: TrendingUp },
    { icon: Building2 },
  ];

  return (
    <section className="py-28 lg:py-36 bg-white">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">

        <motion.div
          className="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-20"
          variants={staggerContainer}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
        >
          {/* Section header -- left column */}
          <div className="lg:col-span-4">
            <span className="text-primary font-bold tracking-wider text-xs uppercase mb-5 block">{t('strategicGoals.badge')}</span>
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-6 leading-tight">
              {t('strategicGoals.title')}
            </h2>
            <p className="text-lg text-gray-600 leading-relaxed">
              {t('strategicGoals.description')}
            </p>
          </div>

          {/* Goals -- right column, no cards */}
          <div className="lg:col-span-8">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-10">
              {goals.map((goal, idx) => (
                <motion.div key={idx} className="flex gap-4" variants={cardVariants}>
                  <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <goal.icon className="w-5 h-5" />
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-gray-900 mb-1">{t(`strategicGoals.goals.${idx}.title`)}</h3>
                    <p className="text-gray-600 leading-relaxed">
                      {t(`strategicGoals.goals.${idx}.description`)}
                    </p>
                  </div>
                </motion.div>
              ))}
            </div>
          </div>
        </motion.div>

      </div>
    </section>
  );
};