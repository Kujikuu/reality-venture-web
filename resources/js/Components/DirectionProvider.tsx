import { useEffect, type ReactNode } from 'react';
import { useTranslation } from 'react-i18next';

interface DirectionProviderProps {
  children: ReactNode;
}

// Mapping of language codes to text directions
const RTL_LANGUAGES = ['ar', 'he', 'fa', 'ur'];

export const DirectionProvider: React.FC<DirectionProviderProps> = ({ children }) => {
  const { i18n } = useTranslation();

  useEffect(() => {
    const updateDirection = () => {
      const currentLang = i18n.language;
      const isRtl = RTL_LANGUAGES.includes(currentLang);

      // Update document direction
      document.documentElement.dir = isRtl ? 'rtl' : 'ltr';

      // Update document language
      document.documentElement.lang = currentLang;
    };

    // Set initial direction
    updateDirection();

    // Listen for language changes
    i18n.on('languageChanged', updateDirection);

    // Cleanup listener
    return () => {
      i18n.off('languageChanged', updateDirection);
    };
  }, [i18n]);

  return <>{children}</>;
};
