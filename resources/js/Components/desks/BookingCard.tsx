import React, { useState, useMemo } from 'react';
import { Link } from '@inertiajs/react';
import { Calendar, Clock, Users, Check } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useDesksApi } from '../../hooks/useDesksApi';
import { SarIcon } from '../ui/SarIcon';

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

interface BookingCardProps {
    workspaceId: number;
    pricing: Pricing | null;
    capacity: number;
    availability: Slot[];
    isAuthenticated: boolean;
    onAuthRequired: () => void;
}

type BookingType = 'hourly' | 'daily';

const fieldClass =
    'flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary';
const inputClass = 'flex-1 outline-none text-sm text-text-main bg-transparent placeholder:text-gray-400';

const toTime = (t: string) => (t.length === 5 ? t + ':00' : t);

export const BookingCard: React.FC<BookingCardProps> = ({
    workspaceId,
    pricing,
    capacity,
    availability,
    isAuthenticated,
    onAuthRequired,
}) => {
    const { t } = useTranslation('desks');
    const { fetchApi } = useDesksApi();

    const [bookingType, setBookingType] = useState<BookingType>(
        pricing?.price_per_hour != null ? 'hourly' : 'daily',
    );
    const [date, setDate] = useState('');
    const [startTime, setStartTime] = useState('09:00');
    const [endTime, setEndTime] = useState('10:00');
    const [guests, setGuests] = useState(1);

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [successId, setSuccessId] = useState<number | null>(null);


    const selectedDayOfWeek = useMemo(() => {
        if (!date) return null;
        return new Date(date + 'T12:00:00').getDay();
    }, [date]);

    const isClosedDay = useMemo(() => {
        if (selectedDayOfWeek === null) return false;
        const slot = availability.find((s) => s.day_of_week === selectedDayOfWeek);
        return slot?.is_closed ?? false;
    }, [selectedDayOfWeek, availability]);

    const openSlot = useMemo(() => {
        if (selectedDayOfWeek === null) return null;
        return availability.find((s) => s.day_of_week === selectedDayOfWeek && !s.is_closed) ?? null;
    }, [selectedDayOfWeek, availability]);

    const total = useMemo(() => {
        if (!date || isClosedDay) return null;

        if (bookingType === 'daily') {
            return pricing?.price_per_day ?? null;
        }

        if (!startTime || !endTime) return null;
        const [sh, sm] = startTime.split(':').map(Number);
        const [eh, em] = endTime.split(':').map(Number);
        const diffHours = (eh * 60 + em - (sh * 60 + sm)) / 60;
        if (diffHours <= 0) return null;
        const rate = pricing?.price_per_hour ?? null;
        return rate != null ? Math.round(rate * diffHours * 100) / 100 : null;
    }, [bookingType, date, startTime, endTime, pricing, isClosedDay]);

    const handleReserve = async () => {
        if (!isAuthenticated) {
            onAuthRequired();
            return;
        }
        await submitBooking();
    };

    const submitBooking = async () => {
        setError('');
        setLoading(true);

        try {
            let startAt: string;
            let endAt: string;

            if (bookingType === 'daily') {
                const openFrom = openSlot ? toTime(openSlot.open_from) : '08:00:00';
                const openTo = openSlot ? toTime(openSlot.open_to) : '18:00:00';
                startAt = `${date} ${openFrom}`;
                endAt = `${date} ${openTo}`;
            } else {
                startAt = `${date} ${toTime(startTime)}`;
                endAt = `${date} ${toTime(endTime)}`;
            }

            const res = await fetchApi('/api/v1/bookings', {
                method: 'POST',
                body: JSON.stringify({
                    workspace_id: workspaceId,
                    type: bookingType,
                    start_at: startAt,
                    end_at: endAt,
                    guests_count: guests,
                }),
            });

            const json = await res.json();

            if (!res.ok) {
                const msg = json.message || json.error || t('booking.error');
                setError(msg);
                return;
            }

            const id = json.data?.id ?? json.id;
            setSuccessId(id);
        } catch {
            setError(t('booking.error'));
        } finally {
            setLoading(false);
        }
    };

    if (successId !== null) {
        return (
            <div className="rounded-2xl border border-gray-200 bg-white p-6 space-y-4">
                <div className="flex flex-col items-center text-center gap-3 py-4">
                    <div className="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <Check className="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                        <p className="font-semibold text-text-main">{t('booking.successTitle')}</p>
                        <p className="text-sm text-gray-500 mt-1">{t('booking.successDescription')}</p>
                    </div>
                    <p className="text-xs text-gray-400">
                        {t('booking.successId')}: <span className="font-medium text-text-main">#{successId}</span>
                    </p>
                    <Link
                        href="/grit/bookings"
                        className="text-sm text-primary font-medium hover:underline"
                    >
                        View My Bookings
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="rounded-2xl border border-gray-200 bg-white p-6 space-y-5">
            {/* Price header */}
            <div>
                <h3 className="text-base font-semibold text-text-main mb-1">{t('booking.title')}</h3>
                <div className="flex items-center gap-3 flex-wrap">
                    {pricing?.price_per_hour != null && (
                        <span className="text-secondary font-bold text-lg">
                            <SarIcon /> {pricing.price_per_hour}
                            <span className="text-sm font-normal text-gray-500"> /hr</span>
                        </span>
                    )}
                    {pricing?.price_per_day != null && (
                        <span className="text-gray-500 text-sm">
                            <SarIcon /> {pricing.price_per_day} /day
                        </span>
                    )}
                </div>
            </div>

            {/* Hourly / Daily toggle */}
            {pricing?.price_per_hour != null && pricing?.price_per_day != null && (
                <div className="flex bg-gray-100 rounded-lg p-1">
                    {(['hourly', 'daily'] as BookingType[]).map((type) => (
                        <button
                            key={type}
                            onClick={() => setBookingType(type)}
                            className={`flex-1 py-2 rounded-md text-sm font-medium transition-all ${
                                bookingType === type
                                    ? 'bg-white text-primary shadow-sm'
                                    : 'text-gray-500 hover:text-gray-700'
                            }`}
                        >
                            {type === 'hourly' ? t('booking.typeHourly') : t('booking.typeDaily')}
                        </button>
                    ))}
                </div>
            )}

            {/* Date */}
            <div>
                <div className={fieldClass}>
                    <Calendar className="w-4 h-4 text-gray-400 flex-shrink-0" />
                    <input
                        type="date"
                        value={date}
                        onChange={(e) => setDate(e.target.value)}
                        min={new Date().toISOString().split('T')[0]}
                        className={inputClass}
                    />
                </div>
                {isClosedDay && (
                    <p className="text-red-500 text-xs mt-1">{t('booking.closedDay')}</p>
                )}
            </div>

            {/* Time inputs (hourly only) */}
            {bookingType === 'hourly' && (
                <div className="grid grid-cols-2 gap-3">
                    <div>
                        <div className={fieldClass}>
                            <Clock className="w-4 h-4 text-gray-400 flex-shrink-0" />
                            <input
                                type="time"
                                value={startTime}
                                onChange={(e) => setStartTime(e.target.value)}
                                className={inputClass}
                            />
                        </div>
                        <p className="text-xs text-gray-400 mt-1">{t('booking.startTime')}</p>
                    </div>
                    <div>
                        <div className={fieldClass}>
                            <Clock className="w-4 h-4 text-gray-400 flex-shrink-0" />
                            <input
                                type="time"
                                value={endTime}
                                onChange={(e) => setEndTime(e.target.value)}
                                className={inputClass}
                            />
                        </div>
                        <p className="text-xs text-gray-400 mt-1">{t('booking.endTime')}</p>
                    </div>
                </div>
            )}

            {/* Guests */}
            <div>
                <div className={fieldClass}>
                    <Users className="w-4 h-4 text-gray-400 flex-shrink-0" />
                    <input
                        type="number"
                        min={1}
                        max={capacity || 100}
                        value={guests}
                        onChange={(e) => setGuests(Math.max(1, Number(e.target.value)))}
                        className={inputClass}
                    />
                    <span className="text-xs text-gray-400 flex-shrink-0">{t('booking.guests')}</span>
                </div>
            </div>

            {/* Total */}
            {total !== null && (
                <div className="bg-gray-50 rounded-lg px-4 py-3 flex items-center justify-between">
                    <span className="text-sm text-gray-600">{t('booking.total')}</span>
                    <span className="font-semibold text-text-main">
                        <SarIcon /> {total}
                    </span>
                </div>
            )}

            {/* Error */}
            {error && (
                <div className="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
                    {error}
                </div>
            )}

            {/* Reserve button */}
            <button
                onClick={handleReserve}
                disabled={loading || isClosedDay || !date}
                className="w-full bg-primary text-white py-3 rounded-xl font-medium text-sm hover:bg-primary/90 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
            >
                {loading ? '...' : t('booking.reserve')}
            </button>
        </div>
    );
};
