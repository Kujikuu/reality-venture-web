import React from 'react';
import { Workflow, Settings, Store, TrendingUp, Cpu, Landmark } from 'lucide-react';
import { ServiceItem } from '../types';
import { motion } from 'framer-motion';
import { staggerContainer, cardVariants } from './animations/CommonAnimations';
import { useTranslation } from 'react-i18next';

export const Services: React.FC = () => {
  const { t } = useTranslation('services');

  const services: ServiceItem[] = [
    { icon: Workflow, titleKey: "items.businessModeling.title", descriptionKey: "items.businessModeling.description" },
    { icon: Settings, titleKey: "items.management.title", descriptionKey: "items.management.description" },
    { icon: Store, titleKey: "items.franchising.title", descriptionKey: "items.franchising.description" },
    { icon: TrendingUp, titleKey: "items.marketing.title", descriptionKey: "items.marketing.description" },
    { icon: Cpu, titleKey: "items.technology.title", descriptionKey: "items.technology.description" },
    { icon: Landmark, titleKey: "items.investment.title", descriptionKey: "items.investment.description" },
  ];

  return (
    <section id="services" className="py-24 bg-white scroll-mt-24">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <div className="text-center mb-20">
          <span className="text-primary font-bold tracking-wider text-xs uppercase mb-4 block">{t('badge')}</span>
          <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">
            {t('title')}
          </h2>
          <p className="text-lg text-gray-500 max-w-2xl mx-auto">
            {t('description')}
          </p>
        </div>
        
        <motion.div
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          variants={staggerContainer}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
        >
          {services.map((service, idx) => (
            <motion.div key={idx} className="group p-8 rounded-lg border border-gray-100 hover:border-secondary/40 hover:bg-white hover:shadow-lg hover:shadow-secondary/5 transition-all duration-300" variants={cardVariants}>
              <div className="w-14 h-14 bg-white rounded-md flex items-center justify-center mb-6 group-hover:bg-secondary group-hover:text-white transition-colors text-gray-400 border border-gray-100 group-hover:border-secondary">
                <service.icon className="w-7 h-7" />
              </div>
              <h4 className="text-xl font-bold text-gray-900 mb-3 group-hover:text-secondary transition-colors">{service.titleKey ? t(service.titleKey) : service.title}</h4>
              <p className="text-gray-500 leading-relaxed text-sm">
                {service.descriptionKey ? t(service.descriptionKey) : service.description}
              </p>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
};