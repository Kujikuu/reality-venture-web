import React from 'react';
import { ArrowUpRight, Mail, MapPin, Linkedin, Twitter, Send } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

export const Footer = () => {
  const { t } = useTranslation(['navigation', 'common']);

  const smoothScrollTo = (e: React.MouseEvent<HTMLAnchorElement>, targetId: string) => {
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

      {/* Newsletter CTA */}
      <div className="max-w-7xl mx-auto px-6 lg:px-8 mb-24">
        <div className="relative bg-gray-50 rounded-lg p-8 md:p-20 text-center overflow-hidden border border-gray-100">

          <div className="relative z-10 max-w-2xl mx-auto">
            <div className="inline-flex items-center gap-2 text-primary font-bold text-xs tracking-widest uppercase mb-6 bg-white/50 backdrop-blur-sm px-4 py-2 rounded-md border border-gray-100/50">
              <Send className="w-3 h-3" /> {t('common:status.noSpam')}
            </div>

            <h2 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6 tracking-tight leading-tight">
              {t('navigation:footer.newsletter.heading')}
            </h2>

            <p className="text-gray-500 text-lg mb-10 max-w-lg mx-auto leading-relaxed">
              {t('navigation:footer.newsletter.description')}
            </p>

            <div className="flex justify-center">
              <Link href="/application-form" className="px-10 py-4 bg-primary hover:bg-background-dark text-white font-bold rounded-md transition-all inline-flex items-center gap-2">
                {t('navigation:footer.newsletter.cta')} <ArrowUpRight className="w-5 h-5" />
              </Link>
            </div>
          </div>
        </div>
      </div>

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
              <a href="#" className="w-10 h-10 rounded-md bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all duration-300">
                <Linkedin className="w-4 h-4" />
              </a>
              <a href="#" className="w-10 h-10 rounded-md bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all duration-300">
                <Twitter className="w-4 h-4" />
              </a>
              <a href="mailto:be@rv.com.sa" className="w-10 h-10 rounded-md bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all duration-300">
                <Mail className="w-4 h-4" />
              </a>
            </div>
          </div>

          {/* Spacer - Span 1 */}
          <div className="hidden lg:block lg:col-span-2" />

          {/* Navigation - Explore - Span 2 */}
          <div className="lg:col-span-3">
            <h4 className="text-sm font-bold uppercase tracking-wider text-gray-900 mb-6">{t('navigation:footer.explore')}</h4>
            <ul className="space-y-4 text-sm font-medium text-gray-500">
              <li><Link href="/" className="hover:text-primary transition-colors block py-1">{t('navigation:footer.home')}</Link></li>
              <li><Link href="/#programs" onClick={(e) => smoothScrollTo(e, 'programs')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.programs')}</Link></li>
              <li><Link href="/#process" onClick={(e) => smoothScrollTo(e, 'process')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.process')}</Link></li>
              <li><Link href="/#team" onClick={(e) => smoothScrollTo(e, 'team')} className="hover:text-primary transition-colors block py-1">{t('navigation:footer.team')}</Link></li>
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
