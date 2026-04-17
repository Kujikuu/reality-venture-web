import { Link, usePage } from '@inertiajs/react';
import { LogOut, Home, Globe } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import type { PageProps } from '../types/marketplace';

interface SidebarLink {
  href: string;
  icon: React.ElementType;
  label: string;
}

interface Props {
  children: React.ReactNode;
  links: SidebarLink[];
  title: string;
}

export default function DashboardLayout({ children, links, title }: Props) {
  const { auth } = usePage<PageProps>().props;
  const currentUrl = usePage().url;
  const { t, i18n } = useTranslation('navigation');

  const isActive = (href: string) => {
    const path = href.split('#')[0];
    if (currentUrl === path) return true;
    if (!currentUrl.startsWith(path + '/')) return false;
    // Only match prefix if no other link is a more specific match
    return !links.some((l) => {
      const lPath = l.href.split('#')[0];
      return lPath !== path && lPath.startsWith(path + '/') && currentUrl.startsWith(lPath);
    });
  };

  const toggleLanguage = () => {
    const next = i18n.language === 'ar' ? 'en' : 'ar';
    i18n.changeLanguage(next);
    localStorage.setItem('i18nextLng', next);
  };

  return (
    <div className="min-h-screen flex flex-col lg:flex-row bg-gray-50">
      {/* Desktop Sidebar */}
      <aside className="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 bg-primary text-white">
        <div className="flex flex-col h-full">
          {/* Logo */}
          <div className="px-6 py-6 border-b border-white/10">
            <Link href="/" className="flex items-center gap-2">
              <img src="/assets/images/RVHorizonal.png" alt="Reality Venture" className="h-7 brightness-0 invert" />
            </Link>
          </div>

          {/* Nav Links */}
          <nav className="flex-1 px-3 py-6 space-y-1">
            {links.map((link) => {
              const Icon = link.icon;
              const active = isActive(link.href);
              return (
                <Link
                  key={link.href}
                  href={link.href}
                  className={`flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors ${
                    active
                      ? 'bg-white/10 text-white'
                      : 'text-white/70 hover:bg-white/5 hover:text-white'
                  }`}
                >
                  <Icon className="w-5 h-5 shrink-0" />
                  {link.label}
                </Link>
              );
            })}
          </nav>

          {/* User Info + Logout */}
          <div className="px-3 py-4 border-t border-white/10">
            <div className="flex items-center gap-3 px-4 py-2">
              {auth.user?.avatar_url ? (
                <img src={auth.user.avatar_url} alt="" className="w-9 h-9 rounded-full object-cover ring-2 ring-white/20 shrink-0" />
              ) : (
                <div className="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center ring-2 ring-white/20 shrink-0">
                  <span className="text-xs font-bold text-white/70">
                    {auth.user?.name?.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()}
                  </span>
                </div>
              )}
              <div className="min-w-0">
                <div className="text-sm font-semibold truncate">{auth.user?.name}</div>
                <div className="text-xs text-white/50 truncate">{auth.user?.email}</div>
              </div>
            </div>
            <Link
              href="/logout"
              method="post"
              as="button"
              className="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-colors mt-1"
            >
              <LogOut className="w-4 h-4" /> {t('buttons.logout')}
            </Link>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <div className="flex-1 lg:ms-64">
        {/* Top Bar */}
        <header className="bg-white border-b border-gray-200 px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <h1 className="text-lg font-bold text-gray-900">{title}</h1>
            <div className="flex items-center gap-3">
              <button
                onClick={toggleLanguage}
                className="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-500 hover:text-primary border border-gray-200 rounded-lg hover:border-primary/30 transition-colors"
              >
                <Globe className="w-3.5 h-3.5" />
                {i18n.language === 'ar' ? 'EN' : 'AR'}
              </button>
              <Link href="/" className="flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary transition-colors">
                <Home className="w-4 h-4" /> {t('buttons.home')}
              </Link>
            </div>
          </div>
        </header>

        <div className="p-6 lg:p-8">{children}</div>
      </div>

      {/* Mobile Bottom Tab Bar */}
      <nav className="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-50">
        <div className="flex items-center justify-around py-2">
          {links.slice(0, 4).map((link) => {
            const Icon = link.icon;
            const active = isActive(link.href);
            return (
              <Link
                key={link.href}
                href={link.href}
                className={`flex flex-col items-center gap-0.5 py-1 px-3 ${
                  active ? 'text-secondary' : 'text-gray-400'
                }`}
              >
                <Icon className="w-5 h-5" />
                <span className="text-[10px] font-medium">{link.label}</span>
              </Link>
            );
          })}
        </div>
      </nav>
    </div>
  );
}
