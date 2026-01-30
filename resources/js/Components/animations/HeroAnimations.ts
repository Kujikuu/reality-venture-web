import { Variants } from 'framer-motion';

/**
 * Animation variants for the Hero component
 * Using spring physics for natural, smooth motion
 */

// Container variant for staggered children animations
export const heroContainerVariants: Variants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.15, // 150ms between each child
      delayChildren: 0.3,    // 300ms initial delay before first animation
    },
  },
};

// Item variant for individual elements (fade in + slide up)
export const heroItemVariants: Variants = {
  hidden: {
    opacity: 0,
    y: 30, // Start 30px below
  },
  visible: {
    opacity: 1,
    y: 0,
    transition: {
      type: 'spring', // Natural spring physics
      stiffness: 100, // Snappy but smooth
      damping: 15,    // Reduces bounce
      mass: 1,        // Standard weight
    },
  },
};

// Button hover animations with dynamic shadow
export const buttonHoverVariants: Variants = {
  hover: {
    scale: 1.05,
    boxShadow: '0 10px 40px -10px rgba(223, 104, 55, 0.4)',
    transition: {
      type: 'spring',
      stiffness: 400,
      damping: 10,
    },
  },
  tap: {
    scale: 0.95,
  },
};
