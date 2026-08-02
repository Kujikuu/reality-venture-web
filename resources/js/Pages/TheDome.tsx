import { Link, useForm, usePage } from '@inertiajs/react';
import { CheckCircle2 } from 'lucide-react';
import { forwardRef, useRef, useState } from 'react';
import type { FormEvent } from 'react';

import { SEO } from '../Components/SEO';

type Mode = 'subscribe' | 'apply';
type Locale = 'ar' | 'en';
type FormStep = 1 | 2;

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
    eyebrow: 'مجتمع واحد. ثلاث منظومات.',
    subscribeTitle: 'ابقَ قريباً من DOME™',
    subscribeBody: 'استقبل رؤى وفرصاً وتحديثات مختارة من مجتمع سنايبر ورياليتي فنتشر وGRIT.',
    applyTitle: 'قدّم طلب الانضمام إلى DOME™',
    applyBody: 'اختر مسار العضوية الأنسب لطريقة تواصلك ومساهمتك ونموك.',
    subscribe: 'اشترك',
    apply: 'إرسال طلب العضوية',
    switchSubscribe: 'أرغب في الاشتراك فقط',
    switchApply: 'أرغب في طلب عضوية',
    progress: 'تقدم النموذج',
    stepOne: 'الخطوة 1 من 2',
    stepTwo: 'الخطوة 2 من 2',
    subscriptionStepOne: 'بيانات التواصل',
    subscriptionStepTwo: 'اهتماماتك',
    applicationStepOne: 'أساسيات العضوية',
    applicationStepTwo: 'ملفك وأهدافك',
    requiredHint: 'أكمل الحقول المطلوبة للمتابعة.',
    optionalHint: 'تساعدنا التفاصيل الاختيارية في إرسال فرص أكثر ملاءمة لك.',
    applicationHint: 'أخبرنا عن خبرتك وما الذي تريد تحقيقه من العضوية.',
    continue: 'متابعة',
    back: 'رجوع',
    holder: 'صاحب العضوية',
    individual: 'فرد',
    company: 'شركة',
    tier: 'فئة العضوية',
    connect: 'DOME™ Connect',
    engage: 'DOME™ Engage',
    name: 'الاسم الكامل',
    email: 'البريد الإلكتروني للعمل',
    phone: 'رقم الجوال',
    city: 'المدينة',
    position: 'المسمى الوظيفي',
    role: 'الدور المهني',
    selectOptional: 'اختر خياراً (اختياري)',
    organization: 'اسم المنشأة',
    organizationType: 'نوع المنشأة',
    industry: 'القطاع',
    website: 'الموقع الإلكتروني',
    motivation: 'لماذا ترغب في الانضمام إلى DOME™؟',
    interests: 'مجالات الاهتمام',
    consent: 'أوافق على سياسة الخصوصية ومعالجة DOME™ لبياناتي لهذا الطلب.',
    errors: 'راجع الحقول التالية ثم أعد المحاولة:',
  },
  en: {
    eyebrow: 'One community. Three ventures.',
    subscribeTitle: 'Stay close to DOME™',
    subscribeBody: 'Receive selected insights, opportunities, and updates from Sniper, Reality Venture, and GRIT.',
    applyTitle: 'Apply to DOME™',
    applyBody: 'Choose the membership path that fits how you want to connect, contribute, and grow.',
    subscribe: 'Subscribe',
    apply: 'Submit membership application',
    switchSubscribe: 'I only want to subscribe',
    switchApply: 'I want to apply for membership',
    progress: 'Form progress',
    stepOne: 'Step 1 of 2',
    stepTwo: 'Step 2 of 2',
    subscriptionStepOne: 'Your contact details',
    subscriptionStepTwo: 'Your interests',
    applicationStepOne: 'Membership basics',
    applicationStepTwo: 'Your profile and goals',
    requiredHint: 'Complete the required fields to continue.',
    optionalHint: 'Optional details help us send you more relevant opportunities.',
    applicationHint: 'Tell us about your experience and what you want from membership.',
    continue: 'Continue',
    back: 'Back',
    holder: 'Membership holder',
    individual: 'Individual',
    company: 'Company',
    tier: 'Requested tier',
    connect: 'DOME™ Connect',
    engage: 'DOME™ Engage',
    name: 'Full name',
    email: 'Work email',
    phone: 'Mobile number',
    city: 'City',
    position: 'Position',
    role: 'Professional role',
    selectOptional: 'Select an option (optional)',
    organization: 'Organization name',
    organizationType: 'Organization type',
    industry: 'Industry',
    website: 'Website',
    motivation: 'Why would you like to join DOME™?',
    interests: 'Areas of interest',
    consent: 'I agree to the privacy terms and to DOME™ processing my details for this request.',
    errors: 'Review these fields and try again:',
  },
} as const;

