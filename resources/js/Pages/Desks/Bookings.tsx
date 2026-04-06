import React, { useState, useEffect, useCallback } from 'react';
import { Head, Link } from '@inertiajs/react';
import { Calendar, ChevronRight, Clock, Users } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';
import { useDesksAuth } from '../../hooks/useDesksAuth';
import { AuthModal } from '../../Components/desks/AuthModal';
import { CancelModal } from '../../Components/desks/CancelModal';

interface Workspace {
    id: number;
    name: string;
    type: string;
}

interface Booking {
    id: number;
    type: string;
    status: 'reserved' | 'completed' | 'cancelled' | 'no_show';
    start_at: string;
    end_at: string;
    guests_count: number;
    total_price: number | null;
    currency: string;
    workspace: Workspace;
}

const statusClasses: Record<string, string> = {
    reserved: 'bg-green-100 text-green-700',
    completed: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-gray-100 text-gray-500',
    no_show: 'bg-red-100 text-red-700',
};

const formatDate = (iso: string) =>
    new Date(iso).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });

const formatTime = (iso: string) =>
    new Date(iso).toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', hour12: false });

export default function DesksBookings() {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();
    const {
        user,
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

    const [bookings, setBookings] = useState<Booking[]>([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [loadingMore, setLoadingMore] = useState(false);

    const [cancelTarget, setCancelTarget] = useState<number | null>(null);
    const [cancelLoading, setCancelLoading] = useState(false);
    const [cancelSuccess, setCancelSuccess] = useState(false);

    const fetchBookings = useCallback(
        async (pageNum: number, append: boolean) => {
            if (!append) {
                setLoading(true);
            } else {
                setLoadingMore(true);
            }

            try {
                const res = await fetchApi(`/api/v1/bookings?page=${pageNum}`);
                if (!res.ok) throw new Error('fetch failed');
                const json = await res.json();
                const items: Booking[] = json.data ?? json;
                const meta = json.meta ?? {};
                const currentPage: number = meta.current_page ?? pageNum;
                const lastPage: number = meta.last_page ?? 1;

                if (append) {
                    setBookings((prev) => [...prev, ...items]);
                } else {
                    setBookings(items);
                }
                setHasMore(currentPage < lastPage);
                setPage(currentPage);
            } catch {
                // silently fail, show empty state
            } finally {
                setLoading(false);
                setLoadingMore(false);
            }
        },
        [fetchApi],
    );

    useEffect(() => {
        if (!authLoading && isAuthenticated) {
            fetchBookings(1, false);
        } else if (!authLoading && !isAuthenticated) {
            setLoading(false);
        }
    }, [isAuthenticated, authLoading, fetchBookings]);

    const handleCancelConfirm = async () => {
        if (cancelTarget === null) return;
        setCancelLoading(true);

        try {
            const res = await fetchApi(`/api/v1/bookings/${cancelTarget}/cancel`, { method: 'POST' });
            if (!res.ok) throw new Error('cancel failed');

            setBookings((prev) =>
                prev.map((b) => (b.id === cancelTarget ? { ...b, status: 'cancelled' } : b)),
            );
            setCancelSuccess(true);
            setTimeout(() => setCancelSuccess(false), 4000);
        } catch {
            // silently fail
        } finally {
            setCancelLoading(false);
            setCancelTarget(null);
        }
    };

    return (
        <>
            <Head title={t('bookings.title')} />

            <div className="max-w-4xl mx-auto px-4 py-8">
                {/* Breadcrumb */}
                <nav className="flex items-center gap-1.5 text-sm text-gray-500 mb-6">
                    <Link href="/desks" className="hover:text-primary transition-colors">
                        {t('detail.breadcrumbHome')}
                    </Link>
                    <ChevronRight className="w-4 h-4 flex-shrink-0" />
                    <span className="text-text-main font-medium">{t('bookings.title')}</span>
                </nav>

                <h1 className="text-2xl font-bold text-text-main mb-6">{t('bookings.title')}</h1>

                {/* Cancel success banner */}
                {cancelSuccess && (
                    <div className="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {t('bookings.cancelledSuccess')}
                    </div>
                )}

                {/* Auth loading */}
                {authLoading && (
                    <div className="space-y-4">
                        {[1, 2, 3].map((i) => (
                            <div key={i} className="h-28 rounded-xl bg-gray-200 animate-pulse" />
                        ))}
                    </div>
                )}

                {/* Login required */}
                {!authLoading && !isAuthenticated && (
                    <div className="text-center py-16">
                        <Calendar className="w-12 h-12 mx-auto mb-4 text-gray-300" />
                        <p className="font-medium text-text-main mb-2">{t('bookings.loginRequired')}</p>
                        <button
                            onClick={() => setShowModal(true)}
                            className="mt-4 px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                        >
                            {t('auth.loginButton')}
                        </button>
                    </div>
                )}

                {/* Loading bookings */}
                {!authLoading && isAuthenticated && loading && (
                    <div className="space-y-4">
                        {[1, 2, 3].map((i) => (
                            <div key={i} className="h-28 rounded-xl bg-gray-200 animate-pulse" />
                        ))}
                    </div>
                )}

                {/* Empty state */}
                {!authLoading && isAuthenticated && !loading && bookings.length === 0 && (
                    <div className="text-center py-16">
                        <Calendar className="w-12 h-12 mx-auto mb-4 text-gray-300" />
                        <p className="font-medium text-text-main">{t('bookings.empty')}</p>
                        <p className="text-sm text-gray-500 mt-1">{t('bookings.emptyDescription')}</p>
                        <Link
                            href="/desks"
                            className="inline-block mt-4 px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                        >
                            {t('bookings.browse')}
                        </Link>
                    </div>
                )}

                {/* Booking cards */}
                {!authLoading && isAuthenticated && !loading && bookings.length > 0 && (
                    <div className="space-y-4">
                        {bookings.map((booking) => (
                            <div
                                key={booking.id}
                                className="rounded-xl border border-gray-200 bg-white p-5 space-y-4"
                            >
                                {/* Top row */}
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <Link
                                            href={`/desks/${booking.workspace.id}`}
                                            className="font-semibold text-text-main hover:text-primary transition-colors line-clamp-1"
                                        >
                                            {booking.workspace.name}
                                        </Link>
                                        <span className="inline-block mt-1 bg-primary/10 text-primary text-xs px-2.5 py-0.5 rounded-full capitalize">
                                            {booking.workspace.type.replace('_', ' ')}
                                        </span>
                                    </div>
                                    <span
                                        className={`text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0 ${
                                            statusClasses[booking.status] ?? 'bg-gray-100 text-gray-500'
                                        }`}
                                    >
                                        {t(`bookings.status.${booking.status}`)}
                                    </span>
                                </div>

                                {/* Details row */}
                                <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <span className="flex items-center gap-1.5">
                                        <Calendar className="w-3.5 h-3.5" />
                                        {formatDate(booking.start_at)}
                                    </span>
                                    <span className="flex items-center gap-1.5">
                                        <Clock className="w-3.5 h-3.5" />
                                        {formatTime(booking.start_at)} – {formatTime(booking.end_at)}
                                    </span>
                                    <span className="flex items-center gap-1.5">
                                        <Users className="w-3.5 h-3.5" />
                                        {booking.guests_count} {t('bookings.guests')}
                                    </span>
                                </div>

                                {/* Price + actions */}
                                <div className="flex items-center justify-between gap-3 pt-2 border-t border-gray-100">
                                    {booking.total_price != null ? (
                                        <span className="text-secondary font-semibold text-sm">
                                            {booking.total_price} {booking.currency}
                                        </span>
                                    ) : (
                                        <span />
                                    )}

                                    <div className="flex items-center gap-2">
                                        {(booking.status === 'completed' || booking.status === 'cancelled') && (
                                            <Link
                                                href={`/desks/${booking.workspace.id}`}
                                                className="text-xs text-primary font-medium hover:underline"
                                            >
                                                {t('bookings.bookAgain')}
                                            </Link>
                                        )}
                                        {booking.status === 'reserved' && (
                                            <button
                                                onClick={() => setCancelTarget(booking.id)}
                                                className="text-xs text-red-600 font-medium border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors"
                                            >
                                                {t('bookings.cancelButton')}
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}

                        {/* Load more */}
                        {hasMore && (
                            <div className="text-center pt-4">
                                <button
                                    onClick={() => fetchBookings(page + 1, true)}
                                    disabled={loadingMore}
                                    className="px-8 py-3 bg-primary text-white rounded-xl font-medium hover:bg-primary/90 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {loadingMore ? '...' : t('listing.loadMore')}
                                </button>
                            </div>
                        )}
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

            {/* Cancel Modal */}
            <CancelModal
                show={cancelTarget !== null}
                onClose={() => setCancelTarget(null)}
                onConfirm={handleCancelConfirm}
                loading={cancelLoading}
            />
        </>
    );
}
