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
import enLegal from './locales/en/legal.json';
import enMeta from './locales/en/meta.json';

import arCommon from './locales/ar/common.json';
import arNavigation from './locales/ar/navigation.json';
import arHome from './locales/ar/home.json';
import arServices from './locales/ar/services.json';
import arPrograms from './locales/ar/programs.json';
import arApply from './locales/ar/apply.json';
import arLegal from './locales/ar/legal.json';
import arMeta from './locales/ar/meta.json';

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
        legal: enLegal,
        meta: enMeta,
      },
      ar: {
        common: arCommon,
        navigation: arNavigation,
        home: arHome,
        services: arServices,
        programs: arPrograms,
        apply: arApply,
        legal: arLegal,
        meta: arMeta,
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
