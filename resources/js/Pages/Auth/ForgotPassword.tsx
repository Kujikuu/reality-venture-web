import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { KeyRound } from 'lucide-react';
import type { PageProps } from '../../types/marketplace';

export default function ForgotPassword() {
  const { t } = useTranslation('auth');
  const { flash } = usePage<PageProps>().props;
  const { data, setData, post, processing, errors } = useForm({ email: '' });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/forgot-password');
  };

  return (
    <>
      <Head title={t('forgot.title')} />
      <div className="min-h-screen flex items-center justify-center bg-gray-50 p-6">
        <motion.div
          className="w-full max-w-md bg-white border border-gray-200 rounded-2xl p-8 shadow-sm"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="flex items-center gap-3 mb-6">
            <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
              <KeyRound className="w-5 h-5 text-primary" />
            </div>
            <div>
              <h1 className="text-xl font-bold text-gray-900">{t('forgot.title')}</h1>
              <p className="text-sm text-gray-500">{t('forgot.subtitle')}</p>
            </div>
          </div>

          {flash.status && (
            <div className="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
              {t(flash.status, flash.status)}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="space-y-1.5">
              <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('forgot.email')}</label>
              <input
                type="email"
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                autoFocus
              />
              {errors.email && <p className="text-red-500 text-xs">{t(errors.email, errors.email)}</p>}
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
            >
              {t('forgot.submit')}
            </button>
          </form>

          <p className="mt-4 text-center">
            <Link href="/login" className="text-sm text-primary font-medium hover:text-primary-800">
              {t('forgot.back')}
            </Link>
          </p>
        </motion.div>
      </div>
    </>
  );
}
