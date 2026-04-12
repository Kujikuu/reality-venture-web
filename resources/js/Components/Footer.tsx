import React from 'react';
import { Mail, MapPin, Linkedin, Twitter } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { NewsletterSubscribe } from './NewsletterSubscribe';

interface FooterProps {
  hideNewsletter?: boolean;
}

export const Footer = ({ hideNewsletter = false }: FooterProps) => {
  const { t } = useTranslation(['navigation', 'common']);

  const smoothScrollTo = (e: React.MouseEvent<Element>, targetId: string) => {
    // Only handle smooth scroll if we're on the home page
    if (window.location.pathname === '/') {
      e.preventDefault();
      const element = document.getElementById(targetId);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    }
  };

  return (
    <footer className="bg-white border-t border-gray-100 pt-16 pb-12 relative overflow-hidden">

      {!hideNewsletter && (
        <div className="mb-24">
          <NewsletterSubscribe />
        </div>
      )}

      <div className="max-w-7xl mx-auto px-6 lg:px-8">

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 mb-20 border-b border-gray-100 pb-12">

          {/* Brand & Address - Span 4 */}
          <div className="lg:col-span-4 space-y-8">
            <div className="flex items-center gap-3">
              <img src="/assets/images/RVHorizonal.png" alt="Company Logo" className="w-auto h-10" />
            </div>
            <p className="text-gray-500 leading-relaxed max-w-sm">
              {t('common:company.tagline')}
            </p>

            {/* Socials Row */}
            <div className="flex gap-3 pt-2">
              <a href="https://www.linkedin.com/company/rvgrowth" target="_blank" className="w-10 h-10 rounded-md bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all duration-300">
                <Linkedin className="w-4 h-4" />
              </a>
              <a href="https://x.com/rvgrowth" target="_blank" className="w-10 h-10 rounded-md bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all duration-300">
                <Twitter className="w-4 h-4" />
              </a>
              <a href="mailto:hello@rv.com.sa" className="w-10 h-10 rounded-md bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all duration-300">
                <Mail className="w-4 h-4" />
              </a>
            </div>
          </div>

          {/* Spacer - Span 1 */}
          <div className="hidden lg:block lg:col-span-2" />

          {/* Navigation - Explore - Span 2 */}
          <div className="lg:col-span-3">
            <h4 className="text-sm font-bold uppercase tracking-wider text-gray-900 mb-6">{t('navigation:footer.quickLinks')}</h4>
            <ul className="grid grid-cols-2 gap-x-6 gap-y-3 text-sm font-medium text-gray-500">
              <li><Link href="/#hero" onClick={(e) => smoothScrollTo(e, 'hero')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.about')}</Link></li>
              <li><Link href="/consultants" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.advisory')}</Link></li>
              <li><Link href="/#programs" onClick={(e) => smoothScrollTo(e, 'programs')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.ventureProgram')}</Link></li>
              <li><Link href="/grit" className="hover:text-primary transition-colors block py-1">GRIT<sup className="text-[0.6em]">TM</sup></Link></li>
              <li><Link href="/#rv-club" onClick={(e) => smoothScrollTo(e, 'rv-club')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.rvClub')}</Link></li>
              <li><Link href="/blog" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.blog')}</Link></li>
            </ul>
          </div>

          {/* Location - Span 2 */}
          <div className="lg:col-span-3">
            <h4 className="text-sm font-bold uppercase tracking-wider text-gray-900 mb-6">{t('navigation:footer.location')}</h4>
            <div className="flex items-start gap-3 text-gray-500 text-sm">
              <MapPin className="w-5 h-5 shrink-0 text-primary mt-0.5" />
              <span className="leading-relaxed">
                {t('navigation:footer.riyadh')}<br />
                {t('navigation:footer.globalOperations')}
              </span>
            </div>
          </div>

        </div>

        {/* Bottom Bar */}
        <div className="flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-gray-400 font-medium">
          <p>{t('navigation:footer.copyright')}</p>
          <div className="flex gap-8">
            <Link href="/privacy-policy" className="hover:text-gray-900 transition-colors">{t('navigation:footer.privacyPolicy')}</Link>
            <Link href="/terms-of-service" className="hover:text-gray-900 transition-colors">{t('navigation:footer.termsOfService')}</Link>
          </div>
        </div>

      </div>
    </footer>
  );
};
