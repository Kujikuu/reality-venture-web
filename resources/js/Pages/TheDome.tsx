import { Link, useForm, usePage } from '@inertiajs/react';
import { ArrowUpRight, CheckCircle2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { SEO } from '../Components/SEO';

type Mode = 'subscribe' | 'apply';
type Locale = 'ar' | 'en';

interface DomeFormData {
  submission_uuid: string;
  holder_type: 'individual' | 'company';
  requested_tier: 'connect' | 'engage';
  full_name: string;
  email: string;
  phone: string;
  city: string;
  position: string;
  professional_role: string;
  interests: string[];
  joining_motivation: string;
  organization_name: string;
  organization_type: string;
  industry: string;
  website_url: string;
  consent: boolean;
}

interface Props {
  mode: Mode;
  locale: Locale;
  submissionUuid: string;
  requestedTier?: 'connect' | 'engage' | null;
}

interface SharedProps {
  flash: { success?: string; notice?: string; error?: string };
  [key: string]: unknown;
}

const copy = {
  ar: {
    eyebrow: 'مجتمع واحد. ثلاث منظومات.', subscribeTitle: 'ابقَ قريباً من The Dome', subscribeBody: 'استقبل رؤى وفرصاً وتحديثات مختارة من مجتمع سنايبر ورياليتي فنتشر وGRIT.',
    applyTitle: 'قدّم طلب الانضمام إلى The Dome', applyBody: 'اختر مسار العضوية الأنسب لطريقة تواصلك ومساهمتك ونموك.', subscribe: 'اشترك', apply: 'إرسال طلب العضوية',
    switchSubscribe: 'أرغب في الاشتراك فقط', switchApply: 'أرغب في طلب عضوية', holder: 'صاحب العضوية', individual: 'فرد', company: 'شركة', tier: 'فئة العضوية',
    connect: 'The Dome Connect', engage: 'The Dome Engage', name: 'الاسم الكامل', email: 'البريد الإلكتروني للعمل', phone: 'رقم الجوال', city: 'المدينة', position: 'المسمى الوظيفي', role: 'الدور المهني',
    organization: 'اسم المنشأة', organizationType: 'نوع المنشأة', industry: 'القطاع', website: 'الموقع الإلكتروني', motivation: 'لماذا ترغب في الانضمام إلى The Dome؟',
    interests: 'مجالات الاهتمام', consent: 'أوافق على سياسة الخصوصية ومعالجة The Dome لبياناتي لهذا الطلب.', errors: 'راجع الحقول التالية ثم أعد المحاولة:',
  },
  en: {
    eyebrow: 'One community. Three ventures.', subscribeTitle: 'Stay close to The Dome', subscribeBody: 'Receive selected insights, opportunities, and updates from Sniper, Reality Venture, and GRIT.',
    applyTitle: 'Apply to The Dome', applyBody: 'Choose the membership path that fits how you want to connect, contribute, and grow.', subscribe: 'Subscribe', apply: 'Submit membership application',
    switchSubscribe: 'I only want to subscribe', switchApply: 'I want to apply for membership', holder: 'Membership holder', individual: 'Individual', company: 'Company', tier: 'Requested tier',
    connect: 'The Dome Connect', engage: 'The Dome Engage', name: 'Full name', email: 'Work email', phone: 'Mobile number', city: 'City', position: 'Position', role: 'Professional role',
    organization: 'Organization name', organizationType: 'Organization type', industry: 'Industry', website: 'Website', motivation: 'Why would you like to join The Dome?',
    interests: 'Areas of interest', consent: 'I agree to the privacy terms and to The Dome processing my details for this request.', errors: 'Review these fields and try again:',
  },
} as const;

const interests = ['startups', 'proptech', 'investment', 'venture_building', 'technology', 'real_estate', 'entrepreneurship', 'innovation'];
const fieldClass = 'mt-2 min-h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/15';

export default function TheDome({ mode, locale, submissionUuid, requestedTier }: Props) {
  const text = copy[locale];
  const isApplication = mode === 'apply';
  const { flash } = usePage<SharedProps>().props;
  const form = useForm<DomeFormData>({
    submission_uuid: submissionUuid, holder_type: 'individual', requested_tier: requestedTier ?? 'connect', full_name: '', email: '', phone: '', city: '', position: '', professional_role: '', interests: [], joining_motivation: '', organization_name: '', organization_type: '', industry: '', website_url: '', consent: false,
  });

  function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    form.post(`/the-dome/${mode}`, { preserveScroll: true });
  }

  function toggleInterest(value: string) {
    form.setData('interests', form.data.interests.includes(value) ? form.data.interests.filter((item) => item !== value) : [...form.data.interests, value]);
  }

  return (
    <>
      <SEO />
      <section className="relative overflow-hidden bg-[#062d2d] text-white">
        <div className="absolute inset-y-0 end-0 w-1/2 bg-[radial-gradient(circle_at_center,rgba(215,169,74,0.18),transparent_65%)]" />
        <div className="relative mx-auto grid max-w-7xl gap-10 px-5 py-16 sm:px-8 md:py-24 lg:grid-cols-12 lg:items-end lg:px-12">
          <div className="lg:col-span-8">
            <p className="text-xs font-semibold uppercase tracking-[0.25em] text-[#d7a94a]">{text.eyebrow}</p>
            <h1 className="mt-6 max-w-4xl text-4xl font-bold uppercase leading-[0.95] tracking-tight sm:text-6xl lg:text-8xl">{isApplication ? text.applyTitle : text.subscribeTitle}</h1>
          </div>
          <div className="lg:col-span-4">
            <p className="text-base leading-8 text-white/70">{isApplication ? text.applyBody : text.subscribeBody}</p>
            <Link href={`/the-dome/${isApplication ? 'subscribe' : 'apply'}`} className="mt-6 inline-flex items-center gap-2 border-b border-[#d7a94a] pb-1 text-sm font-semibold text-[#f2d18c]">
              {isApplication ? text.switchSubscribe : text.switchApply}<ArrowUpRight className="size-4 rtl:-scale-x-100" />
            </Link>
          </div>
        </div>
      </section>

      <section className="bg-[#f4f1ea] px-5 py-12 sm:px-8 md:py-20">
        <form onSubmit={submit} noValidate className="mx-auto max-w-4xl rounded-3xl border border-black/5 bg-white p-5 shadow-[0_20px_70px_rgba(6,45,45,0.09)] sm:p-9 lg:p-12">
          {(flash.success || flash.notice || flash.error) && <div role="status" className={`mb-8 rounded-2xl border-s-4 p-5 text-sm ${flash.error ? 'border-red-700 bg-red-50 text-red-900' : 'border-[#d7a94a] bg-[#fff8e8] text-[#062d2d]'}`}>{!flash.error && <CheckCircle2 className="mb-3 size-6" />}{flash.success ?? flash.notice ?? flash.error}</div>}
          {form.hasErrors && <div role="alert" className="mb-8 rounded-2xl border-s-4 border-red-700 bg-red-50 p-5 text-red-900"><p className="font-semibold">{text.errors}</p><ul className="mt-2 list-disc space-y-1 ps-5 text-sm">{Object.values(form.errors).map((error) => <li key={error}>{error}</li>)}</ul></div>}

          {isApplication && <div className="mb-8 grid gap-6 sm:grid-cols-2">
            <Select label={text.holder} value={form.data.holder_type} onChange={(value) => form.setData('holder_type', value as DomeFormData['holder_type'])} options={[["individual", text.individual], ["company", text.company]]} />
            <Select label={text.tier} value={form.data.requested_tier} onChange={(value) => form.setData('requested_tier', value as DomeFormData['requested_tier'])} options={[["connect", text.connect], ["engage", text.engage]]} />
          </div>}

          <div className="grid gap-x-7 gap-y-6 sm:grid-cols-2">
            <Input label={text.name} value={form.data.full_name} error={form.errors.full_name} onChange={(value) => form.setData('full_name', value)} required />
            <Input label={text.email} type="email" value={form.data.email} error={form.errors.email} onChange={(value) => form.setData('email', value)} required />
            <Input label={text.phone} type="tel" value={form.data.phone} error={form.errors.phone} onChange={(value) => form.setData('phone', value)} required={isApplication} />
            <Input label={text.city} value={form.data.city} error={form.errors.city} onChange={(value) => form.setData('city', value)} required={isApplication} />
            <Input label={text.position} value={form.data.position} error={form.errors.position} onChange={(value) => form.setData('position', value)} />
            <Input label={text.role} value={form.data.professional_role} error={form.errors.professional_role} onChange={(value) => form.setData('professional_role', value)} />
          </div>

          {isApplication && form.data.holder_type === 'company' && <div className="mt-8 grid gap-x-7 gap-y-6 rounded-2xl bg-[#f4f1ea] p-5 sm:grid-cols-2 sm:p-7">
            <Input label={text.organization} value={form.data.organization_name} error={form.errors.organization_name} onChange={(value) => form.setData('organization_name', value)} required />
            <Input label={text.organizationType} value={form.data.organization_type} error={form.errors.organization_type} onChange={(value) => form.setData('organization_type', value)} />
            <Input label={text.industry} value={form.data.industry} error={form.errors.industry} onChange={(value) => form.setData('industry', value)} />
            <Input label={text.website} type="url" value={form.data.website_url} error={form.errors.website_url} onChange={(value) => form.setData('website_url', value)} />
          </div>}

          <fieldset className="mt-8"><legend className="text-sm font-semibold text-gray-900">{text.interests}</legend><div className="mt-3 flex flex-wrap gap-2">{interests.map((interest) => <label key={interest} className="cursor-pointer rounded-full border border-gray-300 px-4 py-2 text-xs font-medium capitalize has-checked:border-[#062d2d] has-checked:bg-[#062d2d] has-checked:text-white"><input className="sr-only" type="checkbox" checked={form.data.interests.includes(interest)} onChange={() => toggleInterest(interest)} />{interest.replace('_', ' ')}</label>)}</div></fieldset>
          {isApplication && <label className="mt-8 block text-sm font-semibold text-gray-900">{text.motivation} *<textarea rows={5} value={form.data.joining_motivation} onChange={(event) => form.setData('joining_motivation', event.target.value)} className={fieldClass} />{form.errors.joining_motivation && <span className="mt-1 block text-xs text-red-700">{form.errors.joining_motivation}</span>}</label>}
          <label className="mt-8 flex items-start gap-3 rounded-2xl bg-[#f4f1ea] p-5 text-sm leading-7 text-gray-700"><input type="checkbox" checked={form.data.consent} onChange={(event) => form.setData('consent', event.target.checked)} className="mt-1 size-5 accent-[#062d2d]" required /><span>{text.consent}</span></label>
          <button type="submit" disabled={form.processing} className="mt-8 min-h-13 w-full rounded-xl bg-[#062d2d] px-6 py-3 font-semibold text-white transition hover:bg-[#0a4444] disabled:cursor-wait disabled:opacity-60">{isApplication ? text.apply : text.subscribe}</button>
        </form>
      </section>
    </>
  );
}

function Input({ label, value, type = 'text', error, onChange, required = false }: { label: string; value: string; type?: string; error?: string; onChange: (value: string) => void; required?: boolean }) {
  return <label className="text-sm font-semibold text-gray-900">{label}{required && ' *'}<input type={type} value={value} onChange={(event) => onChange(event.target.value)} required={required} className={fieldClass} aria-invalid={Boolean(error)} />{error && <span className="mt-1 block text-xs text-red-700">{error}</span>}</label>;
}

function Select({ label, value, onChange, options }: { label: string; value: string; onChange: (value: string) => void; options: readonly (readonly [string, string])[] }) {
  return <label className="text-sm font-semibold text-gray-900">{label} *<select value={value} onChange={(event) => onChange(event.target.value)} className={fieldClass}>{options.map(([optionValue, optionLabel]) => <option key={optionValue} value={optionValue}>{optionLabel}</option>)}</select></label>;
}
