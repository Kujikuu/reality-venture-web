import { createContext, useContext, useEffect, useRef, type ReactNode } from 'react';
import { router } from '@inertiajs/react';
import Lenis from 'lenis';

interface LenisProviderProps {
  children: ReactNode;
}

const LenisContext = createContext<Lenis | null>(null);

export const useLenis = () => useContext(LenisContext);

export const LenisProvider: React.FC<LenisProviderProps> = ({ children }) => {
  const lenisRef = useRef<Lenis | null>(null);

  useEffect(() => {
    // Initialize Lenis with autoRaf for automatic animation frame loop
    const lenis = new Lenis({
      autoRaf: true,
    });

    lenisRef.current = lenis;

    // Scroll to top smoothly on real page navigation only.
    // 'navigate' fires on URL changes (page visits) but NOT on form POSTs that
    // redirect back to the same URL — so forms using preserveScroll stay put.
    const handleNavigate = () => {
      lenis.scrollTo(0);
    };

    const unsubscribe = router.on('navigate', handleNavigate);

    // Cleanup
    return () => {
      unsubscribe();
      lenis.destroy();
    };
  }, []);

  return (
    <LenisContext.Provider value={lenisRef.current}>
      {children}
    </LenisContext.Provider>
  );
};
