import React from 'react';
import { Button } from './ui/Button';
import { Link } from '@inertiajs/react';
import { Check } from 'lucide-react';
import { useTranslation } from 'react-i18next';

const SHOW_VENTURE_BUILDER = false;

export const Programs: React.FC = () => {
  const { t } = useTranslation('programs');

  return (
    <section id="programs" className="bg-gray-50 py-28 lg:py-36 scroll-mt-24">
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">{t('title')}</h2>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            {t('subtitle')}
          </p>
        </div>

        <div className="max-w-4xl mx-auto">
          {/* Accelerator Program */}
          <div className="bg-white rounded-2xl p-8 lg:p-12 border border-gray-200/80 shadow-sm">
            <div className="grid md:grid-cols-2 md:gap-12 lg:gap-16">
              {/* Left column -- copy + CTA */}
              <div className="flex flex-col">
                <div className="mb-6">
                  <span className="text-3xl font-bold text-gray-900">{t('accelerator.title')}</span>
                  <p className="text-sm text-gray-600 mt-2">{t('accelerator.subtitle')}</p>
                </div>
                <p className="text-gray-600 mb-8 text-sm leading-relaxed">
                  {t('accelerator.description')}
                </p>
                <Link href="/application-form" className="w-full mt-auto">
                  <Button variant="primary" className="w-full rounded-xl" withArrow>{t('accelerator.cta')}</Button>
                </Link>
              </div>

              {/* Right column -- includes + duration */}
              <div className="mt-8 md:mt-0 md:ps-12 md:border-s md:border-gray-200">
                <p className="font-semibold text-sm text-gray-900 mb-5">{t('accelerator.includes.title')}</p>
                <ul className="space-y-4 text-sm text-gray-700">
                  {(t('accelerator.includes.items', { returnObjects: true }) as string[]).map((item, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-secondary shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
                <p className="text-sm text-gray-600 mt-8"><strong>{t('accelerator.includes.duration')}</strong></p>
              </div>
            </div>
          </div>

          {SHOW_VENTURE_BUILDER && (
            /* Reality Venture Builder */
            <div className="bg-white rounded-2xl p-8 lg:p-12 border border-gray-200/80 shadow-sm mt-8">
              <div className="mb-6">
                <span className="text-3xl font-bold text-gray-900">{t('ventureBuilder.title')}</span>
                <p className="text-sm text-gray-600 mt-2">{t('ventureBuilder.subtitle')}</p>
              </div>
              <p className="text-gray-600 mb-8 text-sm leading-relaxed">
                {t('ventureBuilder.description')}
              </p>

              <Link href="/application-form" className="w-full mb-8">
                <Button variant="outline" className="w-full rounded-xl">{t('ventureBuilder.cta')}</Button>
              </Link>

              <div className="pt-8 border-t border-gray-200">
                <p className="font-semibold text-sm text-gray-900 mb-5">{t('ventureBuilder.provider.title')}</p>
                <ul className="space-y-4 text-sm text-gray-700">
                  {(t('ventureBuilder.provider.items', { returnObjects: true }) as string[]).map((item, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-primary shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          )}
        </div>
      </div>
    </section>
  );
};
