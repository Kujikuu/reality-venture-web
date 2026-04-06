import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import { MapPin, Users, ChevronRight, Check, Share2 } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';
import { useDesksAuth } from '../../hooks/useDesksAuth';
import { ImageGallery } from '../../Components/desks/ImageGallery';
import { AvailabilityTable } from '../../Components/desks/AvailabilityTable';
import { BookingCard } from '../../Components/desks/BookingCard';
import { AuthModal } from '../../Components/desks/AuthModal';

interface Pricing {
    price_per_hour: number | null;
    price_per_day: number | null;
    currency: string;
}

interface Slot {
    day_of_week: number;
    open_from: string;
    open_to: string;
    is_closed: boolean;
}

interface Host {
    id: number;
    name: string;
}

interface Workspace {
    id: number;
    type: string;
    name: string;
    city: string;
    capacity: number;
    cover_image: string | null;
    images: string[];
    description: string | null;
    amenities: string[];
    pricing: Pricing | null;
    availability: Slot[];
    host: Host | null;
}

interface ShowProps {
    workspaceId: number;
}

export default function DesksShow({ workspaceId }: ShowProps) {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();
    const {
        isAuthenticated,
        loading: authLoading,
        showModal,
        setShowModal,
        login,
        register,
        logout,
        authError,
        authErrors,
        authLoading: authSubmitLoading,
        clearErrors,
    } = useDesksAuth();

    const [workspace, setWorkspace] = useState<Workspace | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        setLoading(true);
        setError('');
        fetchApi(`/api/v1/workspaces/${workspaceId}`)
            .then((res) => {
                if (!res.ok) throw new Error('fetch failed');
                return res.json();
            })
            .then((json) => {
                setWorkspace(json.data ?? json);
            })
            .catch(() => {
                setError(t('listing.error'));
            })
            .finally(() => {
                setLoading(false);
            });
    }, [workspaceId, fetchApi, t]);

    const handleShare = () => {
        navigator.clipboard.writeText(window.location.href).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    };

    return (
        <>
            <Head title={workspace?.name ?? 'Desks'} />

            <div className="max-w-6xl mx-auto px-4 py-8">
                {/* Breadcrumb */}
                {!loading && !error && workspace && (
                    <nav className="flex items-center gap-1.5 text-sm text-gray-500 mb-6">
                        <Link href="/desks" className="hover:text-primary transition-colors">
                            {t('detail.breadcrumbHome')}
                        </Link>
                        <ChevronRight className="w-4 h-4 flex-shrink-0" />
                        <span className="text-text-main font-medium truncate">{workspace.name}</span>
                    </nav>
                )}

                {/* Loading */}
                {loading && (
                    <div className="flex flex-col lg:flex-row gap-8">
                        <div className="flex-1 space-y-4">
                            <div className="h-72 md:h-96 rounded-2xl bg-gray-200 animate-pulse" />
                            <div className="h-8 w-2/3 rounded-lg bg-gray-200 animate-pulse" />
                            <div className="h-4 w-1/3 rounded-lg bg-gray-200 animate-pulse" />
                            <div className="h-24 rounded-lg bg-gray-200 animate-pulse" />
                        </div>
                        <div className="lg:w-96">
                            <div className="h-80 rounded-2xl bg-gray-200 animate-pulse" />
                        </div>
                    </div>
                )}

                {/* Error */}
                {!loading && error && (
                    <div className="text-center py-16">
                        <p className="text-red-500 mb-4">{error}</p>
                        <button
                            onClick={() => {
                                setLoading(true);
                                setError('');
                                fetchApi(`/api/v1/workspaces/${workspaceId}`)
                                    .then((res) => {
                                        if (!res.ok) throw new Error('fetch failed');
                                        return res.json();
                                    })
                                    .then((json) => setWorkspace(json.data ?? json))
                                    .catch(() => setError(t('listing.error')))
                                    .finally(() => setLoading(false));
                            }}
                            className="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                        >
                            {t('listing.retry')}
                        </button>
                    </div>
                )}

                {/* Content */}
                {!loading && !error && workspace && (
                    <div className="flex flex-col lg:flex-row gap-8">
                        {/* Left column */}
                        <div className="flex-1 min-w-0 space-y-8">
                            <ImageGallery
                                coverImage={workspace.cover_image}
                                images={workspace.images ?? []}
                                name={workspace.name}
                                typeBadge={t(`tabs.${workspace.type}`)}
                            />

                            {/* Name + share */}
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <h1 className="text-2xl font-bold text-text-main leading-snug">
                                        {workspace.name}
                                    </h1>
                                    <span className="inline-block mt-2 bg-primary/10 text-primary text-xs font-medium px-3 py-1 rounded-full capitalize">
                                        {t(`tabs.${workspace.type}`)}
                                    </span>
                                </div>
                                <div className="flex flex-col items-end gap-1 flex-shrink-0">
                                    <button
                                        onClick={handleShare}
                                        className="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors"
                                    >
                                        <Share2 className="w-4 h-4" />
                                        {t('share.copyLink')}
                                    </button>
                                    {copied && (
                                        <span className="text-xs text-green-600 font-medium">
                                            {t('share.copied')}
                                        </span>
                                    )}
                                </div>
                            </div>

                            {/* Meta row */}
                            <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                {workspace.city && (
                                    <span className="flex items-center gap-1.5">
                                        <MapPin className="w-4 h-4" />
                                        {workspace.city}
                                    </span>
                                )}
                                {workspace.capacity > 0 && (
                                    <span className="flex items-center gap-1.5">
                                        <Users className="w-4 h-4" />
                                        {t('card.capacity', { count: workspace.capacity })}
                                    </span>
                                )}
                                {workspace.host && (
                                    <span className="flex items-center gap-1.5">
                                        {t('detail.host')} {workspace.host.name}
                                    </span>
                                )}
                            </div>

                            {/* Description */}
                            {workspace.description && (
                                <div>
                                    <h3 className="text-base font-semibold text-text-main mb-2">
                                        {t('detail.description')}
                                    </h3>
                                    <p className="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                                        {workspace.description}
                                    </p>
                                </div>
                            )}

                            {/* Amenities */}
                            {workspace.amenities && workspace.amenities.length > 0 && (
                                <div>
                                    <h3 className="text-base font-semibold text-text-main mb-3">
                                        {t('detail.amenities')}
                                    </h3>
                                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        {workspace.amenities.map((amenity: any) => (
                                            <div
                                                key={amenity.id || amenity.key}
                                                className="flex items-center gap-2 text-sm text-gray-600"
                                            >
                                                <Check className="w-4 h-4 text-primary flex-shrink-0" />
                                                <span>{amenity.label || amenity.key}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Availability */}
                            {workspace.availability && workspace.availability.length > 0 && (
                                <AvailabilityTable availability={workspace.availability} />
                            )}
                        </div>

                        {/* Right column — sticky booking card */}
                        <div className="lg:w-96 lg:sticky lg:top-28 self-start">
                            <BookingCard
                                workspaceId={workspace.id}
                                pricing={workspace.pricing}
                                capacity={workspace.capacity}
                                availability={workspace.availability ?? []}
                                isAuthenticated={isAuthenticated}
                                onAuthRequired={() => setShowModal(true)}
                            />
                        </div>
                    </div>
                )}
            </div>

            {/* Auth Modal */}
            <AuthModal
                show={showModal}
                onClose={() => {
                    setShowModal(false);
                    clearErrors();
                }}
                onLogin={login}
                onRegister={register}
                authError={authError}
                authErrors={authErrors}
                authLoading={authSubmitLoading}
                onClearErrors={clearErrors}
            />
        </>
    );
}
