import { Head, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { ShieldCheck } from 'lucide-react';

interface Props {
  token: string;
  email: string;
}

export default function ResetPassword({ token, email }: Props) {
  const { t } = useTranslation('auth');
  const { data, setData, post, processing, errors } = useForm({
    token,
    email,
    password: '',
    password_confirmation: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/reset-password');
  };

  return (
    <>
      <Head title={t('reset.title')} />
      <div className="min-h-screen flex items-center justify-center bg-gray-50 p-6">
        <motion.div
          className="w-full max-w-md bg-white border border-gray-200 rounded-2xl p-8 shadow-sm"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="flex items-center gap-3 mb-6">
            <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
              <ShieldCheck className="w-5 h-5 text-primary" />
            </div>
            <div>
              <h1 className="text-xl font-bold text-gray-900">{t('reset.title')}</h1>
              <p className="text-sm text-gray-500">{t('reset.subtitle')}</p>
            </div>
          </div>

          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="space-y-1.5">
              <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('reset.email')}</label>
              <input
                type="email"
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
              />
              {errors.email && <p className="text-red-500 text-xs">{t(errors.email, errors.email)}</p>}
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('reset.password')}</label>
              <input
                type="password"
                value={data.password}
                onChange={(e) => setData('password', e.target.value)}
                className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
              />
              {errors.password && <p className="text-red-500 text-xs">{t(errors.password, errors.password)}</p>}
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('reset.confirmPassword')}</label>
              <input
                type="password"
                value={data.password_confirmation}
                onChange={(e) => setData('password_confirmation', e.target.value)}
                className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
              />
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
            >
              {t('reset.submit')}
            </button>
          </form>
        </motion.div>
      </div>
    </>
  );
}
