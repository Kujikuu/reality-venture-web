import React, { useState, useRef, useEffect, useCallback } from 'react';
import { ArrowUpRight, ChevronLeft, ChevronRight, Check } from 'lucide-react';
import { ServiceItem } from '../types';
import { motion } from 'framer-motion';
import { sectionVariants } from './animations/CommonAnimations';
import { useTranslation } from 'react-i18next';

const services: ServiceItem[] = [
  { image: '/images/services/BusinessModeling.png', titleKey: 'items.businessModeling.title', descriptionKey: 'items.businessModeling.description' },
  { image: '/images/services/Management.png', titleKey: 'items.management.title', descriptionKey: 'items.management.description' },
  { image: '/images/services/Franchising.png', titleKey: 'items.franchising.title', descriptionKey: 'items.franchising.description' },
  { image: '/images/services/Marketing.png', titleKey: 'items.marketing.title', descriptionKey: 'items.marketing.description' },
  { image: '/images/services/Technology.png', titleKey: 'items.technology.title', descriptionKey: 'items.technology.description' },
  { image: '/images/services/Investment.png', titleKey: 'items.investment.title', descriptionKey: 'items.investment.description' },
];

const featureKeys = ['features.endToEnd', 'features.tailored', 'features.proven'] as const;

export const Services: React.FC = () => {
  const { t } = useTranslation('services');
  const viewportRef = useRef<HTMLDivElement>(null);
  const [current, setCurrent] = useState(0);
  const [cardWidth, setCardWidth] = useState(360);
  const [gap, setGap] = useState(24);
  const [isRtl, setIsRtl] = useState(false);

  const maxIndex = services.length - 1;

  const calcCardWidth = useCallback(() => {
    if (!viewportRef.current) return;
    const w = viewportRef.current.offsetWidth;
    if (w < 640) {
      setCardWidth(w - 32);
      setGap(16);
    } else if (w < 1024) {
      setCardWidth(300);
      setGap(20);
    } else {
      setCardWidth(360);
      setGap(24);
    }
  }, []);

  useEffect(() => {
    setIsRtl(document.documentElement.dir === 'rtl');
    calcCardWidth();
    window.addEventListener('resize', calcCardWidth);
    return () => window.removeEventListener('resize', calcCardWidth);
  }, [calcCardWidth]);

  const goPrev = () => {
    if (isRtl) {
      if (current < maxIndex) setCurrent(current + 1);
    } else {
      if (current > 0) setCurrent(current - 1);
    }
  };

  const goNext = () => {
    if (isRtl) {
      if (current > 0) setCurrent(current - 1);
    } else {
      if (current < maxIndex) setCurrent(current + 1);
    }
  };

  const prevDisabled = isRtl ? current >= maxIndex : current === 0;
  const nextDisabled = isRtl ? current === 0 : current >= maxIndex;

  const translateX = isRtl
    ? current * (cardWidth + gap)
    : -(current * (cardWidth + gap));

  return (
    <section id="services" className="py-16 lg:py-24 bg-gray-50 scroll-mt-24 overflow-hidden">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        <motion.div
          className="flex flex-col lg:flex-row gap-10 lg:gap-16"
          variants={sectionVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
        >
          {/* Left column */}
          <div className="w-full lg:w-1/2">
            <span className="text-primary font-bold tracking-wider text-xs uppercase mb-4 block">{t('badge')}</span>
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 mb-4">
              {t('title')}
            </h2>
            <p className="text-base md:text-lg text-gray-500 mb-6 md:mb-8">
              {t('description')}
            </p>
            <div className="flex flex-col gap-4 md:gap-6">
              {featureKeys.map((key) => (
                <div key={key} className="flex items-start gap-3">
                  <div className="shrink-0 w-6 h-6 rounded-full bg-primary-50 flex items-center justify-center mt-0.5">
                    <Check className="w-3.5 h-3.5 text-primary" />
                  </div>
                  <p
                    className="text-sm md:text-base font-medium text-gray-600"
                    dangerouslySetInnerHTML={{ __html: t(key) }}
                  />
                </div>
              ))}
            </div>
          </div>

          {/* Right column - Carousel */}
          <div className="w-full lg:w-1/2">
            <div
              ref={viewportRef}
              className="overflow-visible"
              style={{
                clipPath: isRtl
                  ? 'inset(-20px 0 -20px -9999px)'
                  : 'inset(-20px -9999px -20px 0)',
              }}
            >
              <div
                className="flex transition-transform duration-500 ease-out"
                style={{ transform: `translateX(${translateX}px)` }}
              >
                {services.map((service, idx) => (
                  <div
                    key={idx}
                    className="shrink-0 cursor-pointer"
                    style={{
                      width: `${cardWidth}px`,
                      marginInlineEnd: `${gap}px`,
                    }}
                  >
                    <div className="group flex flex-col h-full gap-3 p-3 rounded-2xl border border-gray-100 bg-white overflow-hidden transition-all duration-300 hover:shadow-lg">
                      <div className="bg-gray-50 p-3 rounded-sm overflow-clip w-full h-48 md:h-64 lg:h-80">
                        <img
                          src={service.image}
                          alt={t(service.titleKey)}
                          className="w-full h-full object-contain"
                        />
                      </div>
                      <div className="flex flex-col gap-3 pt-5 p-4">
                        <div className="flex items-center justify-between">
                          <h3 className="text-xl font-bold text-gray-900">
                            {t(service.titleKey)}
                          </h3>
                          <ArrowUpRight className="w-6 h-6 text-gray-400" />
                        </div>
                        <p className="text-sm text-gray-500 leading-tight">
                          {t(service.descriptionKey)}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Navigation arrows */}
            <div className="flex gap-4 mt-4">
              <button
                onClick={goPrev}
                disabled={prevDisabled}
                className="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
              >
                {isRtl ? <ChevronRight className="w-5 h-5" /> : <ChevronLeft className="w-5 h-5" />}
              </button>
              <button
                onClick={goNext}
                disabled={nextDisabled}
                className="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
              >
                {isRtl ? <ChevronLeft className="w-5 h-5" /> : <ChevronRight className="w-5 h-5" />}
              </button>
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  );
};
