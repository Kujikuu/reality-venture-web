import React from 'react';
import { ArrowRight, Linkedin, Twitter, TwitterIcon, X, XIcon, XSquareIcon } from 'lucide-react';



import { useTranslation } from 'react-i18next';

export const Team: React.FC = () => {
  const { t } = useTranslation('home');

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
          {/* <button className="hidden md:flex items-center gap-2 text-sm font-bold text-gray-900 hover:text-primary transition-colors">
            {t('team.joinTeam')} <ArrowRight className="w-4 h-4 rtl:-scale-x-100" />
          </button> */}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">

          {/* CEO Card - Featured */}
          {/* <div className="lg:col-span-3 bg-surface rounded-lg p-8 md:p-12 flex flex-col md:flex-row gap-10 items-center">
            <div className="w-48 h-48 md:w-64 md:h-64 rounded-lg overflow-hidden shrink-0">
              <img
                src="/assets/images/team-ceo.jpeg"
                alt={t('team.ceo.position')}
                className="w-full h-full object-cover"
              />
            </div>
            <div className="flex-1 text-center md:text-start">
              <div className="inline-block px-3 py-1 bg-white rounded-md text-xs font-bold text-primary mb-4 border border-gray-100">{t('team.ceo.role')}</div>
              <h3 className="text-3xl font-bold text-gray-900 mb-2">{t('team.ceo.name')}</h3>
              <p className="text-xl text-gray-500 mb-6 font-medium">{t('team.ceo.position')}</p>
              <p className="text-gray-600 text-lg leading-relaxed mb-8 max-w-2xl">
                {t('team.ceo.bio')}
              </p>
              <div className="flex gap-4 justify-center md:justify-start">
                <a href='https://www.linkedin.com/in/yousif-alharbi-00510717' target="_blank" className="p-2 rounded-md bg-white hover:bg-gray-100 transition-colors text-gray-600">
                  <Linkedin className="w-5 h-5" />
                </a>
                <a href='https://x.com/YALHARBY' target="_blank" className="p-2 rounded-md bg-white hover:bg-gray-100 transition-colors text-gray-600">
                  <Twitter className="w-5 h-5" />
                </a>
              </div>
            </div>
          </div> */}

          {/* Program Leads */}
          <div className="bg-gray-50 rounded-lg p-8 hover:bg-white transition-all duration-300 border border-gray-100 group">
            <div className="w-32 h-32 rounded-md overflow-hidden mb-6 group-hover:scale-105 transition-transform">
              <img
                src="/assets/images/team-ceo.jpeg"
                alt={t('team.ceo.name')}
                className="w-full h-full object-cover"
              />
            </div>
            <h4 className="text-xl font-bold text-gray-900 mb-1">{t('team.ceo.name')}</h4>
            <p className="text-primary text-sm font-bold mb-4 uppercase tracking-wide">{t('team.ceo.position')}</p>
            <p className="text-gray-500 text-sm leading-relaxed">
              {t('team.ceo.bio')}
            </p>
          </div>

          <div className="bg-gray-50 rounded-lg p-8 hover:bg-white transition-all duration-300 border border-gray-100 group">
            <div className="w-32 h-32 rounded-md overflow-hidden mb-6 group-hover:scale-105 transition-transform">
              <img
                src="/assets/images/team-cofounder.jpeg"
                alt={t('team.headOfAcceleration.title')}
                className="w-full h-full object-cover"
              />
            </div>
            <h4 className="text-xl font-bold text-gray-900 mb-1">{t('team.headOfAcceleration.title')}</h4>
            <p className="text-primary text-sm font-bold mb-4 uppercase tracking-wide">{t('team.headOfAcceleration.department')}</p>
            <p className="text-gray-500 text-sm leading-relaxed">
              {t('team.headOfAcceleration.bio')}
            </p>
          </div>

          <div className="bg-gray-50 rounded-lg p-8 hover:bg-white transition-all duration-300 border border-gray-100 group">
            <div className="w-32 h-32 rounded-md overflow-hidden mb-6 group-hover:scale-105 transition-transform">
              <img
                src="/assets/images/team-cofounder2.jpeg"
                alt={t('team.headOfVentureBuilding.title')}
                className="w-full h-full object-cover"
              />
            </div>
            <h4 className="text-xl font-bold text-gray-900 mb-1">{t('team.headOfVentureBuilding.title')}</h4>
            <p className="text-primary text-sm font-bold mb-4 uppercase tracking-wide">{t('team.headOfVentureBuilding.department')}</p>
            <p className="text-gray-500 text-sm leading-relaxed">
              {t('team.headOfVentureBuilding.bio')}
            </p>
          </div>

          {/* Advisors Card */}
          {/* <div className="bg-primary rounded-lg p-8 flex flex-col justify-between text-white">
            <div>
              <h3 className="text-2xl font-bold mb-2">{t('team.advisors.title')}</h3>
              <p className="text-white/80 text-sm mb-8 leading-relaxed">
                {t('team.advisors.description')}
              </p>
            </div>
             <button className="flex items-center gap-2 font-bold text-sm bg-white/20 hover:bg-white hover:text-primary transition-all p-3 rounded-md w-fit backdrop-blur-sm">
               {t('team.advisors.viewButton')} <ArrowRight className="w-4 h-4 rtl:-scale-x-100" />
             </button> 
          </div> */}

        </div>
      </div>
    </section>
  );
};