import React, { useState } from 'react';
import { Menu, X } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LanguageSwitcher } from './LanguageSwitcher';

export const Header = () => {
  const { t } = useTranslation('navigation');
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const navLinks = [
    { nameKey: 'about', link: 'hero' },
    { nameKey: 'services', link: 'services' },
    { nameKey: 'realityVenture', link: 'proptech' },
    { nameKey: 'programs', link: 'programs' },
  ];

  const smoothScrollTo = (e: React.MouseEvent<HTMLAnchorElement>, targetId: string) => {
    e.preventDefault();
    const element = document.getElementById(targetId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <header className="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-background-dark dark:border-gray-800">
      <div className="mx-auto max-w-[1440px] px-6 lg:px-12 h-20 flex items-center justify-between">

        {/* Logo */}
        <Link
          href="/"
          className="flex items-center gap-3 group cursor-pointer"
        >
          <img src="/assets/images/RVHorizonal.png" alt="Company Logo" className="w-auto h-7 md:h-10" />
        </Link>

        {/* Desktop Nav */}
        <nav className="hidden lg:flex items-center gap-8">
          {navLinks.map((item) => (
            <a
              key={item.nameKey}
              href={`/#${item.link}`}
              onClick={(e) => smoothScrollTo(e, item.link)}
              className="text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group"
            >
              {t(`header.${item.nameKey}`)}
              <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />
            </a>
          ))}
        </nav>

        {/* Actions: Language Switcher + CTA */}
        <div className="hidden lg:flex items-center gap-4">
          <LanguageSwitcher />
          <Link href="/application-form">
            <button className="h-10 px-6 items-center justify-center bg-primary text-white text-sm font-bold tracking-tight hover:bg-background-dark transition-all rounded-md uppercase sm:normal-case">
              {t('buttons.getStarted')}
            </button>
          </Link>
        </div>

        {/* Mobile Menu Toggle */}
        <div className="lg:hidden flex items-center gap-3">
          <LanguageSwitcher />
          <button
            className="p-2 text-black"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
          >
            {isMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      {/* Mobile Nav Dropdown */}
      {isMenuOpen && (
        <div className="lg:hidden absolute top-20 left-0 w-full bg-white border-b border-gray-200 p-6 flex flex-col gap-4">
          {navLinks.map((item) => (
            <a
              key={item.nameKey}
              href={`/#${item.link}`}
              className="text-lg font-bold uppercase tracking-wide hover:text-primary"
              onClick={(e) => {
                smoothScrollTo(e, item.link);
                setIsMenuOpen(false);
              }}
            >
              {t(`header.${item.nameKey}`)}
            </a>
          ))}
          <Link href="/application-form" onClick={() => setIsMenuOpen(false)}>
            <button className="h-12 w-full bg-primary text-white font-bold uppercase tracking-wide rounded-sm mt-4">
              {t('buttons.applyNow')}
            </button>
          </Link>
        </div>
      )}
    </header>
  );
};
