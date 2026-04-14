import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

// Import translation files
import enCommon from './locales/en/common.json';
import enNavigation from './locales/en/navigation.json';
import enHome from './locales/en/home.json';
import enServices from './locales/en/services.json';
import enPrograms from './locales/en/programs.json';
import enApply from './locales/en/apply.json';
import enStartupApplication from './locales/en/startup-application.json';
import enLegal from './locales/en/legal.json';
import enMeta from './locales/en/meta.json';
import enBlog from './locales/en/blog.json';
import enAuth from './locales/en/auth.json';
import enConsultants from './locales/en/consultants.json';
import enBookings from './locales/en/bookings.json';
import enDashboard from './locales/en/dashboard.json';
import enPayouts from './locales/en/payouts.json';
import enDesks from './locales/en/desks.json';
import enAgreement from './locales/en/agreement.json';


import arCommon from './locales/ar/common.json';
import arNavigation from './locales/ar/navigation.json';
import arHome from './locales/ar/home.json';
import arServices from './locales/ar/services.json';
import arPrograms from './locales/ar/programs.json';
import arApply from './locales/ar/apply.json';
import arStartupApplication from './locales/ar/startup-application.json';
import arLegal from './locales/ar/legal.json';
import arMeta from './locales/ar/meta.json';
import arBlog from './locales/ar/blog.json';
import arAuth from './locales/ar/auth.json';
import arConsultants from './locales/ar/consultants.json';
import arBookings from './locales/ar/bookings.json';
import arDashboard from './locales/ar/dashboard.json';
import arPayouts from './locales/ar/payouts.json';
import arDesks from './locales/ar/desks.json';
import arAgreement from './locales/ar/agreement.json';


// Configure i18next
i18n
  .use(LanguageDetector) // Detect user language
  .use(initReactI18next) // Pass i18n instance to react-i18next
  .init({
    resources: {
      en: {
        common: enCommon,
        navigation: enNavigation,
        home: enHome,
        services: enServices,
        programs: enPrograms,
        apply: enApply,
        'startup-application': enStartupApplication,
        legal: enLegal,
        meta: enMeta,
        blog: enBlog,
        auth: enAuth,
        consultants: enConsultants,
        bookings: enBookings,
        dashboard: enDashboard,
        payouts: enPayouts,
        desks: enDesks,
        agreement: enAgreement,
      },

      ar: {
        common: arCommon,
        navigation: arNavigation,
        home: arHome,
        services: arServices,
        programs: arPrograms,
        apply: arApply,
        'startup-application': arStartupApplication,
        legal: arLegal,
        meta: arMeta,
        blog: arBlog,
        auth: arAuth,
        consultants: arConsultants,
        bookings: arBookings,
        dashboard: arDashboard,
        payouts: arPayouts,
        desks: arDesks,
        agreement: arAgreement,
      },

    },
    fallbackLng: 'en', // Use English if translation is missing
    debug: true, // Set to true for development debugging

    interpolation: {
      escapeValue: false, // React already escapes values
    },

    detection: {
      // Order of language detection (from highest to lowest priority)
      order: ['localStorage', 'navigator'],

      // Keys to look for in localStorage
      lookupLocalStorage: 'i18nextLng',

      // Cache user language
      caches: ['localStorage'],
    },

    react: {
      useSuspense: false, // Disable suspense for simpler integration
    },
  });

// Set RTL configuration
i18n.services.formatter?.add('ar', (value, lng, options) => {
  if (lng === 'ar') {
    return value;
  }
  return value;
});

export default i18n;
