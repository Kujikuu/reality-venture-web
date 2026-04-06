import { useState } from 'react';
import { usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LayoutDashboard, Settings, Save, UserCircle, Camera } from 'lucide-react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { PageProps } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';

interface Props {
  userData: {
    name: string;
    email: string;
    avatar_url: string | null;
  };
}

export default function ClientSettings({ userData }: Props) {
  const { t } = useTranslation('dashboard');
  const { flash } = usePage<PageProps>().props;

  const [name, setName] = useState(userData.name);
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [avatarPreview, setAvatarPreview] = useState<string | null>(userData.avatar_url);
  const [processing, setProcessing] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const sidebarLinks = [
    { href: '/dashboard', icon: LayoutDashboard, label: t('client.bookings') },
    { href: '/dashboard/settings', icon: Settings, label: t('client.settings') },
  ];

  const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setAvatarFile(file);
      setAvatarPreview(URL.createObjectURL(file));
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    router.post('/dashboard/settings', {
      name,
      avatar: avatarFile,
    }, {
      forceFormData: true,
      onStart: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onError: (errs) => setErrors(errs),
    });
  };

  return (
    <>
      <SEO />
      <DashboardLayout links={sidebarLinks} title={t('client.settingsTitle')}>

        {flash.success && (
          <div className="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {t(flash.success, flash.success)}
          </div>
        )}

        {Object.keys(errors).length > 0 && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {Object.values(errors).map((err, i) => <p key={i}>{t(err, err)}</p>)}
          </div>
        )}

        <form onSubmit={handleSubmit} className="max-w-xl space-y-6">
          {/* Avatar */}
          <div className="bg-white border border-gray-200 rounded-xl p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">{t('client.avatar')}</h2>
            <div className="flex items-center gap-5">
              <div className="relative group">
                {avatarPreview ? (
                  <img src={avatarPreview} alt="" className="w-20 h-20 rounded-full object-cover ring-2 ring-gray-100" />
                ) : (
                  <div className="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center ring-2 ring-gray-100">
                    <UserCircle className="w-10 h-10 text-primary/40" />
                  </div>
                )}
                <label className="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                  <Camera className="w-5 h-5 text-white" />
                  <input type="file" accept="image/jpeg,image/png,image/webp" onChange={handleAvatarChange} className="hidden" />
                </label>
              </div>
              <div>
                <label className="inline-flex px-4 py-2 text-sm font-medium text-primary border border-primary/30 rounded-lg hover:bg-primary/5 cursor-pointer transition-colors">
                  {avatarPreview ? t('client.changePhoto') : t('client.uploadPhoto')}
                  <input type="file" accept="image/jpeg,image/png,image/webp" onChange={handleAvatarChange} className="hidden" />
                </label>
                <p className="text-xs text-gray-400 mt-2">{t('client.photoHint')}</p>
              </div>
            </div>
            {errors.avatar && <p className="text-red-500 text-xs mt-2">{errors.avatar}</p>}
          </div>

          {/* Personal Info */}
          <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h2 className="text-lg font-semibold text-gray-900">{t('client.personalInfo')}</h2>

            <div className="space-y-1.5">
              <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('client.fullName')} *</label>
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
              />
              {errors.name && <p className="text-red-500 text-xs">{errors.name}</p>}
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('client.email')}</label>
              <input
                type="email"
                value={userData.email}
                disabled
                className="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed"
              />
              <p className="text-xs text-gray-400">{t('client.emailHint')}</p>
            </div>
          </div>

          {/* Submit */}
          <div className="flex justify-end">
            <button
              type="submit"
              disabled={processing}
              className="flex items-center gap-2 px-8 h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
            >
              <Save className="w-4 h-4" />
              {t('client.saveSettings')}
            </button>
          </div>
        </form>
      </DashboardLayout>
    </>
  );
}
