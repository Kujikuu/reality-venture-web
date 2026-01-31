import React, { useState, useEffect, useRef } from 'react';
import { Users, Briefcase, Network } from 'lucide-react';
import { useTranslation } from 'react-i18next';

const AUTOPLAY_DURATION = 5000; // 5 seconds per slide

export const TargetAudience: React.FC = () => {
  const { t } = useTranslation('home');

  const targets = [
    {
      id: 'founders',
      icon: Users,
      image: '/assets/images/saudi-founders.jpg',
    },
    {
      id: 'operators',
      icon: Briefcase,
      image: '/assets/images/saudi-operators.jpg',
    },
    {
      id: 'investors',
      icon: Network,
      image: '/assets/images/saudi-investors.jpg',
    }
  ];

  const [activeTab, setActiveTab] = useState(0);
  const [progress, setProgress] = useState(0);
  const startTimeRef = useRef<number | null>(null);
  const requestRef = useRef<number>();

  const resetTimer = () => {
    startTimeRef.current = null;
    setProgress(0);
  };

  const animate = (time: number) => {
    if (!startTimeRef.current) startTimeRef.current = time;
    const elapsed = time - startTimeRef.current;
    const newProgress = Math.min((elapsed / AUTOPLAY_DURATION) * 100, 100);

    setProgress(newProgress);

    if (newProgress < 100) {
      requestRef.current = requestAnimationFrame(animate);
    } else {
      // Move to next tab
      setActiveTab((prev) => (prev + 1) % targets.length);
      resetTimer();
      // Small delay to restart animation on new tab
      setTimeout(() => {
        requestRef.current = requestAnimationFrame(animate);
      }, 50);
    }
  };

  useEffect(() => {
    requestRef.current = requestAnimationFrame(animate);
    return () => {
      if (requestRef.current) cancelAnimationFrame(requestRef.current);
    };
  }, [activeTab]);

  const handleTabClick = (index: number) => {
    if (requestRef.current) cancelAnimationFrame(requestRef.current);
    setActiveTab(index);
    resetTimer();
    // Restart animation
    requestRef.current = requestAnimationFrame(animate);
  };

  const activeContent = targets[activeTab];
  const targetKeys = ['founders', 'operators', 'investors'] as const;

  return (
    <section className="py-24 bg-gray-50 overflow-hidden" id="community">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">

        <div className="text-center max-w-3xl mx-auto mb-12">
           <span className="text-primary font-bold tracking-wider text-xs uppercase mb-4 block">{t('targetAudience.badge')}</span>
           <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">
             {t('targetAudience.title')}
           </h2>
           <p className="text-lg text-gray-500">
             {t('targetAudience.description')}
           </p>
        </div>

        {/* Feature Component */}
        <div className="bg-white rounded-lg border border-gray-100 overflow-hidden">

          {/* Tabs Header */}
          <div className="flex flex-col md:flex-row">
            {targets.map((target, idx) => {
              const isActive = activeTab === idx;
              const targetKey = targetKeys[idx];
              return (
                <button
                  key={target.id}
                  onClick={() => handleTabClick(idx)}
                  className={`relative flex-1 py-8 px-8 text-start outline-none group overflow-hidden transition-colors duration-300 ${
                    isActive ? 'bg-primary' : 'bg-gray-50 hover:bg-gray-100 border-b border-gray-200 md:border-b-0'
                  }`}
                >
                  <div className={`flex items-center gap-3 mb-2 font-bold uppercase tracking-widest text-xs md:text-sm ${
                    isActive ? 'text-white/90' : 'text-gray-400 group-hover:text-gray-600'
                  }`}>
                     {t(`targetAudience.${targetKey}.label`)}
                  </div>
                  <div className={`text-lg md:text-xl font-bold truncate ${
                    isActive ? 'text-white' : 'text-gray-900'
                  }`}>
                    {t(`targetAudience.${targetKey}.title`)}
                  </div>

                  {/* Progress Bar for Active Tab */}
                  {isActive && (
                    <div className="absolute bottom-0 start-0 h-1.5 bg-white/40 z-10"
                         style={{ width: `${progress}%` }} />
                  )}
                </button>
              );
            })}
          </div>

          {/* Content Area */}
          <div className="p-8 md:p-12 lg:p-16">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-stretch">
              <div className="space-y-8 h-full animate-in fade-in slide-in-from-left-4 duration-500">
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
                  <activeContent.icon className="w-4 h-4" />
                  {t(`targetAudience.${targetKeys[activeTab]}.title`)}
                </div>

                <h3 className="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight leading-tight">
                  {t(`targetAudience.${targetKeys[activeTab]}.headline`)}
                </h3>

                <p className="text-lg text-gray-500 leading-relaxed max-w-lg">
                  {t(`targetAudience.${targetKeys[activeTab]}.description`)}
                </p>
              </div>

              {/* Image Content */}
              <div className="relative aspect-[4/2] rounded-md overflow-hidden animate-in fade-in zoom-in-95 duration-700 delay-100 group">
                 <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity z-10" />
                 <img
                   src={activeContent.image}
                   alt={t(`targetAudience.${targetKeys[activeTab]}.title`)}
                   className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                 />
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>
  );
};
