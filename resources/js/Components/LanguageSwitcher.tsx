import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Globe } from 'lucide-react';

interface Language {
  code: string;
  name: string;
  nativeName: string;
}

const LANGUAGES: Language[] = [
  { code: 'en', name: 'English', nativeName: 'English' },
  { code: 'ar', name: 'Arabic', nativeName: 'العربية' }
];

export const LanguageSwitcher: React.FC = () => {
  const { i18n } = useTranslation();
  const [isOpen, setIsOpen] = useState(false);
  const currentLang = LANGUAGES.find(lang => lang.code === i18n.language) || LANGUAGES[0];

  const changeLanguage = (langCode: string) => {
    i18n.changeLanguage(langCode);
    setIsOpen(false);
    // Store preference
    localStorage.setItem('i18nextLng', langCode);
  };

  return (
    <div className="relative z-50">
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center gap-2 px-3 py-2 rounded-md border border-gray-200 hover:border-primary transition-all"
        aria-label="Switch language"
      >
        <Globe className="w-4 h-4 text-primary" />
        <span className="text-sm font-medium text-gray-700">
          {currentLang.nativeName}
        </span>
      </button>

      {isOpen && (
        <>
          {/* Backdrop */}
          <div
            className="fixed inset-0 z-40"
            onClick={() => setIsOpen(false)}
          />

          {/* Dropdown */}
          <div className="absolute top-full mt-2 right-0 bg-white border border-gray-200 rounded-lg shadow-lg py-2 min-w-[140px] z-50">
            {LANGUAGES.map((lang) => (
              <button
                key={lang.code}
                onClick={() => changeLanguage(lang.code)}
                className={`w-full px-4 py-2 text-start text-sm hover:bg-surface transition-colors ${
                  lang.code === i18n.language
                    ? 'bg-surface text-primary font-semibold'
                    : 'text-gray-700'
                }`}
              >
                {lang.nativeName}
              </button>
            ))}
          </div>
        </>
      )}
    </div>
  );
};