const interestOptions = [
  ['startups', 'الشركات الناشئة', 'Startups'],
  ['proptech', 'تقنيات العقار', 'Proptech'],
  ['investment', 'الاستثمار', 'Investment'],
  ['venture_building', 'بناء المشاريع', 'Venture building'],
  ['technology', 'التقنية', 'Technology'],
  ['real_estate', 'العقار', 'Real estate'],
  ['entrepreneurship', 'ريادة الأعمال', 'Entrepreneurship'],
  ['innovation', 'الابتكار', 'Innovation'],
  ['games', 'الألعاب', 'Games'],
  ['sport', 'الرياضة', 'Sport'],
  ['hospitality', 'الضيافة', 'Hospitality'],
  ['food_and_beverage', 'الأغذية والمشروبات', 'Food & beverage'],
  ['healthcare', 'الرعاية الصحية', 'Healthcare'],
  ['ai_and_tech', 'الذكاء الاصطناعي والتقنية', 'AI & tech'],
  ['manufacturing', 'التصنيع', 'Manufacturing'],
] as const;

const roleOptions = [
  ['investor', 'مستثمر', 'Investor'],
  ['owner', 'مالك', 'Owner'],
  ['ceo', 'رئيس تنفيذي', 'CEO'],
  ['developer', 'مطور', 'Developer'],
  ['consultant', 'مستشار', 'Consultant'],
  ['employee', 'موظف', 'Employee'],
] as const;

const organizationTypeOptions = [
  ['public', 'قطاع حكومي', 'Public sector'],
  ['private', 'قطاع خاص', 'Private sector'],
  ['non_profit', 'قطاع غير ربحي', 'Non-profit'],
] as const;

const applicationStepTwoFields = new Set([
  'position',
  'professional_role',
  'interests',
  'organization_name',
  'organization_type',
  'industry',
  'website_url',
  'joining_motivation',
  'consent',
]);

const subscriptionStepTwoFields = new Set([
  'city',
  'position',
  'professional_role',
  'interests',
  'consent',
]);

const baseField = 'mt-2 min-h-12 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/15';

