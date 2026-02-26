import { Head, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { XCircle, Home, RefreshCw } from 'lucide-react';

interface Props {
  rejectionReason: string | null;
}

export default function Rejected({ rejectionReason }: Props) {
  const { t } = useTranslation('dashboard');

  return (
    <>
      <Head title={t('rejected.title')} />
      <div className="min-h-screen flex items-center justify-center bg-gray-50 p-6">
        <motion.div
          className="max-w-md w-full text-center"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6">
            <XCircle className="w-10 h-10 text-red-500" />
          </div>
          <h1 className="text-2xl font-bold text-gray-900 mb-3">{t('rejected.title')}</h1>
          <p className="text-gray-500 leading-relaxed mb-6">{t('rejected.message')}</p>

          {rejectionReason && (
            <div className="bg-red-50 border border-red-100 rounded-lg p-4 mb-6 text-start">
              <p className="text-xs font-bold uppercase tracking-wide text-red-400 mb-1">{t('rejected.reason')}</p>
              <p className="text-sm text-red-700">{rejectionReason}</p>
            </div>
          )}

          {!rejectionReason && (
            <div className="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
              <p className="text-sm text-gray-500">{t('rejected.noReason')}</p>
            </div>
          )}

          <p className="text-sm text-gray-400 mb-6">{t('rejected.reapplyDesc')}</p>

          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href="/consultant/onboarding/reapply"
              method="post"
              as="button"
              className="inline-flex items-center justify-center gap-2 px-6 h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors"
            >
              <RefreshCw className="w-4 h-4" /> {t('rejected.reapply')}
            </Link>
            <Link
              href="/"
              className="inline-flex items-center justify-center gap-2 px-6 h-12 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition-colors"
            >
              <Home className="w-4 h-4" /> {t('rejected.backHome')}
            </Link>
          </div>
        </motion.div>
      </div>
    </>
  );
}
