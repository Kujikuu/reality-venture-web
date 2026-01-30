import { Variants } from 'framer-motion';

/**
 * Common animation variants for home page components
 * Using gentle spring physics for subtle, professional motion
 */

// Section entrance: fade in + slide up
export const sectionVariants: Variants = {
  hidden: {
    opacity: 0,
    y: 20,
  },
  visible: {
    opacity: 1,
    y: 0,
    transition: {
      type: 'spring',
      stiffness: 80,
      damping: 15,
      mass: 1,
    },
  },
};

// Container variant for staggered children animations
export const staggerContainer: Variants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1, // 100ms between each child
      delayChildren: 0.2,    // 200ms initial delay
    },
  },
};

// Individual card/item variant (fade in + slide up)
export const cardVariants: Variants = {
  hidden: {
    opacity: 0,
    y: 20,
  },
  visible: {
    opacity: 1,
    y: 0,
    transition: {
      type: 'spring',
      stiffness: 80,
      damping: 15,
    },
  },
};

// Subtle hover effect for cards
export const cardHover: Variants = {
  hover: {
    scale: 1.02,
    transition: {
      type: 'spring',
      stiffness: 300,
      damping: 20,
    },
  },
};
