import { Head, Link, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { UserPlus, Search, Briefcase } from 'lucide-react';

export default function Register() {
  const { t } = useTranslation('auth');
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'client' as 'client' | 'consultant',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/register');
  };

  return (
    <>
      <Head title={t('register.title')} />
      <div className="min-h-screen flex">
        {/* Left Panel */}
        <div className="hidden lg:flex lg:w-1/2 bg-linear-to-br from-primary via-primary-800 to-[#2a1a40] items-center justify-center p-12">
          <div className="max-w-md text-white">
           <a href="/"><img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-10 mb-12 brightness-0 invert" /></a>
            <h2 className="text-4xl font-bold tracking-tight mb-4">{t('register.heroTitle')}</h2>
            <p className="text-white/70 text-lg leading-relaxed">{t('register.heroDesc')}</p>
          </div>
        </div>

        {/* Right Panel */}
        <div className="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
          <motion.div
            className="w-full max-w-md"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.4 }}
          >
            <div className="flex items-center gap-3 mb-8">
              <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                <UserPlus className="w-5 h-5 text-primary" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-gray-900">{t('register.title')}</h1>
                <p className="text-sm text-gray-500">{t('register.subtitle')}</p>
              </div>
            </div>

            <form onSubmit={handleSubmit} className="space-y-5">
              {/* Role Selection */}
              <div className="space-y-2">
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('register.role')}</label>
                <div className="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    onClick={() => setData('role', 'client')}
                    className={`p-4 border-2 rounded-xl text-start transition-all ${
                      data.role === 'client'
                        ? 'border-primary bg-primary/5'
                        : 'border-gray-200 hover:border-gray-300'
                    }`}
                  >
                    <Search className={`w-5 h-5 mb-2 ${data.role === 'client' ? 'text-primary' : 'text-gray-400'}`} />
                    <div className="text-sm font-semibold text-gray-900">{t('register.roleClient')}</div>
                    <div className="text-xs text-gray-500 mt-0.5">{t('register.roleClientDesc')}</div>
                  </button>
                  <button
                    type="button"
                    onClick={() => setData('role', 'consultant')}
                    className={`p-4 border-2 rounded-xl text-start transition-all ${
                      data.role === 'consultant'
                        ? 'border-primary bg-primary/5'
                        : 'border-gray-200 hover:border-gray-300'
                    }`}
                  >
                    <Briefcase className={`w-5 h-5 mb-2 ${data.role === 'consultant' ? 'text-primary' : 'text-gray-400'}`} />
                    <div className="text-sm font-semibold text-gray-900">{t('register.roleConsultant')}</div>
                    <div className="text-xs text-gray-500 mt-0.5">{t('register.roleConsultantDesc')}</div>
                  </button>
                </div>
                {errors.role && <p className="text-red-500 text-xs">{t(errors.role, errors.role)}</p>}
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('register.name')}</label>
                <input
                  type="text"
                  value={data.name}
                  onChange={(e) => setData('name', e.target.value)}
                  className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                />
                {errors.name && <p className="text-red-500 text-xs">{t(errors.name, errors.name)}</p>}
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('register.email')}</label>
                <input
                  type="email"
                  value={data.email}
                  onChange={(e) => setData('email', e.target.value)}
                  className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                />
                {errors.email && <p className="text-red-500 text-xs">{t(errors.email, errors.email)}</p>}
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('register.password')}</label>
                  <input
                    type="password"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                  />
                  {errors.password && <p className="text-red-500 text-xs">{t(errors.password, errors.password)}</p>}
                </div>
                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('register.confirmPassword')}</label>
                  <input
                    type="password"
                    value={data.password_confirmation}
                    onChange={(e) => setData('password_confirmation', e.target.value)}
                    className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={processing}
                className="w-full h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
              >
                {t('register.submit')}
              </button>
            </form>

            <p className="mt-6 text-center text-sm text-gray-500">
              {t('register.hasAccount')}{' '}
              <Link href="/login" className="text-primary font-semibold hover:text-primary-800">
                {t('register.login')}
              </Link>
            </p>
          </motion.div>
        </div>
      </div>
    </>
  );
}
