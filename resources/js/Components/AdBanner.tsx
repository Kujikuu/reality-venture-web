import React, { useState, useEffect, useCallback } from 'react';
import { usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import axios from 'axios';

interface BannerData {
  id: number;
  title: string;
  image_url: string;
  link_url: string | null;
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

  const handleClick = (id: number) => {
    axios.post(`/banners/${id}/click`).catch(() => {});
  };

  const image = (
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
  );

  return (
    <div className={`w-full py-8 ${className}`}>
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <div className="relative w-full overflow-hidden rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 group">
          <div className="relative aspect-[4/2] md:aspect-[6/2] w-full">
            <AnimatePresence mode="wait">
              {banner.link_url ? (
                <motion.a
                  key={banner.id}
                  href={banner.link_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={() => handleClick(banner.id)}
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
              ) : (
                image
              )}
            </AnimatePresence>
            <div className="absolute top-2 right-2 bg-white/90 px-2 py-0.5 rounded text-[10px] font-medium text-gray-500 uppercase tracking-wider shadow-sm z-10">
              Ad
            </div>
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