export default function TheDome({ mode, locale, submissionUuid, requestedTier }: Props) {
  const text = copy[locale];
  const isApplication = mode === 'apply';
  const { flash } = usePage<SharedProps>().props;
  const [step, setStep] = useState<FormStep>(1);
  const formRef = useRef<HTMLFormElement>(null);
  const stepOneHeadingRef = useRef<HTMLHeadingElement>(null);
  const stepTwoHeadingRef = useRef<HTMLHeadingElement>(null);
  const errorSummaryRef = useRef<HTMLDivElement>(null);
  const form = useForm<DomeFormData>({
    submission_uuid: submissionUuid,
    holder_type: 'individual',
    requested_tier: requestedTier ?? 'connect',
    full_name: '',
    email: '',
    phone: '',
    city: '',
    position: '',
    professional_role: '',
    interests: [],
    joining_motivation: '',
    organization_name: '',
    organization_type: '',
    industry: '',
    website_url: '',
    consent: false,
  });

  function focusHeading(nextStep: FormStep): void {
    window.requestAnimationFrame(() => {
      (nextStep === 1 ? stepOneHeadingRef.current : stepTwoHeadingRef.current)?.focus();
    });
  }

  function continueToStepTwo(): void {
    if (!formRef.current?.reportValidity()) {
      return;
    }

    setStep(2);
    focusHeading(2);
  }

  function returnToStepOne(): void {
    setStep(1);
    focusHeading(1);
  }

  function submit(event: FormEvent<HTMLFormElement>): void {
    event.preventDefault();
    form.post(`/the-dome/${mode}`, {
      preserveScroll: true,
      onError: (errors) => {
        setStep(stepForErrors(Object.keys(errors), isApplication));
        window.requestAnimationFrame(() => errorSummaryRef.current?.focus());
      },
      onSuccess: (page) => {
        const nextFlash = (page.props as unknown as SharedProps).flash;

        if (!nextFlash.success && !nextFlash.notice) {
          return;
        }

        form.reset();
        form.setData('submission_uuid', crypto.randomUUID());
        setStep(1);
      },
    });
  }

  function changeHolderType(value: DomeFormData['holder_type']): void {
    form.setData({
      ...form.data,
      holder_type: value,
      ...(value === 'individual'
        ? { organization_name: '', organization_type: '', industry: '', website_url: '' }
        : {}),
    });
  }

  function toggleInterest(value: string): void {
    form.setData(
      'interests',
      form.data.interests.includes(value)
        ? form.data.interests.filter((item) => item !== value)
        : [...form.data.interests, value],
    );
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
              {isApplication ? text.switchSubscribe : text.switchApply}
            </Link>
          </div>
        </div>
      </section>

      <section className="bg-[#f4f1ea] px-5 py-12 sm:px-8 md:py-20">
        <form ref={formRef} onSubmit={submit} className="mx-auto max-w-4xl rounded-3xl border border-black/5 bg-white p-5 shadow-[0_20px_70px_rgba(6,45,45,0.09)] sm:p-9 lg:p-12">
          <input type="hidden" name="submission_uuid" value={form.data.submission_uuid} />
          <StepProgress step={step} label={text.progress} stepOne={text.stepOne} stepTwo={text.stepTwo} />

          {(flash.success || flash.notice || flash.error) && (
            <div role="status" className={`mt-8 rounded-2xl border-s-4 p-5 text-sm ${flash.error ? 'border-red-700 bg-red-50 text-red-900' : 'border-[#d7a94a] bg-[#fff8e8] text-[#062d2d]'}`}>
              {!flash.error && <CheckCircle2 className="mb-3 size-6" />}
              {flash.success ?? flash.notice ?? flash.error}
            </div>
          )}

          {form.hasErrors && (
            <div ref={errorSummaryRef} role="alert" tabIndex={-1} className="mt-8 rounded-2xl border-s-4 border-red-700 bg-red-50 p-5 text-red-900 outline-none">
              <p className="font-semibold">{text.errors}</p>
              <ul className="mt-2 list-disc space-y-1 ps-5 text-sm">
                {Object.values(form.errors).map((error) => <li key={error}>{error}</li>)}
              </ul>
            </div>
          )}

          {step === 1 ? (
            <div data-dome-step="1" className="mt-8 flex flex-col gap-7">
              <StepHeading
                ref={stepOneHeadingRef}
                eyebrow={text.stepOne}
                title={isApplication ? text.applicationStepOne : text.subscriptionStepOne}
                hint={text.requiredHint}
              />

              {isApplication && (
                <div className="grid gap-6 sm:grid-cols-2">
                  <SelectField
                    label={text.holder}
                    name="holder_type"
                    value={form.data.holder_type}
                    error={form.errors.holder_type}
                    onChange={(value) => changeHolderType(value as DomeFormData['holder_type'])}
                    options={[["individual", text.individual], ["company", text.company]]}
                    required
                  />
                  <SelectField
                    label={text.tier}
                    name="requested_tier"
                    value={form.data.requested_tier}
                    error={form.errors.requested_tier}
                    onChange={(value) => form.setData('requested_tier', value as DomeFormData['requested_tier'])}
                    options={[["connect", text.connect], ["engage", text.engage]]}
                    required
                  />
                </div>
              )}

              <div className="grid gap-x-7 gap-y-6 sm:grid-cols-2">
                <InputField className="sm:col-span-2" label={text.name} name="full_name" value={form.data.full_name} error={form.errors.full_name} onChange={(value) => form.setData('full_name', value)} autoComplete="name" required />
                <InputField label={text.email} name="email" type="email" value={form.data.email} error={form.errors.email} onChange={(value) => form.setData('email', value)} autoComplete="email" required />
                <InputField label={text.phone} name="phone" type="tel" value={form.data.phone} error={form.errors.phone} onChange={(value) => form.setData('phone', value)} autoComplete="tel" inputMode="tel" forceLtr required />
                {isApplication && (
                  <InputField className="sm:col-span-2" label={text.city} name="city" value={form.data.city} error={form.errors.city} onChange={(value) => form.setData('city', value)} autoComplete="address-level2" required />
                )}
              </div>

              <button type="button" onClick={continueToStepTwo} className="min-h-13 w-full rounded-xl bg-[#062d2d] px-6 py-3 font-semibold text-white transition hover:bg-[#0a4444] focus-visible:outline-2 focus-visible:outline-offset-3 focus-visible:outline-[#062d2d] motion-reduce:transition-none">
                {text.continue}
              </button>
            </div>
          ) : (
            <div data-dome-step="2" className="mt-8 flex flex-col gap-7">
              <StepHeading
                ref={stepTwoHeadingRef}
                eyebrow={text.stepTwo}
                title={isApplication ? text.applicationStepTwo : text.subscriptionStepTwo}
                hint={isApplication ? text.applicationHint : text.optionalHint}
              />

              <div className="grid gap-x-7 gap-y-6 sm:grid-cols-2">
                {!isApplication && (
                  <InputField label={text.city} name="city" value={form.data.city} error={form.errors.city} onChange={(value) => form.setData('city', value)} autoComplete="address-level2" />
                )}
                <InputField label={text.position} name="position" value={form.data.position} error={form.errors.position} onChange={(value) => form.setData('position', value)} autoComplete="organization-title" />
                <SelectField
                  className={isApplication ? '' : 'sm:col-span-2'}
                  label={text.role}
                  name="professional_role"
                  value={form.data.professional_role}
                  error={form.errors.professional_role}
                  onChange={(value) => form.setData('professional_role', value)}
                  options={[["", text.selectOptional], ...localizedOptions(roleOptions, locale)]}
                />
              </div>

              {isApplication && form.data.holder_type === 'company' && (
                <div className="grid gap-x-7 gap-y-6 rounded-2xl bg-[#f4f1ea] p-5 sm:grid-cols-2 sm:p-7">
                  <InputField className="sm:col-span-2" label={text.organization} name="organization_name" value={form.data.organization_name} error={form.errors.organization_name} onChange={(value) => form.setData('organization_name', value)} autoComplete="organization" required />
                  <SelectField
                    label={text.organizationType}
                    name="organization_type"
                    value={form.data.organization_type}
                    error={form.errors.organization_type}
                    onChange={(value) => form.setData('organization_type', value)}
                    options={[["", text.selectOptional], ...localizedOptions(organizationTypeOptions, locale)]}
                  />
                  <InputField label={text.industry} name="industry" value={form.data.industry} error={form.errors.industry} onChange={(value) => form.setData('industry', value)} />
                  <InputField className="sm:col-span-2" label={text.website} name="website_url" type="url" value={form.data.website_url} error={form.errors.website_url} onChange={(value) => form.setData('website_url', value)} forceLtr placeholder="https://" />
                </div>
              )}

              <fieldset>
                <legend className="text-sm font-semibold text-gray-900">{text.interests}</legend>
                <div className="mt-3 grid max-h-72 gap-2 overflow-y-auto rounded-2xl border border-gray-200 bg-[#f4f1ea] p-3 sm:grid-cols-2">
                  {interestOptions.map(([value, arLabel, enLabel]) => (
                    <label key={value} className="flex cursor-pointer items-center gap-2 rounded-xl px-3 py-2 text-sm text-gray-800 transition hover:bg-white has-checked:bg-[#062d2d] has-checked:text-white">
                      <input className="size-4 rounded border-gray-300 accent-[#062d2d]" type="checkbox" checked={form.data.interests.includes(value)} onChange={() => toggleInterest(value)} />
                      <span>{locale === 'ar' ? arLabel : enLabel}</span>
                    </label>
                  ))}
                </div>
              </fieldset>

              {isApplication && (
                <label className="block text-sm font-semibold text-gray-900">
                  {text.motivation} *
                  <textarea
                    name="joining_motivation"
                    rows={5}
                    minLength={20}
                    maxLength={3000}
                    required
                    value={form.data.joining_motivation}
                    onChange={(event) => form.setData('joining_motivation', event.target.value)}
                    className={`${baseField} min-h-36 resize-y`}
                    aria-invalid={Boolean(form.errors.joining_motivation)}
                  />
                  {form.errors.joining_motivation && <span className="mt-1 block text-xs text-red-700">{form.errors.joining_motivation}</span>}
                </label>
              )}

              <div>
                <label className="flex items-start gap-3 rounded-2xl bg-[#f4f1ea] p-5 text-sm leading-7 text-gray-700">
                  <input type="checkbox" checked={form.data.consent} onChange={(event) => form.setData('consent', event.target.checked)} className="mt-1 size-5 accent-[#062d2d]" required />
                  <span>{text.consent}</span>
                </label>
                {form.errors.consent && <p className="mt-2 text-xs text-red-700">{form.errors.consent}</p>}
              </div>

              <div className="flex flex-col-reverse gap-3 sm:flex-row">
                <button type="button" onClick={returnToStepOne} className="min-h-13 rounded-xl border border-[#062d2d]/20 bg-white px-6 py-3 font-semibold text-[#062d2d] transition hover:border-[#062d2d] focus-visible:outline-2 focus-visible:outline-offset-3 focus-visible:outline-[#062d2d] sm:w-auto motion-reduce:transition-none">
                  {text.back}
                </button>
                <button type="submit" disabled={form.processing} className="min-h-13 flex-1 rounded-xl bg-[#062d2d] px-6 py-3 font-semibold text-white transition hover:bg-[#0a4444] focus-visible:outline-2 focus-visible:outline-offset-3 focus-visible:outline-[#062d2d] disabled:cursor-wait disabled:opacity-60 motion-reduce:transition-none">
                  {isApplication ? text.apply : text.subscribe}
                </button>
              </div>
            </div>
          )}
        </form>
      </section>
    </>
  );
}

