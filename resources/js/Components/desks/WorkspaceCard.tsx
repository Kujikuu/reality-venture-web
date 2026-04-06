import React from 'react';
import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { MapPin, Users, ArrowUpRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { cardVariants } from '../animations/CommonAnimations';

interface Pricing {
    price_per_hour: number | null;
    price_per_day: number | null;
    currency: string;
}

interface Workspace {
    id: number;
    type: string;
    name: string;
    city: string;
    capacity: number | null;
    cover_image: string | null;
    pricing: Pricing | null;
    amenities: string[];
}

interface WorkspaceCardProps {
    workspace: Workspace;
}

export const WorkspaceCard: React.FC<WorkspaceCardProps> = ({ workspace }) => {
    const { t } = useTranslation('desks');
    const topAmenities = workspace.amenities.slice(0, 3);
    const pricing = workspace.pricing;
    const currency = pricing?.currency ?? 'SAR';

    return (
        <motion.div
            variants={cardVariants}
            className="h-full rounded-xl border border-gray-200 bg-white overflow-hidden transition-shadow duration-300 hover:shadow-lg"
        >
                <Link href={`/desks/${workspace.id}`} className="block">
                    {/* Image */}
                    <div className="relative h-48 bg-gray-100 overflow-hidden">
                        {workspace.cover_image ? (
                            <img
                                src={workspace.cover_image}
                                alt={workspace.name}
                                className="w-full h-full object-cover"
                            />
                        ) : (
                            <div className="w-full h-full flex flex-col items-center justify-center gap-2 text-gray-400">
                                <MapPin className="w-8 h-8" />
                                <span className="text-sm">{t('card.noPhoto')}</span>
                            </div>
                        )}
                        {/* Type badge */}
                        <span className="absolute top-3 start-3 bg-primary text-white text-xs font-medium px-3 py-1 rounded-full">
                            {t(`tabs.${workspace.type}`)}
                        </span>
                    </div>

                    {/* Content */}
                    <div className="p-4 space-y-3">
                        {/* Name row */}
                        <div className="flex items-start justify-between gap-2">
                            <h3 className="font-semibold text-text-main text-base leading-snug line-clamp-2">
                                {workspace.name}
                            </h3>
                            <ArrowUpRight className="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" />
                        </div>

                        {/* City + capacity */}
                        <div className="flex items-center gap-4 text-sm text-gray-500">
                            <span className="flex items-center gap-1">
                                <MapPin className="w-3.5 h-3.5" />
                                {workspace.city}
                            </span>
                            {workspace.capacity != null && (
                                <span className="flex items-center gap-1">
                                    <Users className="w-3.5 h-3.5" />
                                    {t('card.capacity', { count: workspace.capacity })}
                                </span>
                            )}
                        </div>

                        {/* Amenities */}
                        {topAmenities.length > 0 && (
                            <div className="flex flex-wrap gap-1.5">
                                {topAmenities.map((amenity) => (
                                    <span
                                        key={amenity.id || amenity.key}
                                        className="bg-surface text-gray-600 text-xs px-2.5 py-1 rounded-full"
                                    >
                                        {amenity.label || amenity.key}
                                    </span>
                                ))}
                            </div>
                        )}

                        {/* Price row */}
                        <div className="flex items-center justify-between pt-1 border-t border-gray-100">
                            {pricing?.price_per_hour != null ? (
                                <span className="text-secondary font-semibold text-sm">
                                    {pricing.price_per_hour} {currency}{t('card.perHour')}
                                </span>
                            ) : pricing?.price_per_day != null ? (
                                <span className="text-secondary font-semibold text-sm">
                                    {pricing.price_per_day} {currency}{t('card.perDay')}
                                </span>
                            ) : null}

                            {pricing?.price_per_hour != null && pricing?.price_per_day != null && (
                                <span className="text-gray-400 text-xs">
                                    {pricing.price_per_day} {currency}{t('card.perDay')}
                                </span>
                            )}
                        </div>
                    </div>
                </Link>
        </motion.div>
    );
};
