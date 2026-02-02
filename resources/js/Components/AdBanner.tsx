import React from 'react';
import { useTranslation } from 'react-i18next';

interface AdBannerProps {
  position: 'top' | 'middle' | 'bottom';
  className?: string;
}

export const AdBanner: React.FC<AdBannerProps> = ({ position, className = '' }) => {
  const { t } = useTranslation('home');
  
  // In a real app, these would likely come from a backend or configuration
  // For now, we'll use placeholders
  const adContent = {
    top: {
      image: "https://placehold.co/1200x400/4d3070/ffffff?text=Advertising+Space+(Top)",
      link: "#",
      alt: "Top Banner Ad"
    },
    middle: {
      image: "https://placehold.co/1200x400/e6a319/ffffff?text=Advertising+Space+(Middle)",
      link: "#",
      alt: "Middle Banner Ad"
    },
    bottom: {
      image: "https://placehold.co/1200x250/181411/ffffff?text=Advertising+Space+(Bottom)",
      link: "#",
      alt: "Bottom Banner Ad"
    }
  };

  const content = adContent[position];

  return (
    <div className={`w-full py-8 ${className}`}>
      <div className="max-w-[1440px] mx-auto px-6 lg:px-8">
        <a 
          href={content.link} 
          target="_blank" 
          rel="noopener noreferrer"
          className="block w-full overflow-hidden rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 group"
        >
          <div className="relative aspect-[4/2] md:aspect-[6/2] w-full bg-gray-100 flex items-center justify-center overflow-hidden">
            {/* Replace this img with your actual ad image */}
            <img 
              src={content.image} 
              alt={content.alt} 
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300" />
            
            {/* Optional: Label to indicate it's an ad */}
            <div className="absolute top-2 right-2 bg-white/90 px-2 py-0.5 rounded text-[10px] font-medium text-gray-500 uppercase tracking-wider shadow-sm">
              Ad
            </div>
          </div>
        </a>
      </div>
    </div>
  );
};