function StepProgress({ step, label, stepOne, stepTwo }: { step: FormStep; label: string; stepOne: string; stepTwo: string }) {
  return (
    <ol aria-label={label} className="grid grid-cols-2 gap-3">
      {[stepOne, stepTwo].map((stepLabel, index) => {
        const stepNumber = (index + 1) as FormStep;
        const active = step === stepNumber;

        return (
          <li key={stepLabel} aria-current={active ? 'step' : undefined} className={`rounded-xl border px-4 py-3 text-sm font-semibold transition-colors ${active ? 'border-[#062d2d] bg-[#062d2d] text-white' : 'border-gray-200 bg-[#f4f1ea] text-gray-500'}`}>
            {stepLabel}
          </li>
        );
      })}
    </ol>
  );
}

const StepHeading = forwardRef<HTMLHeadingElement, { eyebrow: string; title: string; hint: string }>(function StepHeading({ eyebrow, title, hint }, ref) {
  return (
    <div>
      <p className="text-xs font-semibold uppercase tracking-[0.18em] text-[#a67720]">{eyebrow}</p>
      <h2 ref={ref} tabIndex={-1} className="mt-2 text-3xl font-bold text-[#062d2d] outline-none">{title}</h2>
      <p className="mt-2 text-sm leading-6 text-gray-600">{hint}</p>
    </div>
  );
});

