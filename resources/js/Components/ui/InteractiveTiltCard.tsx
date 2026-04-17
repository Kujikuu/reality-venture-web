import React, { useRef, useEffect, useState, useMemo, useCallback } from 'react';

interface InteractiveTiltCardProps {
  children: React.ReactNode;
  className?: string;
  maxTilt?: number;
  perspective?: number;
  scaleOnHover?: number;
  parallaxDepth?: number;
}

interface TiltState {
  transform: string;
  shadow: string;
  shinX: number;
  shinY: number;
  contentX: number;
  contentY: number;
  isHovered: boolean;
}

const IDLE_STATE: TiltState = {
  transform: '',
  shadow: '0 20px 40px -12px rgba(0, 0, 0, 0.08)',
  shinX: 50,
  shinY: 50,
  contentX: 0,
  contentY: 0,
  isHovered: false,
};

const EASE_OUT_QUART = 'cubic-bezier(0.25, 1, 0.5, 1)';

export const InteractiveTiltCard: React.FC<InteractiveTiltCardProps> = ({
  children,
  className = '',
  maxTilt = 8,
  perspective = 1000,
  scaleOnHover = 1.02,
  parallaxDepth = 5,
}) => {
  const cardRef = useRef<HTMLDivElement>(null);
  const [tilt, setTilt] = useState<TiltState>(IDLE_STATE);
  const rafRef = useRef<number | null>(null);
  const lastUpdateRef = useRef<number>(0);
  const THROTTLE_DELAY = 16;

  const prefersReducedMotion = useMemo(() => {
    if (typeof window === 'undefined') return false;
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }, []);

  const handleMouseMove = useCallback((e: MouseEvent) => {
    const now = performance.now();
    if (now - lastUpdateRef.current < THROTTLE_DELAY) return;
    lastUpdateRef.current = now;

    if (rafRef.current) cancelAnimationFrame(rafRef.current);

    rafRef.current = requestAnimationFrame(() => {
      const card = cardRef.current;
      if (!card) return;

      const rect = card.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;

      const mouseX = (e.clientX - centerX) / (rect.width / 2);
      const mouseY = (e.clientY - centerY) / (rect.height / 2);

      const rotateY = mouseX * maxTilt;
      const rotateX = -mouseY * maxTilt;

      const shadowX = -mouseX * 16;
      const shadowY = -mouseY * 16 + 24;

      const shinX = ((e.clientX - rect.left) / rect.width) * 100;
      const shinY = ((e.clientY - rect.top) / rect.height) * 100;

      // Inner content shifts toward cursor, creating depth parallax
      const contentX = mouseX * parallaxDepth;
      const contentY = mouseY * parallaxDepth;

      setTilt({
        transform: `perspective(${perspective}px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(${scaleOnHover})`,
        shadow: `${shadowX}px ${shadowY}px 48px -8px rgba(0, 0, 0, 0.12)`,
        shinX,
        shinY,
        contentX,
        contentY,
        isHovered: true,
      });
    });
  }, [maxTilt, perspective, scaleOnHover, parallaxDepth]);

  const handleMouseLeave = useCallback(() => {
    if (rafRef.current) cancelAnimationFrame(rafRef.current);
    setTilt({
      ...IDLE_STATE,
      transform: `perspective(${perspective}px) rotateX(0deg) rotateY(0deg) scale(1)`,
    });
  }, [perspective]);

  useEffect(() => {
    const card = cardRef.current;
    if (!card || prefersReducedMotion) return;

    card.addEventListener('mousemove', handleMouseMove, { passive: true } as AddEventListenerOptions);
    card.addEventListener('mouseleave', handleMouseLeave);

    return () => {
      card.removeEventListener('mousemove', handleMouseMove);
      card.removeEventListener('mouseleave', handleMouseLeave);
      if (rafRef.current) cancelAnimationFrame(rafRef.current);
    };
  }, [prefersReducedMotion, handleMouseMove, handleMouseLeave]);

  const floatAnimation = !prefersReducedMotion && !tilt.isHovered
    ? 'tilt-card-float 6s ease-in-out infinite'
    : 'none';

  return (
    <div
      ref={cardRef}
      className={`will-change-transform rounded-3xl ${className}`}
      style={{
        transform: tilt.transform,
        boxShadow: tilt.shadow,
        animation: floatAnimation,
        transition: tilt.isHovered
          ? `transform 150ms ${EASE_OUT_QUART}, box-shadow 150ms ${EASE_OUT_QUART}`
          : `transform 400ms ${EASE_OUT_QUART}, box-shadow 400ms ${EASE_OUT_QUART}`,
      }}
    >
      <div className="relative w-full h-full">
        {/* Content layer with parallax offset */}
        <div
          className="w-full h-full"
          style={{
            transform: tilt.isHovered
              ? `translate3d(${tilt.contentX}px, ${tilt.contentY}px, 0)`
              : 'translate3d(0, 0, 0)',
            transition: tilt.isHovered
              ? `transform 200ms ${EASE_OUT_QUART}`
              : `transform 500ms ${EASE_OUT_QUART}`,
          }}
        >
          {children}
        </div>
        {/* Light reflection that follows cursor */}
        <div
          className="pointer-events-none absolute inset-0 rounded-3xl"
          style={{
            background: tilt.isHovered
              ? `radial-gradient(circle at ${tilt.shinX}% ${tilt.shinY}%, rgba(255, 255, 255, 0.15) 0%, transparent 60%)`
              : 'none',
            transition: `opacity 300ms ${EASE_OUT_QUART}`,
            opacity: tilt.isHovered ? 1 : 0,
          }}
          aria-hidden="true"
        />
      </div>
    </div>
  );
};
