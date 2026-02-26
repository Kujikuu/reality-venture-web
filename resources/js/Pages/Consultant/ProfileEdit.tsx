import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Save, ExternalLink, LayoutDashboard, Calendar, DollarSign, UserCircle, Camera, Wallet } from 'lucide-react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { Specialization, PageProps } from '../../types/marketplace';

interface ConsultantProfileData {
  id: number;
  bio_en: string;
  bio_ar: string | null;
  years_experience: number;
  hourly_rate: string;
  languages: string[];
  timezone: string;
  response_time_hours: number;
  calendly_event_type_url: string | null;
  specializations: { id: number }[];
}

interface Props {
  profile: ConsultantProfileData;
  avatarUrl: string | null;
  specializations: Specialization[];
}

const LANGUAGES = [
  { value: 'en', label: 'English' },
  { value: 'ar', label: 'العربية' },
  { value: 'fr', label: 'Français' },
  { value: 'es', label: 'Español' },
  { value: 'de', label: 'Deutsch' },
  { value: 'zh', label: '中文' },
  { value: 'hi', label: 'हिन्दी' },
  { value: 'ur', label: 'اردو' },
];

export default function ProfileEdit({ profile, avatarUrl, specializations }: Props) {
  const { t } = useTranslation('dashboard');
  const { flash } = usePage<PageProps>().props;

  const [avatarPreview, setAvatarPreview] = useState<string | null>(avatarUrl);
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [processing, setProcessing] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const [formData, setFormData] = useState({
    bio_en: profile.bio_en,
    bio_ar: profile.bio_ar ?? '',
    specialization_ids: profile.specializations.map((s) => s.id),
    years_experience: profile.years_experience,
    hourly_rate: parseFloat(profile.hourly_rate),
    languages: profile.languages,
    timezone: profile.timezone,
    response_time_hours: profile.response_time_hours,
    calendly_event_type_url: profile.calendly_event_type_url ?? '',
  });

  const setField = <K extends keyof typeof formData>(key: K, value: (typeof formData)[K]) => {
    setFormData((prev) => ({ ...prev, [key]: value }));
  };

  const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setAvatarFile(file);
      setAvatarPreview(URL.createObjectURL(file));
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    router.post('/consultant/profile', {
      ...formData,
      avatar: avatarFile,
    }, {
      forceFormData: true,
      onStart: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onError: (errs) => setErrors(errs),
    });
  };

  const toggleSpecialization = (id: number) => {
    setField('specialization_ids',
      formData.specialization_ids.includes(id)
        ? formData.specialization_ids.filter((s) => s !== id)
        : [...formData.specialization_ids, id]
    );
  };

  const toggleLanguage = (lang: string) => {
    setField('languages',
      formData.languages.includes(lang)
        ? formData.languages.filter((l) => l !== lang)
        : [...formData.languages, lang]
    );
  };

  const sidebarLinks = [
    { href: '/consultant/dashboard', icon: LayoutDashboard, label: t('consultant.overview') },
    { href: '/consultant/bookings', icon: Calendar, label: t('consultant.bookings') },
    { href: '/consultant/earnings', icon: DollarSign, label: t('consultant.earnings') },
    { href: '/consultant/wallet', icon: Wallet, label: t('consultant.wallet') },
    { href: '/consultant/profile/edit', icon: UserCircle, label: t('consultant.profileEdit') },
  ];

  return (
    <DashboardLayout links={sidebarLinks} title={t('consultant.profileEditTitle')}>
      <Head title={t('consultant.profileEditTitle')} />
      <div>

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

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {/* Left Column */}
            <div className="space-y-6">
              {/* Avatar */}
              <div className="bg-white border border-gray-200 rounded-xl p-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">{t('consultant.avatar')}</h2>
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
                      {avatarPreview ? t('consultant.changePhoto') : t('consultant.uploadPhoto')}
                      <input type="file" accept="image/jpeg,image/png,image/webp" onChange={handleAvatarChange} className="hidden" />
                    </label>
                    <p className="text-xs text-gray-400 mt-2">{t('consultant.photoHint')}</p>
                  </div>
                </div>
                {errors.avatar && <p className="text-red-500 text-xs mt-2">{errors.avatar}</p>}
              </div>

              {/* Bio */}
              <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
                <h2 className="text-lg font-semibold text-gray-900">{t('consultant.bio')}</h2>

                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.bioEn')} *</label>
                  <textarea
                    rows={5}
                    value={formData.bio_en}
                    onChange={(e) => setField('bio_en', e.target.value)}
                    className="w-full p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none"
                  />
                  {errors.bio_en && <p className="text-red-500 text-xs">{errors.bio_en}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">
                    {t('consultant.bioAr')} <span className="text-gray-400 normal-case">({t('consultant.optional')})</span>
                  </label>
                  <textarea
                    rows={5}
                    value={formData.bio_ar}
                    onChange={(e) => setField('bio_ar', e.target.value)}
                    className="w-full p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none"
                    dir="rtl"
                  />
                </div>
              </div>

              {/* Specializations */}
              <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h2 className="text-lg font-semibold text-gray-900">{t('consultant.specializations')}</h2>
                <div className="flex flex-wrap gap-2">
                  {specializations.map((spec) => (
                    <button
                      key={spec.id}
                      type="button"
                      onClick={() => toggleSpecialization(spec.id)}
                      className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                        formData.specialization_ids.includes(spec.id)
                          ? 'bg-primary text-white'
                          : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                      }`}
                    >
                      {spec.name_en}
                    </button>
                  ))}
                </div>
                {errors.specialization_ids && <p className="text-red-500 text-xs">{errors.specialization_ids}</p>}
              </div>

              {/* Calendly */}
              <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h2 className="text-lg font-semibold text-gray-900">{t('consultant.calendlyIntegration')}</h2>
                <div className="p-4 bg-primary/5 border border-primary/10 rounded-xl">
                  <p className="text-sm text-gray-700 leading-relaxed">{t('consultant.calendlyDescription')}</p>
                  <a
                    href="https://calendly.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-1 mt-2 text-sm font-medium text-primary hover:text-primary-800"
                  >
                    {t('consultant.goToCalendly')} <ExternalLink className="w-3.5 h-3.5" />
                  </a>
                </div>
                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.calendlyEventUrl')} *</label>
                  <input
                    type="url"
                    value={formData.calendly_event_type_url}
                    onChange={(e) => setField('calendly_event_type_url', e.target.value)}
                    placeholder="https://calendly.com/yourname/30min"
                    className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                  />
                  {errors.calendly_event_type_url && <p className="text-red-500 text-xs">{errors.calendly_event_type_url}</p>}
                </div>
              </div>
            </div>

            {/* Right Column */}
            <div className="space-y-6">
              {/* Rate & Experience */}
              <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
                <h2 className="text-lg font-semibold text-gray-900">{t('consultant.rateExperience')}</h2>

                <div className="space-y-4">
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.hourlyRate')}</label>
                    <input
                      type="number"
                      min={50}
                      max={10000}
                      value={formData.hourly_rate}
                      onChange={(e) => setField('hourly_rate', parseInt(e.target.value) || 0)}
                      className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                    />
                    {errors.hourly_rate && <p className="text-red-500 text-xs">{errors.hourly_rate}</p>}
                  </div>
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.yearsExperience')}</label>
                    <input
                      type="number"
                      min={0}
                      max={50}
                      value={formData.years_experience}
                      onChange={(e) => setField('years_experience', parseInt(e.target.value) || 0)}
                      className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                    />
                  </div>
                </div>
              </div>

              {/* Languages */}
              <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h2 className="text-lg font-semibold text-gray-900">{t('consultant.languages')}</h2>
                <div className="flex flex-wrap gap-2">
                  {LANGUAGES.map((lang) => (
                    <button
                      key={lang.value}
                      type="button"
                      onClick={() => toggleLanguage(lang.value)}
                      className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                        formData.languages.includes(lang.value)
                          ? 'bg-primary text-white'
                          : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                      }`}
                    >
                      {lang.label}
                    </button>
                  ))}
                </div>
              </div>

              {/* Timezone & Response Time */}
              <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
                <h2 className="text-lg font-semibold text-gray-900">{t('consultant.availability')}</h2>

                <div className="space-y-4">
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.timezone')}</label>
                    <select
                      value={formData.timezone}
                      onChange={(e) => setField('timezone', e.target.value)}
                      className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                    >
                      <option value="Asia/Riyadh">Asia/Riyadh (GMT+3)</option>
                      <option value="Asia/Dubai">Asia/Dubai (GMT+4)</option>
                      <option value="Asia/Bahrain">Asia/Bahrain (GMT+3)</option>
                      <option value="Asia/Qatar">Asia/Qatar (GMT+3)</option>
                      <option value="Asia/Kuwait">Asia/Kuwait (GMT+3)</option>
                      <option value="Asia/Muscat">Asia/Muscat (GMT+4)</option>
                      <option value="Asia/Baghdad">Asia/Baghdad (GMT+3)</option>
                      <option value="Asia/Amman">Asia/Amman (GMT+3)</option>
                      <option value="Asia/Beirut">Asia/Beirut (GMT+2)</option>
                      <option value="Africa/Cairo">Africa/Cairo (GMT+2)</option>
                    </select>
                  </div>
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.responseTime')}</label>
                    <input
                      type="number"
                      min={1}
                      max={72}
                      value={formData.response_time_hours}
                      onChange={(e) => setField('response_time_hours', parseInt(e.target.value) || 24)}
                      className="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Submit */}
          <div className="flex justify-end mt-6">
            <button
              type="submit"
              disabled={processing}
              className="flex items-center gap-2 px-8 h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
            >
              <Save className="w-4 h-4" />
              {t('consultant.saveChanges')}
            </button>
          </div>
        </form>
      </div>
    </DashboardLayout>
  );
}