function InputField({ className = '', label, name, value, type = 'text', error, onChange, required = false, autoComplete, inputMode, forceLtr = false, placeholder }: {
  className?: string;
  label: string;
  name: string;
  value: string;
  type?: string;
  error?: string;
  onChange: (value: string) => void;
  required?: boolean;
  autoComplete?: string;
  inputMode?: 'tel';
  forceLtr?: boolean;
  placeholder?: string;
}) {
  return (
    <label className={`text-sm font-semibold text-gray-900 ${className}`}>
      {label}{required && ' *'}
      <input name={name} type={type} dir={forceLtr ? 'ltr' : undefined} inputMode={inputMode} value={value} onChange={(event) => onChange(event.target.value)} required={required} autoComplete={autoComplete} placeholder={placeholder} className={`${baseField} ${forceLtr ? 'text-left' : ''}`} aria-invalid={Boolean(error)} />
      {error && <span className="mt-1 block text-xs text-red-700">{error}</span>}
    </label>
  );
}

function SelectField({ className = '', label, name, value, error, onChange, options, required = false }: {
  className?: string;
  label: string;
  name: string;
  value: string;
  error?: string;
  onChange: (value: string) => void;
  options: readonly (readonly [string, string])[];
  required?: boolean;
}) {
  return (
    <label className={`text-sm font-semibold text-gray-900 ${className}`}>
      {label}{required && ' *'}
      <select name={name} value={value} onChange={(event) => onChange(event.target.value)} required={required} className={baseField} aria-invalid={Boolean(error)}>
        {options.map(([optionValue, optionLabel]) => <option key={optionValue || 'empty'} value={optionValue}>{optionLabel}</option>)}
      </select>
      {error && <span className="mt-1 block text-xs text-red-700">{error}</span>}
    </label>
  );
}

function localizedOptions(options: readonly (readonly [string, string, string])[], locale: Locale): [string, string][] {
  return options.map(([value, arLabel, enLabel]) => [value, locale === 'ar' ? arLabel : enLabel]);
}

function stepForErrors(errorFields: string[], isApplication: boolean): FormStep {
  const secondStepFields = isApplication ? applicationStepTwoFields : subscriptionStepTwoFields;
  const firstErrorField = errorFields[0]?.split('.')[0];

  return firstErrorField && secondStepFields.has(firstErrorField) ? 2 : 1;
}
