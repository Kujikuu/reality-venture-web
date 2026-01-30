import { Link } from '@inertiajs/react';
import { Home, ArrowRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Button } from '../Components/ui/Button';

export default function NotFound() {
  const { t } = useTranslation(['common', 'navigation']);

  return (
    <div className="flex flex-col min-h-screen bg-white">
      <section className="flex-1 flex items-center justify-center px-6 lg:px-12 py-20 lg:py-28 bg-gray-50">
        <div className="max-w-3xl mx-auto text-center">
          {/* 404 Number */}
          <div className="mb-8">
            <h1 className="text-[180px] md:text-[220px] font-black text-gray-100 leading-none tracking-tighter">
              404
            </h1>
          </div>

          {/* Message */}
          <div className="mb-12">
            <span className="inline-flex items-center gap-2 mb-6 text-primary font-bold tracking-[0.35em] uppercase text-xs">
              Page Not Found
              <span className="w-8 h-[2px] bg-primary"></span>
            </span>
            <h2 className="text-3xl md:text-5xl font-black uppercase tracking-tighter leading-tight mb-6 text-gray-900">
              We couldn't find
              <span className="block text-primary">what you're looking for</span>
            </h2>
            <p className="text-gray-500 text-lg max-w-xl mx-auto leading-relaxed">
              The page you're trying to reach doesn't exist or has been moved. Let's get you back on track.
            </p>
          </div>

          {/* CTA Buttons */}
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
            <Link href="/">
              <Button withArrow>
                <Home className="w-4 h-4" />
                Go {t('footer.home')}
              </Button>
            </Link>
            <button
              onClick={() => window.history.back()}
              className="h-12 px-8 border border-gray-300 text-gray-700 font-bold uppercase tracking-wider text-sm rounded-md hover:bg-gray-50 hover:border-gray-400 transition-all inline-flex items-center gap-2"
            >
              Go Back
            </button>
          </div>

          {/* Help Text */}
          <div className="mt-16 pt-8 border-t border-gray-200">
            <p className="text-sm text-gray-400">
              If you believe this is an error, please contact us at{' '}
              <a href="mailto:be@rv.com.sa" className="text-primary hover:underline font-medium">
                be@rv.com.sa
              </a>
            </p>
          </div>
        </div>
      </section>
    </div>
  );
}
