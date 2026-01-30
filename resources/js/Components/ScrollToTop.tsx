import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { useLenis } from './LenisProvider';

export const ScrollToTop = () => {
  const lenis = useLenis();
  const { url } = usePage();

  useEffect(() => {
    if (lenis) {
      lenis.scrollTo(0);
    }
  }, [url, lenis]);

  return null;
};
