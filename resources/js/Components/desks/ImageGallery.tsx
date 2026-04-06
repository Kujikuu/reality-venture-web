import React, { useState } from 'react';
import { MapPin } from 'lucide-react';

interface ImageGalleryProps {
    coverImage: string | null;
    images: string[];
    name: string;
    typeBadge: string;
}

export const ImageGallery: React.FC<ImageGalleryProps> = ({ coverImage, images, name, typeBadge }) => {
    const allImages = [
        ...(coverImage ? [coverImage] : []),
        ...images.filter((img) => img !== coverImage),
    ];

    const [activeIndex, setActiveIndex] = useState(0);
    const activeImage = allImages[activeIndex] ?? null;

    return (
        <div className="space-y-3">
            {/* Main image */}
            <div className="relative h-72 md:h-96 bg-gray-100 rounded-2xl overflow-hidden">
                {activeImage ? (
                    <img src={activeImage} alt={name} className="w-full h-full object-cover" />
                ) : (
                    <div className="w-full h-full flex flex-col items-center justify-center gap-2 text-gray-400">
                        <MapPin className="w-10 h-10" />
                        <span className="text-sm">No photos yet</span>
                    </div>
                )}
                {/* Type badge */}
                <span className="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-3 py-1.5 rounded-full capitalize">
                    {typeBadge}
                </span>
            </div>

            {/* Thumbnails */}
            {allImages.length > 1 && (
                <div className="flex gap-2 overflow-x-auto pb-1">
                    {allImages.map((img, i) => (
                        <button
                            key={i}
                            onClick={() => setActiveIndex(i)}
                            className={`flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all ${
                                i === activeIndex
                                    ? 'ring-2 ring-primary border-primary'
                                    : 'border-transparent hover:border-gray-300'
                            }`}
                        >
                            <img src={img} alt={`${name} ${i + 1}`} className="w-full h-full object-cover" />
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
};
