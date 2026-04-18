import { useState, useEffect, useCallback, useRef } from 'react';
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
  const userMenuRef = useRef<HTMLDivElement>(null);

  const navLinks: NavLink[] = [
    { nameKey: 'about', link: 'hero' },
    { nameKey: 'ventureProgram', link: 'programs' },
    { nameKey: 'advisory', link: '/consultants', isPage: true },
    { nameKey: 'rvClub', link: 'rv-club' },
    { nameKey: 'blog', link: '/blog', isPage: true },
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

  const closeAllMenus = useCallback(() => {
    setIsMenuOpen(false);
    setIsUserMenuOpen(false);
  }, []);

  useEffect(() => {
    const handleEscape = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        closeAllMenus();
      }
    };

    document.addEventListener('keydown', handleEscape);
    return () => document.removeEventListener('keydown', handleEscape);
  }, [closeAllMenus]);

  useEffect(() => {
    if (isMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isMenuOpen]);

  const dashboardUrl = auth.user?.role === 'consultant' ? '/consultant/dashboard' : '/dashboard';

  const renderNavLink = (item: NavLink) => {
    const linkClasses = "text-sm font-medium text-gray-600 hover:text-secondary transition-colors relative group";
    const underline = <span className="absolute -bottom-1 start-0 w-0 h-0.5 bg-secondary transition-all duration-300 group-hover:w-full" />;

    if (item.isPage) {
      return (
        <Link key={item.nameKey} href={item.link} className={linkClasses}>
          {t(`header.${item.nameKey}`)}
          {underline}
        </Link>
      );
    }

    return (
      <a
        key={item.nameKey}
        href={`/#${item.link}`}
        onClick={(e) => handleNavClick(e, item.link)}
        className={linkClasses}
      >
        {t(`header.${item.nameKey}`)}
        {underline}
      </a>
    );
  };

  const renderMobileNavLink = (item: NavLink) => {
    const linkClasses = "text-lg font-bold uppercase tracking-wide text-gray-900 hover:text-primary transition-colors";

    if (item.isPage) {
      return (
        <Link
          key={item.nameKey}
          href={item.link}
          className={linkClasses}
          onClick={() => setIsMenuOpen(false)}
        >
          {t(`header.${item.nameKey}`)}
        </Link>
      );
    }

    return (
      <a
        key={item.nameKey}
        href={`/#${item.link}`}
        className={linkClasses}
        onClick={(e) => {
          handleNavClick(e, item.link);
          setIsMenuOpen(false);
        }}
      >
        {t(`header.${item.nameKey}`)}
      </a>
    );
  };

  return (
    <header className="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60">
      <div className="mx-auto max-w-7xl px-6 lg:px-12 h-20 flex items-center justify-between">

        {/* Logo */}
        <a
          href="/"
          onClick={(e) => handleNavClick(e, 'hero')}
          className="flex items-center gap-3 group cursor-pointer"
        >
          <img src="/assets/images/RVHorizonal.png" alt={t('common:company.logoAlt')} className="w-auto h-7 md:h-10" />
        </a>

        {/* Desktop Nav */}
        <nav className="hidden lg:flex items-center gap-8">
          {navLinks.map(renderNavLink)}
        </nav>

        {/* Actions: Language Switcher + Auth */}
        <div className="hidden lg:flex items-center gap-4">
          <LanguageSwitcher />
          {auth.user ? (
            <div className="relative" ref={userMenuRef}>
              <button
                onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
                aria-expanded={isUserMenuOpen}
                aria-haspopup="true"
                className="flex items-center gap-2 h-10 px-4 bg-gray-50 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <User className="w-4 h-4" />
                <span className="max-w-[120px] truncate">{auth.user.name}</span>
                <ChevronDown className={`w-3 h-3 transition-transform duration-200 ${isUserMenuOpen ? 'rotate-180' : ''}`} />
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
              <Link
                href="/startuphub"
                className="h-10 px-6 flex items-center justify-center bg-primary text-white text-sm font-bold tracking-tight hover:bg-primary-800 active:scale-95 transition-all rounded-lg"
              >
                {t('buttons.applyNow')}
              </Link>
              <Link
                href="/grit"
                className="h-10 px-6 flex items-center justify-center bg-secondary text-white text-sm font-bold tracking-tight hover:bg-secondary-800 active:scale-95 transition-all rounded-lg"
              >
                {t('buttons.grit')}<sup className="text-[0.6em]">TM</sup>
              </Link>
            </div>
          )}
        </div>

        {/* Mobile Menu Toggle */}
        <div className="lg:hidden flex items-center gap-3">
          <LanguageSwitcher />
          <button
            className="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center text-gray-900"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            aria-label={isMenuOpen ? 'Close menu' : 'Open menu'}
            aria-expanded={isMenuOpen}
          >
            {isMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      {/* Mobile Nav Overlay */}
      <div
        className={`lg:hidden fixed inset-0 top-20 z-40 transition-opacity duration-200 ${
          isMenuOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'
        }`}
      >
        {/* Backdrop */}
        <div
          className="absolute inset-0 bg-black/20"
          onClick={() => setIsMenuOpen(false)}
        />

        {/* Panel */}
        <div
          className={`relative bg-white border-b border-gray-200 p-6 flex flex-col gap-4 shadow-lg transition-transform duration-200 ${
            isMenuOpen ? 'translate-y-0' : '-translate-y-4'
          }`}
        >
          {navLinks.map(renderMobileNavLink)}

          <div className="border-t border-gray-200 pt-4 mt-2">
            {auth.user ? (
              <div className="flex flex-col gap-3">
                <Link
                  href={dashboardUrl}
                  className="text-lg font-bold uppercase tracking-wide text-gray-900 hover:text-primary transition-colors"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.dashboard')}
                </Link>
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  className="text-lg font-bold uppercase tracking-wide text-gray-900 hover:text-primary transition-colors text-start"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.logout')}
                </Link>
              </div>
            ) : (
              <div className="flex flex-col gap-3">
                <Link
                  href="/login"
                  className="h-12 w-full flex items-center justify-center bg-white border border-primary text-primary font-bold uppercase tracking-wide rounded-lg transition-colors hover:bg-primary-50"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.login')}
                </Link>
                <Link
                  href="/startuphub"
                  className="h-12 w-full flex items-center justify-center bg-primary text-white font-bold uppercase tracking-wide rounded-lg transition-colors hover:bg-primary-800"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.applyNow')}
                </Link>
                <Link
                  href="/grit"
                  className="h-12 w-full flex items-center justify-center bg-secondary text-white font-bold uppercase tracking-wide rounded-lg transition-colors hover:bg-secondary-800"
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t('buttons.grit')}<sup className="text-[0.6em]">TM</sup>
                </Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};
