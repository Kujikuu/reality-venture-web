import React, { useState } from 'react';
import { Menu, X, User, LogOut, LayoutDashboard, ChevronDown } from 'lucide-react';
import { Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LanguageSwitcher } from './LanguageSwitcher';
import type { PageProps } from '../types/marketplace';

interface NavLink {
  nameKey: string;
  link: string;
  isPage?: boolean;
}

export const Header = () => {
  const { auth = { user: null } } = usePage<PageProps>().props;
  const { t } = useTranslation('navigation');
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
  const [isMarketplaceOpen, setIsMarketplaceOpen] = useState(false);

  const navLinks: NavLink[] = [
    // { nameKey: 'about', link: 'hero' },
    { nameKey: 'ventureProgram', link: 'programs' },
    { nameKey: 'advisory', link: '/consultants' },
    { nameKey: 'rvClub', link: 'rv-club' },
    { nameKey: 'blog', link: '/blog', isPage: true },
  ];

  const marketplaceLinks = [
    { nameKey: 'advisory', link: '/consultants' },
    { nameKey: 'desks', link: '/desks' },
  ];

  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, targetId: string) => {
    if (window.location.pathname === '/') {
      e.preventDefault();
      const element = document.getElementById(targetId);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
      return;
    }

    e.preventDefault();
    window.location.href = `/#${targetId}`;
  };

  const dashboardUrl = auth.user?.role === 'consultant' ? '/consultant/dashboard' : '/dashboard';

  return (
    <header className="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-background-dark dark:border-gray-800">
      <div className="mx-auto max-w-7xl px-6 lg:px-12 h-20 flex items-center justify-between">

        {/* Logo */}
        <a
          href="/"
          onClick={(e) => handleNavClick(e, 'hero')}
          className="flex items-center gap-3 group cursor-pointer"
        >
          <img src="/assets/images/RVHorizonal.png" alt="Company Logo" className="w-auto h-7 md:h-10" />
        </a>

        {/* Desktop Nav */}
        <nav className="hidden lg:flex items-center gap-8">
          {navLinks.slice(0, 2).map((item) =>
            item.isPage ? (
              <Link
                key={item.nameKey}
                href={item.link}
                className="text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group"
              >
                {t(`header.${item.nameKey}`)}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />
              </Link>
            ) : (
              <a
                key={item.nameKey}
                href={`/#${item.link}`}
                onClick={(e) => handleNavClick(e, item.link)}
                className="text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group"
              >
                {t(`header.${item.nameKey}`)}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />
              </a>
            )
          )}

          {/* Marketplace Dropdown */}
          {/* <div className="relative">
            <button
              onClick={() => setIsMarketplaceOpen(!isMarketplaceOpen)}
              className="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group"
            >
              {t('header.marketplace')}
              <ChevronDown className={`w-3.5 h-3.5 transition-transform duration-200 ${isMarketplaceOpen ? 'rotate-180' : ''}`} />
              <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />
            </button>
            {isMarketplaceOpen && (
              <>
                <div className="fixed inset-0 z-40" onClick={() => setIsMarketplaceOpen(false)} />
                <div className="absolute start-0 mt-3 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1">
                  {marketplaceLinks.map((item) => (
                    <Link
                      key={item.nameKey}
                      href={item.link}
                      className="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors"
                      onClick={() => setIsMarketplaceOpen(false)}
                    >
                      {t(`header.${item.nameKey}`)}
                    </Link>
                  ))}
                </div>
              </>
            )}
          </div> */}

          {navLinks.slice(2).map((item) =>
            item.isPage ? (
              <Link
                key={item.nameKey}
                href={item.link}
                className="text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group"
              >
                {t(`header.${item.nameKey}`)}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />
              </Link>
            ) : (
              <a
                key={item.nameKey}
                href={`/#${item.link}`}
                onClick={(e) => handleNavClick(e, item.link)}
                className="text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group"
              >
                {t(`header.${item.nameKey}`)}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />
              </a>
            )
          )}
        </nav>

        {/* Actions: Language Switcher + Auth */}
        <div className="hidden lg:flex items-center gap-4">
          <LanguageSwitcher />
          {auth.user ? (
            <div className="relative">
              <button
                onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
                className="flex items-center gap-2 h-10 px-4 bg-gray-50 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <User className="w-4 h-4" />
                <span className="max-w-[120px] truncate">{auth.user.name}</span>
                <ChevronDown className={`w-3 h-3 transition-transform ${isUserMenuOpen ? 'rotate-180' : ''}`} />
              </button>
              {isUserMenuOpen && (
                <>
                  <div className="fixed inset-0 z-40" onClick={() => setIsUserMenuOpen(false)} />
                  <div className="absolute end-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1">
                    <Link
                      href={dashboardUrl}
                      className="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                      onClick={() => setIsUserMenuOpen(false)}
                    >
                      <LayoutDashboard className="w-4 h-4" />
                      {t('buttons.dashboard')}
                    </Link>
                    <Link
                      href="/logout"
                      method="post"
                      as="button"
                      className="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                      onClick={() => setIsUserMenuOpen(false)}
                    >
                      <LogOut className="w-4 h-4" />
                      {t('buttons.logout')}
                    </Link>
                  </div>
                </>
              )}
            </div>
          ) : (
            <div className="flex items-center gap-3">
              <Link
                href="/login"
                className="h-10 px-5 flex items-center justify-center text-sm font-medium text-gray-700 hover:text-primary transition-colors"
              >
                {t('buttons.login', 'Login')}
              </Link>
              <Link href="/application-form">
                <button className="h-10 px-6 items-center justify-center bg-primary text-white text-sm font-bold tracking-tight hover:bg-primary-800 transition-all rounded-md">
                  {t('buttons.applyNow')}
                </button>
              </Link>
            </div>
          )}
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
        <div className="lg:hidden absolute top-20 left-0 w-full bg-white border-b border-gray-200 p-6 flex flex-col gap-4 z-50">
          {navLinks.map((item) =>
            item.isPage ? (
              <Link
                key={item.nameKey}
                href={item.link}
                className="text-lg font-bold uppercase tracking-wide hover:text-primary"
                onClick={() => setIsMenuOpen(false)}
              >
                {t(`header.${item.nameKey}`)}
              </Link>
            ) : (
              <a
                key={item.nameKey}
                href={`/#${item.link}`}
                className="text-lg font-bold uppercase tracking-wide hover:text-primary"
                onClick={(e) => {
                  handleNavClick(e, item.link);
                  setIsMenuOpen(false);
                }}
              >
                {t(`header.${item.nameKey}`)}
              </a>
            )
          )}

          {/* Marketplace section */}
          {/* <div className="border-t border-gray-200 pt-4 mt-2">
            <span className="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3 block">{t('header.marketplace')}</span>
            <div className="flex flex-col gap-3">
              {marketplaceLinks.map((item) => (
                <Link
                  key={item.nameKey}
                  href={item.link}
                  className="text-lg font-bold uppercase tracking-wide hover:text-primary"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t(`header.${item.nameKey}`)}
                </Link>
              ))}
            </div>
          </div> */}

          <div className="border-t border-gray-200 pt-4 mt-2">
            {auth.user ? (
              <div className="flex flex-col gap-3">
                <Link
                  href={dashboardUrl}
                  className="text-lg font-bold uppercase tracking-wide hover:text-primary"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.dashboard')}
                </Link>
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  className="text-lg font-bold uppercase tracking-wide hover:text-primary text-start"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.logout')}
                </Link>
              </div>
            ) : (
              <div className="flex flex-col gap-3">
                <Link href="/login" onClick={() => setIsMenuOpen(false)}>
                  <button className="h-12 w-full bg-white border border-primary text-primary font-bold uppercase tracking-wide rounded-sm">
                    {t('buttons.login')}
                  </button>
                </Link>
                <Link href="/application-form" onClick={() => setIsMenuOpen(false)}>
                  <button className="h-12 w-full bg-primary text-white font-bold uppercase tracking-wide rounded-sm">
                    {t('buttons.applyNow')}
                  </button>
                </Link>
              </div>
            )}
          </div>
        </div>
      )}
    </header>
  );
};
