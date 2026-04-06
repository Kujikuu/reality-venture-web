import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { Clock, Home } from 'lucide-react';
import { SEO } from '../../Components/SEO';

export default function PendingApproval() {
  const { t } = useTranslation('dashboard');

  return (
    <>
      <SEO />
      <div className="min-h-screen flex items-center justify-center bg-gray-50 p-6">
        <motion.div
          className="max-w-md text-center"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
            <Clock className="w-10 h-10 text-primary" />
          </div>
          <h1 className="text-2xl font-bold text-gray-900 mb-3">{t('pending.title')}</h1>
          <p className="text-gray-500 leading-relaxed mb-8">{t('pending.message')}</p>
          <Link
            href="/"
            className="inline-flex items-center gap-2 px-6 h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors"
          >
            <Home className="w-4 h-4" /> {t('pending.backHome')}
          </Link>
        </motion.div>
      </div>
    </>
  );
}
