import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { CheckCircle2 } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { SEO } from '../../Components/SEO';

interface Props {
  application: {
    uid: string;
    first_name: string;
    type_label: string;
    type_label_ar: string;
    status_label: string;
    status_label_ar: string;
    recommended_action_key: string | null;
  };
}

export default function Status({ application }: Props) {
  const { t, i18n } = useTranslation(['application-status', 'common']);
  const isArabic = i18n.language === 'ar';

  return (
    <>
      <SEO title={t('application-status:pageTitle')} />
      <Head title={t('application-status:pageTitle')} />
      <div className="min-h-screen bg-gray-50 flex items-center justify-center p-6">
        <motion.div
          initial={{ scale: 0.95, opacity: 0 }}
          animate={{ scale: 1, opacity: 1 }}
          className="max-w-lg w-full bg-white rounded-3xl shadow-xl p-10 border border-gray-100"
        >
          <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <CheckCircle2 className="w-8 h-8 text-primary" />
          </div>

          <h1 className="text-3xl font-extrabold text-gray-900 mb-2 text-center">
            {t('application-status:title')}
          </h1>
          <p className="text-gray-500 text-center mb-8">{t('application-status:subtitle')}</p>

          <div className="space-y-4 mb-8">
            <div className="rounded-2xl bg-gray-50 px-5 py-4">
              <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">
                {t('application-status:reference')}
              </p>
              <p className="text-xl font-bold text-primary">{application.uid}</p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="rounded-2xl border border-gray-100 px-5 py-4">
                <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">
                  {t('application-status:stage')}
                </p>
                <p className="font-semibold text-gray-900">
                  {isArabic ? application.type_label_ar : application.type_label}
                </p>
              </div>
              <div className="rounded-2xl border border-gray-100 px-5 py-4">
                <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">
                  {t('application-status:status')}
                </p>
                <p className="font-semibold text-gray-900">
                  {isArabic ? application.status_label_ar : application.status_label}
                </p>
              </div>
            </div>

            {application.recommended_action_key && (
              <div className="rounded-2xl border border-primary/10 bg-primary/5 px-5 py-4">
                <p className="text-xs font-bold uppercase tracking-widest text-primary mb-1">
                  {t('application-status:nextStep')}
                </p>
                <p className="text-sm text-gray-700">
                  {t(`application-status:recommendedActions.${application.recommended_action_key}`)}
                </p>
              </div>
            )}
          </div>

          <p className="text-center text-sm text-gray-500 mb-6">
            {t('application-status:greeting', { name: application.first_name })}
          </p>

          <Link
            href="/"
            className="w-full h-14 bg-primary text-white hover:bg-primary-700 px-10 text-base font-bold tracking-tight rounded-xl transition-all duration-300 flex items-center justify-center"
          >
            {t('application-status:returnHome')}
          </Link>
        </motion.div>
      </div>
    </>
  );
}
