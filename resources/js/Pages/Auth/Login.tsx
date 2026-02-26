import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { LogIn } from 'lucide-react';
import type { PageProps } from '../../types/marketplace';

export default function Login() {
  const { t } = useTranslation('auth');
  const { flash } = usePage<PageProps>().props;
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/login');
  };

  return (
    <>
      <Head title={t('login.title')} />
      <div className="min-h-screen flex">
        {/* Left Panel - Brand */}
        <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary via-primary-800 to-[#2a1a40] items-center justify-center p-12">
          <div className="max-w-md text-white">
            <a href="/"><img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-10 mb-12 brightness-0 invert" /></a>
            <h2 className="text-4xl font-bold tracking-tight mb-4">{t('login.heroTitle')}</h2>
            <p className="text-white/70 text-lg leading-relaxed">{t('login.heroDesc')}</p>
          </div>
        </div>

        {/* Right Panel - Form */}
        <div className="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
          <motion.div
            className="w-full max-w-md"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.4 }}
          >
            <div className="flex items-center gap-3 mb-8">
              <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                <LogIn className="w-5 h-5 text-primary" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-gray-900">{t('login.title')}</h1>
                <p className="text-sm text-gray-500">{t('login.subtitle')}</p>
              </div>
            </div>

            {flash.status && (
              <div className="mb-6 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                {t(flash.status, flash.status)}
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-1.5">
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('login.email')}</label>
                <input
                  type="email"
                  value={data.email}
                  onChange={(e) => setData('email', e.target.value)}
                  className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                  autoFocus
                />
                {errors.email && <p className="text-red-500 text-xs">{t(errors.email, errors.email)}</p>}
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('login.password')}</label>
                <input
                  type="password"
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                  className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                />
                {errors.password && <p className="text-red-500 text-xs">{errors.password}</p>}
              </div>

              <div className="flex items-center justify-between">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={data.remember}
                    onChange={(e) => setData('remember', e.target.checked)}
                    className="rounded border-gray-300 text-primary focus:ring-primary"
                  />
                  <span className="text-sm text-gray-600">{t('login.remember')}</span>
                </label>
                <Link href="/forgot-password" className="text-sm text-primary hover:text-primary-800 font-medium">
                  {t('login.forgot')}
                </Link>
              </div>

              <button
                type="submit"
                disabled={processing}
                className="w-full h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
              >
                {t('login.submit')}
              </button>
            </form>

            <p className="mt-6 text-center text-sm text-gray-500">
              {t('login.noAccount')}{' '}
              <Link href="/register" className="text-primary font-semibold hover:text-primary-800">
                {t('login.register')}
              </Link>
            </p>
          </motion.div>
        </div>
      </div>
    </>
  );
}
