import React from 'react';
import { Button } from './ui/Button';
import { Link } from '@inertiajs/react';
import { Check } from 'lucide-react';
import { useTranslation } from 'react-i18next';

const SHOW_VENTURE_BUILDER = false;

export const Programs: React.FC = () => {
  const { t } = useTranslation('programs');

  return (
    <section id="programs" className="bg-gray-50 py-24 scroll-mt-24">
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">{t('title')}</h2>
          <p className="text-lg text-gray-500 max-w-2xl mx-auto">
            {t('subtitle')}
          </p>
        </div>

        <div className="max-w-2xl mx-auto">
          {/* Accelerator Program */}
          <div className="bg-white rounded-2xl p-8 border border-secondary/40 ring-1 ring-secondary/20 flex flex-col relative shadow-lg shadow-secondary/5">
            <div className="mb-6">
              <span className="text-3xl font-bold text-gray-900">{t('accelerator.title')}</span>
              <p className="text-sm text-gray-500 mt-2">{t('accelerator.subtitle')}</p>
            </div>
            <p className="text-gray-500 mb-4 text-sm leading-relaxed min-h-[60px]">
              {t('accelerator.description')}
            </p>
            <Link href="/application-form" className="w-full mb-8">
              <Button variant="primary" className="w-full rounded-xl py-6" withArrow>{t('accelerator.cta')}</Button>
            </Link>
            <div className="pt-8 border-t border-gray-100">
              <div className="mb-6">
                <p className="font-semibold text-sm text-gray-900 mb-2">{t('accelerator.includes.title')}</p>
                <ul className="space-y-4 text-sm text-gray-600">
                  {(t('accelerator.includes.items', { returnObjects: true }) as string[]).map((item, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-secondary shrink-0" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
                <p className="text-sm text-gray-500 mt-4"><strong>{t('accelerator.includes.duration')}</strong></p>
              </div>
            </div>
          </div>

          {SHOW_VENTURE_BUILDER && (
            /* Reality Venture Builder */
            <div className="bg-white rounded-2xl p-8 border border-gray-200 flex flex-col transition-all duration-300 hover:border-secondary/30 hover:shadow-md hover:shadow-secondary/5 mt-8">
              <div className="mb-6">
                <span className="text-3xl font-bold text-gray-900">{t('ventureBuilder.title')}</span>
                <p className="text-sm text-gray-500 mt-2">{t('ventureBuilder.subtitle')}</p>
              </div>
              <p className="text-gray-500 mb-8 text-sm leading-relaxed min-h-[60px]">
                {t('ventureBuilder.description')}
              </p>

              <Link href="/application-form" className="w-full mb-8">
                <Button variant="outline" className="w-full rounded-xl py-6 border-gray-200">{t('ventureBuilder.cta')}</Button>
              </Link>

              <div className="pt-8 border-t border-gray-100">
                <p className="font-semibold text-sm text-gray-900 mb-4">{t('ventureBuilder.provider.title')}</p>
                <ul className="space-y-4 text-sm text-gray-600">
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
