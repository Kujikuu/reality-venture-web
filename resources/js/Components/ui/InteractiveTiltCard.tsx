import React, { useRef, useEffect, useState } from 'react';

interface InteractiveTiltCardProps {
  children: React.ReactNode;
  className?: string;
  maxTilt?: number; // Default: 10 degrees
  perspective?: number; // Default: 1000px
  scaleOnHover?: number; // Default: 1.02
}

/**
 * InteractiveTiltCard - A reusable 3D tilt effect wrapper
 *
 * Features:
 * - Mouse tracking with 60fps throttling
 * - Perspective transforms for realistic 3D depth
 * - Smooth reset animation on mouse leave
 * - Proper cleanup to prevent memory leaks
 *
 * @example
 * <InteractiveTiltCard className="w-96">
 *   <img src="logo.png" alt="Logo" />
 * </InteractiveTiltCard>
 */
export const InteractiveTiltCard: React.FC<InteractiveTiltCardProps> = ({
  children,
  className = '',
  maxTilt = 10,
  perspective = 1000,
  scaleOnHover = 1.02,
}) => {
  const cardRef = useRef<HTMLDivElement>(null);
  const [transform, setTransform] = useState('');
  const rafRef = useRef<number | null>(null);
  const lastUpdateRef = useRef<number>(0);
  const THROTTLE_DELAY = 16; // ~60fps (1000ms / 60fps = 16.67ms)

  useEffect(() => {
    const card = cardRef.current;
    if (!card) return;

    const handleMouseMove = (e: MouseEvent) => {
      const now = performance.now();

      // Throttle: Only update if 16ms has elapsed (maintains 60fps)
      if (now - lastUpdateRef.current < THROTTLE_DELAY) return;
      lastUpdateRef.current = now;

      // Cancel previous RAF to prevent buildup
      if (rafRef.current) cancelAnimationFrame(rafRef.current);

      rafRef.current = requestAnimationFrame(() => {
        const rect = card.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        // Calculate mouse position relative to center (-1 to 1)
        const mouseX = (e.clientX - centerX) / (rect.width / 2);
        const mouseY = (e.clientY - centerY) / (rect.height / 2);

        // Calculate tilt angles (inverted Y for natural feel)
        const rotateY = mouseX * maxTilt; // -10 to 10
        const rotateX = -mouseY * maxTilt; // -10 to 10

        setTransform(
          `perspective(${perspective}px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(${scaleOnHover})`
        );
      });
    };

    const handleMouseLeave = () => {
      if (rafRef.current) cancelAnimationFrame(rafRef.current);
      // Smooth reset to default position
      setTransform(`perspective(${perspective}px) rotateX(0deg) rotateY(0deg) scale(1)`);
    };

    // Add event listeners with passive for better performance
    card.addEventListener('mousemove', handleMouseMove, { passive: true } as AddEventListenerOptions);
    card.addEventListener('mouseleave', handleMouseLeave);

    // Cleanup function
    return () => {
      card.removeEventListener('mousemove', handleMouseMove);
      card.removeEventListener('mouseleave', handleMouseLeave);
      if (rafRef.current) cancelAnimationFrame(rafRef.current);
    };
  }, [maxTilt, perspective, scaleOnHover]);

  return (
    <div
      ref={cardRef}
      className={`transition-transform duration-200 ease-out will-change-transform ${className}`}
      style={{ transform }}
    >
      {children}
    </div>
  );
};
