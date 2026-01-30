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
    <section className="py-24" style={{
      backgroundColor: 'transparent',
      backgroundImage: 'radial-gradient(rgba(99, 102, 242, 0.1) 1px, transparent 1px), radial-gradient(rgba(99, 102, 242, 0.1) 1px, rgba(33, 222, 222, 0) 1px)',
      backgroundPosition: '0 0, 10px 10px',
      backgroundSize: '20px 20px',
    }}>
      <div className="max-w-7xl mx-auto px-6 lg:px-8" >

        <div className="text-center max-w-2xl mx-auto mb-16">
          <h2 className="text-sm font-bold tracking-widest text-primary uppercase mb-3">{t('strategicGoals.badge')}</h2>
          <h3 className="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-6">
            {t('strategicGoals.title')}
          </h3>
          <p className="text-lg text-gray-500">
            {t('strategicGoals.description')}
          </p>
        </div>

        <motion.div
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          variants={staggerContainer}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
        >
          {goals.map((goal, idx) => (
            <motion.div key={idx} className="bg-white rounded-lg p-8 border border-gray-100 transition-all duration-300 group" variants={cardVariants}>
              <div className="w-12 h-12 bg-primary-50 rounded-md flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors text-primary">
                <goal.icon className="w-6 h-6" />
              </div>
              <h4 className="text-xl font-bold text-gray-900 mb-3">{t(`strategicGoals.goals.${idx}.title`)}</h4>
              <p className="text-gray-500 leading-relaxed">
                {t(`strategicGoals.goals.${idx}.description`)}
              </p>
            </motion.div>
          ))}

          {/* Add a CTA card or stat card to make it even */}
          <motion.div className="bg-primary rounded-lg p-8 flex flex-col justify-center items-center text-center text-white" variants={cardVariants}>
            <h4 className="text-4xl font-bold mb-2">{t('strategicGoals.statCard.percentage')}</h4>
            <p className="text-primary-100 mb-6">{t('strategicGoals.statCard.label')}</p>
          </motion.div>
        </motion.div>
      </div>
    </section>
  );
};