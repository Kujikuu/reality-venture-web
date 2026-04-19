import React, { useState, useEffect, useCallback } from 'react';
import { usePage, router } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import axios from 'axios';
import { useLenis } from './LenisProvider';

interface BannerData {
  id: number;
  title: string;
  image_url: string;
  link_url: string | null;
  external: boolean;
  alt_text: string;
  position: string;
}

interface AdBannerProps {
  position: 'top' | 'middle' | 'bottom';
  className?: string;
  interval?: number;
}

interface PageProps {
  banners?: Record<string, BannerData[]>;
  [key: string]: unknown;
}

export const AdBanner: React.FC<AdBannerProps> = ({ position, className = '', interval = 5000 }) => {
  const { banners } = usePage<PageProps>().props;
  const lenis = useLenis();
  const items = banners?.[position];
  const [current, setCurrent] = useState(0);

  const advance = useCallback(() => {
    if (items && items.length > 1) {
      setCurrent((prev) => (prev + 1) % items.length);
    }
  }, [items]);

  useEffect(() => {
    if (!items || items.length <= 1) return;
    const timer = setInterval(advance, interval);
    return () => clearInterval(timer);
  }, [items, interval, advance]);

  if (!items || items.length === 0) {
    return null;
  }

  const banner = items[current];

  const handleClick = (e: React.MouseEvent<HTMLAnchorElement>, id: number) => {
    axios.post(`/banners/${id}/click`).catch(() => {});
  };

  const handleLinkClick = (e: React.MouseEvent<HTMLAnchorElement>, url: string, external: boolean) => {
    handleClick(e, banner.id);

    if (!url) return;

    const isAnchor = url.startsWith('#');
    const isInternalPath = url.startsWith('/');

    if (isAnchor) {
      e.preventDefault();
      const targetId = url.slice(1);
      const element = document.getElementById(targetId);
      if (element) {
        if (lenis) {
          lenis.scrollTo(element, { offset: -80 });
        } else {
          element.scrollIntoView({ behavior: 'smooth' });
        }
      }
    } else if (isInternalPath && !external) {
      e.preventDefault();
      router.visit(url, { preserveScroll: true });
    }
  };

  const renderBannerLink = () => {
    if (!banner.link_url) return null;

    const url = banner.link_url;
    const isAnchor = url.startsWith('#');
    const isInternalPath = url.startsWith('/');

    if (isAnchor) {
      return (
        <motion.a
          key={banner.id}
          href={url}
          onClick={(e) => handleLinkClick(e, url, banner.external)}
          className="absolute inset-0"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.6, ease: 'easeInOut' }}
        >
          <div className="w-full h-full bg-gray-100">
            <img src={banner.image_url} alt={banner.alt_text} className="w-full h-full object-cover" />
            <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300" />
          </div>
        </motion.a>
      );
    }

    if (isInternalPath && !banner.external) {
      return (
        <motion.a
          key={banner.id}
          href={url}
          onClick={(e) => handleLinkClick(e, url, banner.external)}
          className="absolute inset-0"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.6, ease: 'easeInOut' }}
        >
          <div className="w-full h-full bg-gray-100">
            <img src={banner.image_url} alt={banner.alt_text} className="w-full h-full object-cover" />
            <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300" />
          </div>
        </motion.a>
      );
    }

    return (
      <motion.a
        key={banner.id}
        href={url}
        target="_blank"
        rel="noopener noreferrer"
        onClick={(e) => handleClick(e, banner.id)}
        className="absolute inset-0"
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        transition={{ duration: 0.6, ease: 'easeInOut' }}
      >
        <div className="w-full h-full bg-gray-100">
          <img src={banner.image_url} alt={banner.alt_text} className="w-full h-full object-cover" />
          <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300" />
        </div>
      </motion.a>
    );
  };

  return (
    <div className={`w-full py-8 ${className}`}>
      <div className="max-w-7xl mx-auto p-6 lg:p-8">
        <div className="relative w-full overflow-hidden rounded-xl shadow-xs hover:shadow-md transition-shadow duration-300 group">
          <div className="relative aspect-[4/2] md:aspect-[6/2] w-full">
            <AnimatePresence mode="wait">
              {banner.link_url ? (
                renderBannerLink()
              ) : (
                <motion.div
                  key={banner.id}
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  exit={{ opacity: 0 }}
                  transition={{ duration: 0.6, ease: 'easeInOut' }}
                  className="absolute inset-0 bg-gray-100"
                >
                  <img
                    src={banner.image_url}
                    alt={banner.alt_text}
                    className="w-full h-full object-cover"
                  />
                  <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300" />
                </motion.div>
              )}
            </AnimatePresence>
            {/* <div className="absolute top-2 right-2 bg-white/90 px-2 py-0.5 rounded text-[10px] font-medium text-gray-500 uppercase tracking-wider shadow-xs z-10">
              Ad
            </div> */}
          </div>
          {items.length > 1 && (
            <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
              {items.map((_, i) => (
                <span
                  key={i}
                  className={`block h-1.5 rounded-full transition-all duration-300 ${
                    i === current ? 'bg-white w-4' : 'bg-white/50 w-1.5'
                  }`}
                />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
