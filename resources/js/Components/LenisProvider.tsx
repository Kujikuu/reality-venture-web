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

    // Scroll to top smoothly on Inertia navigation
    const handleNavigate = () => {
      lenis.scrollTo(0);
    };

    // Listen for Inertia 'finish' event (fires after page loads)
    // In Inertia v2, on() returns an unsubscribe function
    const unsubscribe = router.on('finish', handleNavigate);

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
