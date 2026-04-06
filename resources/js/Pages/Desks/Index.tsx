import React, { useState, useEffect, useCallback } from 'react';
import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { MapPin } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { staggerContainer } from '../../Components/animations/CommonAnimations';
import { useDesksApi } from '../../hooks/useDesksApi';
import { useDesksAuth } from '../../hooks/useDesksAuth';
import { SkeletonCard } from '../../Components/desks/SkeletonCard';
import { TypeTabs } from '../../Components/desks/TypeTabs';
import { WorkspaceCard } from '../../Components/desks/WorkspaceCard';
import { UserMenu } from '../../Components/desks/UserMenu';
import { AuthModal } from '../../Components/desks/AuthModal';

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

interface City {
    id: number;
    name: string;
    slug: string;
}

export default function DesksIndex() {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();
    const {
        user,
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

    const [workspaces, setWorkspaces] = useState<Workspace[]>([]);
    const [cities, setCities] = useState<City[]>([]);
    const [activeType, setActiveType] = useState('all');
    const [activeCity, setActiveCity] = useState('');
    const [pendingCity, setPendingCity] = useState('');
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [loading, setLoading] = useState(true);
    const [loadingMore, setLoadingMore] = useState(false);
    const [error, setError] = useState('');

    // Fetch cities once on mount
    useEffect(() => {
        fetchApi('/api/v1/cities')
            .then((res) => res.json())
            .then((json) => {
                const data: City[] = json.data ?? json;
                if (Array.isArray(data)) {
                    setCities(data);
                }
            })
            .catch(() => {});
    }, [fetchApi]);

    const fetchWorkspaces = useCallback(
        async (type: string, city: string, pageNum: number, append: boolean) => {
            if (!append) {
                setLoading(true);
            } else {
                setLoadingMore(true);
            }
            setError('');

            try {
                const params = new URLSearchParams({ page: String(pageNum) });
                if (type && type !== 'all') params.set('type', type);
                if (city) params.set('city', city);

                const res = await fetchApi(`/api/v1/workspaces?${params.toString()}`);
                if (!res.ok) throw new Error('fetch failed');

                const json = await res.json();
                const items: Workspace[] = json.data ?? json;
                const meta = json.meta ?? {};
                const currentPage: number = meta.current_page ?? pageNum;
                const lastPage: number = meta.last_page ?? 1;

                if (append) {
                    setWorkspaces((prev) => [...prev, ...items]);
                } else {
                    setWorkspaces(items);
                }
                setHasMore(currentPage < lastPage);
                setPage(currentPage);
            } catch {
                setError(t('listing.error'));
            } finally {
                setLoading(false);
                setLoadingMore(false);
            }
        },
        [fetchApi, t],
    );

    // Fetch on type or city change
    useEffect(() => {
        fetchWorkspaces(activeType, activeCity, 1, false);
    }, [activeType, activeCity, fetchWorkspaces]);

    const handleTypeChange = (type: string) => {
        setActiveType(type);
    };

    const handleSearch = () => {
        setActiveCity(pendingCity);
    };

    const handleLoadMore = () => {
        fetchWorkspaces(activeType, activeCity, page + 1, true);
    };

    const handleRetry = () => {
        fetchWorkspaces(activeType, activeCity, 1, false);
    };

    return (
        <>
            <Head title={t('title')} />

            {/* Hero */}
            <section className="bg-primary text-white py-16 px-4">
                <div className="max-w-4xl mx-auto text-center">
                    <h1 className="text-3xl md:text-4xl font-bold mb-3">{t('title')}</h1>
                    <p className="text-white/80 text-lg mb-8">{t('subtitle')}</p>

                    <div className="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                        <select
                            value={pendingCity}
                            onChange={(e) => setPendingCity(e.target.value)}
                            className="flex-1 rounded-xl px-4 py-3 text-text-main bg-white text-sm outline-none"
                        >
                            <option value="">{t('search.cityPlaceholder')}</option>
                            {cities.map((city) => (
                                <option key={city.id} value={city.slug}>
                                    {city.name}
                                </option>
                            ))}
                        </select>
                        <button
                            onClick={handleSearch}
                            className="px-6 py-3 bg-secondary text-white rounded-xl font-medium text-sm hover:bg-secondary/90 transition-colors"
                        >
                            {t('search.searchButton')}
                        </button>
                    </div>
                </div>
            </section>

            {/* Tabs + UserMenu row */}
            <div className="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-3">
                <div className="max-w-6xl mx-auto flex items-center justify-between gap-4">
                    <div className="overflow-x-auto flex-1">
                        <TypeTabs activeType={activeType} onTypeChange={handleTypeChange} />
                    </div>
                    <UserMenu
                        user={user}
                        loading={authLoading}
                        onLoginClick={() => setShowModal(true)}
                        onLogout={logout}
                    />
                </div>
            </div>

            {/* Workspace grid */}
            <div className="max-w-6xl mx-auto px-4 py-8">
                {loading ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {Array.from({ length: 6 }).map((_, i) => (
                            <SkeletonCard key={i} />
                        ))}
                    </div>
                ) : error ? (
                    <div className="text-center py-16">
                        <p className="text-red-500 mb-4">{error}</p>
                        <button
                            onClick={handleRetry}
                            className="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                        >
                            {t('listing.retry')}
                        </button>
                    </div>
                ) : workspaces.length === 0 ? (
                    <div className="text-center py-16 text-gray-500">
                        <MapPin className="w-10 h-10 mx-auto mb-3 text-gray-300" />
                        <p className="font-medium">{t('listing.noResults')}</p>
                        <p className="text-sm mt-1">{t('listing.noResultsDescription')}</p>
                    </div>
                ) : (
                    <>
                        <motion.div
                            variants={staggerContainer}
                            initial="hidden"
                            animate="visible"
                            className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            {workspaces.map((workspace) => (
                                <WorkspaceCard key={workspace.id} workspace={workspace} />
                            ))}
                        </motion.div>

                        {hasMore && (
                            <div className="text-center mt-10">
                                <button
                                    onClick={handleLoadMore}
                                    disabled={loadingMore}
                                    className="px-8 py-3 bg-primary text-white rounded-xl font-medium hover:bg-primary/90 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {loadingMore ? '...' : t('listing.loadMore')}
                                </button>
                            </div>
                        )}
                    </>
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
