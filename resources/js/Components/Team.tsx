import React, { useEffect, useMemo, useState } from 'react';
import { ArrowLeft, ArrowRight } from 'lucide-react';

import { useTranslation } from 'react-i18next';

const MEMBER_CONFIG = [
  {
    key: 'ceo',
    image: '/assets/images/team-ceo.jpeg',
    fallbackName: 'Yousif Al Harbi',
    fallbackTitle: 'Founder & CEO',
    fallbackDepartment: 'Founder & CEO',
    fallbackBio: 'Responsible for strategy, venture outcomes, and long-term value creation.',
  },
  {
    key: 'headOfAcceleration',
    image: '/assets/images/team-cofounder.jpeg',
    fallbackName: 'Names goes here',
    fallbackTitle: 'Head of Acceleration',
    fallbackDepartment: 'Co-Founder & Business Partner',
    fallbackBio: 'Leads accelerator programs and execution quality.',
  },
  {
    key: 'headOfVentureBuilding',
    image: '/assets/images/team-cofounder2.jpeg',
    fallbackName: 'Names goes here',
    fallbackTitle: 'Head of Venture Building',
    fallbackDepartment: 'Co-Founder & Business Partner',
    fallbackBio: 'Oversees venture creation from inception to scale.',
  },
  {
    key: 'investmentManager',
    image: '/assets/images/team-investment.jpg',
    fallbackName: 'Badryah Hanbashi',
    fallbackTitle: 'Investment Manager',
    fallbackDepartment: 'Investment',
    fallbackBio: 'Leads investment strategy, diligence, and portfolio capital plans.',
  },
  {
    key: 'investorRelationsManager',
    image: '/assets/images/team-investor-relations.jpg',
    fallbackName: 'Agad Alnemri',
    fallbackTitle: 'Investors Relation Manager',
    fallbackDepartment: 'Investor Relations',
    fallbackBio: 'Builds trusted LP relationships and orchestrates transparent reporting.',
  },
  {
    key: 'operationsManager',
    image: '/assets/images/team-operations.jpg',
    fallbackName: 'Fahad Alharbi',
    fallbackTitle: 'Operation Manager',
    fallbackDepartment: 'Operations',
    fallbackBio: 'Runs day-to-day venture operations with precision and accountability.',
  },
  {
    key: 'marketingManager',
    image: '/assets/images/team-marketing.jpg',
    fallbackName: 'Dalal Alnasser',
    fallbackTitle: 'Marketing Manager',
    fallbackDepartment: 'Marketing',
    fallbackBio: 'Shapes go-to-market stories and demand programs across ventures.',
  },
  {
    key: 'cto',
    image: '/assets/images/team-cto.jpg',
    fallbackName: 'Ahmed Afifi',
    fallbackTitle: 'CTO',
    fallbackDepartment: 'Technology',
    fallbackBio: 'Leads engineering standards, platform strategy, and technical governance.',
  },
];

export const Team: React.FC = () => {
  const { t, i18n } = useTranslation('home');
  const isRTL = i18n.dir() === 'rtl';

  const teamMembers = useMemo(
    () =>
      MEMBER_CONFIG.map((member) => ({
        ...member,
        name: t(`team.members.${member.key}.name`, { defaultValue: member.fallbackName }),
        title: t(`team.members.${member.key}.title`, { defaultValue: member.fallbackTitle }),
        department: t(`team.members.${member.key}.department`, { defaultValue: member.fallbackDepartment }),
        bio: t(`team.members.${member.key}.bio`, { defaultValue: member.fallbackBio }),
      })),
    [t]
  );

  const [current, setCurrent] = useState(0);
  const [visibleSlides, setVisibleSlides] = useState(1);

  const maxIndex = Math.max(teamMembers.length - visibleSlides, 0);
  const forwardStep = isRTL ? 1 : 1; // keep autoplay moving in the perceived forward direction for both LTR and RTL

  useEffect(() => {
    const computeVisible = () => {
      if (typeof window === 'undefined') return 1;
      if (window.innerWidth >= 1024) return 3;
      if (window.innerWidth >= 768) return 2;
      return 1;
    };

    const updateVisible = () => setVisibleSlides(computeVisible());
    updateVisible();
    window.addEventListener('resize', updateVisible);
    return () => window.removeEventListener('resize', updateVisible);
  }, []);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrent((prev) => {
        const next = prev + forwardStep;
        if (next > maxIndex) return 0;
        if (next < 0) return maxIndex;
        return next;
      });
    }, 5000);

    return () => clearInterval(timer);
  }, [forwardStep, maxIndex]);

  const handlePrev = () => {
    const step = -forwardStep;
    setCurrent((prev) => {
      const next = prev + step;
      if (next > maxIndex) return 0;
      if (next < 0) return maxIndex;
      return next;
    });
  };

  const handleNext = () => {
    const step = forwardStep;
    setCurrent((prev) => {
      const next = prev + step;
      if (next > maxIndex) return 0;
      if (next < 0) return maxIndex;
      return next;
    });
  };

  return (
    <section id="team" className="py-24 bg-white scroll-mt-24">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">

        <div className="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
          <div className="max-w-2xl">
            <span className="text-primary font-bold tracking-wider text-xs uppercase mb-4 block">{t('team.badge')}</span>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">
              {t('team.title')}
            </h2>
            <p className="text-lg text-gray-500">
              {t('team.description')}
            </p>
          </div>
        </div>

        <div className="relative">
          <div className="overflow-hidden">
            <div
              className="flex transition-transform duration-500"
              style={{
                transform: `translateX(${(isRTL ? 1 : -1) * ((100 / visibleSlides) * current)}%)`,
              }}
            >
              {teamMembers.map((member, idx) => (
                <div
                  key={member.name + idx}
                  className="flex-shrink-0 box-border p-4"
                  style={{ width: `${100 / visibleSlides}%` }}
                >
                  <div className="bg-gray-50 rounded-lg p-8 h-full hover:bg-white transition-all duration-300 border border-gray-100 group">
                    <div className="w-32 h-32 rounded-md overflow-hidden mb-6 group-hover:scale-105 transition-transform">
                      <img
                        src={member.image}
                        alt={member.name}
                        className="w-full h-full object-cover grayscale transition-all duration-300"
                      />
                    </div>
                    <h4 className="text-xl font-bold text-gray-900 mb-1">{member.name}</h4>
                    <p className="text-primary text-sm font-bold mb-4 uppercase tracking-wide">{member.title}</p>
                    <p className="text-gray-500 text-sm leading-relaxed">
                      {member.bio}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="absolute inset-y-0 left-0 flex items-center pl-2">
            <button
              type="button"
              onClick={handlePrev}
              className="p-3 rounded-full bg-white shadow-md border border-gray-200 text-gray-700 hover:text-primary hover:border-primary transition"
              aria-label="Previous slide"
            >
              <ArrowLeft size={18} />
            </button>
          </div>

          <div className="absolute inset-y-0 right-0 flex items-center pr-2">
            <button
              type="button"
              onClick={handleNext}
              className="p-3 rounded-full bg-white shadow-md border border-gray-200 text-gray-700 hover:text-primary hover:border-primary transition"
              aria-label="Next slide"
            >
              <ArrowRight size={18} />
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};