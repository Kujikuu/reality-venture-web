import React from 'react';
import { ProcessStep } from '../types';
import { useTranslation } from 'react-i18next';

export const Process: React.FC = () => {
  const { t } = useTranslation('home');

  const steps: ProcessStep[] = [
    {
      number: "01",
      title: '',
      description: ''
    },
    {
      number: "02",
      title: '',
      description: ''
    },
    {
      number: "03",
      title: '',
      description: ''
    },
    {
      number: "04",
      title: '',
      description: ''
    },
  ];

  return (

    <section id="process" className="py-24 bg-white">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <div className="flex flex-col md:flex-row justify-between items-end mb-20 gap-6">
          <div className="max-w-xl">
             <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-4">{t('process.title')}</h2>
             <p className="text-lg text-gray-500">
               {t('process.description')}
             </p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
          {/* Connector Line - Desktop */}
          <div className="hidden md:block absolute top-[28px] left-0 w-[80%] h-0.5 bg-gray-100 z-0"></div>

          {steps.map((step, idx) => (
            <div key={idx} className="relative z-10 flex flex-col gap-6 group">
              <div className={`w-14 h-14 rounded-lg flex items-center justify-center font-bold text-lg transition-all duration-300 border-[3px]
                ${idx === 0
                  ? 'bg-primary border-primary text-white'
                  : 'bg-white border-gray-100 text-gray-400 group-hover:border-primary group-hover:text-primary'
                }`}
              >
                {t(`process.steps.${idx}.number`)}
              </div>
              <div>
                <h4 className="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary transition-colors">{t(`process.steps.${idx}.title`)}</h4>
                <p className="text-sm text-gray-500 leading-relaxed pr-4">
                  {t(`process.steps.${idx}.description`)}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};
