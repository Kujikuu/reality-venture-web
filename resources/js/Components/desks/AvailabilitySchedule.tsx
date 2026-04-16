import React, { useMemo } from 'react';
import { useTranslation } from 'react-i18next';
import { Clock } from 'lucide-react';
import { formatTimeLabel } from '../../lib/time';

interface Slot {
    day_of_week: number;
    open_from: string;
    open_to: string;
    is_closed: boolean;
}

interface AvailabilityScheduleProps {
    availability: Slot[];
}

const toMinutes = (value: string): number => {
    const match = /^(\d{1,2}):(\d{2})/.exec(value);
    if (!match) {
        return 0;
    }
    return Number(match[1]) * 60 + Number(match[2]);
};

const SCALE_PADDING_MIN = 60;

export const AvailabilitySchedule: React.FC<AvailabilityScheduleProps> = ({ availability }) => {
    const { t } = useTranslation('desks');
    const dayNames = t('detail.days', { returnObjects: true }) as string[];

    const sorted = useMemo(
        () => [...availability].sort((a, b) => a.day_of_week - b.day_of_week),
        [availability],
    );

    const today = new Date().getDay();
    const todaySlot = sorted.find((s) => s.day_of_week === today) ?? null;

    const { scaleStart, scaleEnd, scaleTicks } = useMemo(() => {
        const openSlots = sorted.filter((s) => !s.is_closed);
        if (openSlots.length === 0) {
            return { scaleStart: 0, scaleEnd: 24 * 60, scaleTicks: [] as number[] };
        }

        const earliest = Math.min(...openSlots.map((s) => toMinutes(s.open_from)));
        const latest = Math.max(...openSlots.map((s) => toMinutes(s.open_to)));

        const start = Math.max(0, Math.floor((earliest - SCALE_PADDING_MIN) / 60) * 60);
        const end = Math.min(24 * 60, Math.ceil((latest + SCALE_PADDING_MIN) / 60) * 60);

        const span = end - start;
        const tickCount = span <= 6 * 60 ? 4 : span <= 12 * 60 ? 5 : 6;
        const stepMinutes = Math.round(span / (tickCount - 1) / 60) * 60;

        const ticks: number[] = [];
        for (let m = start; m <= end; m += stepMinutes) {
            ticks.push(m);
        }
        return { scaleStart: start, scaleEnd: end, scaleTicks: ticks };
    }, [sorted]);

    const scaleSpan = scaleEnd - scaleStart;

    const positionFor = (slot: Slot) => {
        const from = toMinutes(slot.open_from);
        const to = toMinutes(slot.open_to);
        const left = ((from - scaleStart) / scaleSpan) * 100;
        const width = ((to - from) / scaleSpan) * 100;
        return { left: `${left}%`, width: `${width}%` };
    };

    const formatTickLabel = (minutes: number): string => {
        const hh = Math.floor(minutes / 60);
        const mm = minutes % 60;
        return `${hh.toString().padStart(2, '0')}:${mm.toString().padStart(2, '0')}`;
    };

    return (
        <div>
            <div className="flex items-center justify-between mb-3 flex-wrap gap-2">
                <h3 className="text-base font-semibold text-text-main">
                    {t('detail.availability')}
                </h3>
                {todaySlot && (
                    <span
                        className={`inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full ${
                            todaySlot.is_closed
                                ? 'bg-gray-100 text-gray-500'
                                : 'bg-primary/10 text-primary'
                        }`}
                    >
                        <Clock className="w-3 h-3" />
                        {todaySlot.is_closed
                            ? t('detail.closedToday')
                            : t('detail.openToday', {
                                  from: formatTimeLabel(todaySlot.open_from),
                                  to: formatTimeLabel(todaySlot.open_to),
                              })}
                    </span>
                )}
            </div>

            <div className="rounded-xl border border-gray-200 bg-white p-4 sm:p-5">
                <div className="space-y-2.5">
                    {sorted.map((slot) => {
                        const isToday = slot.day_of_week === today;
                        const dayName = dayNames[slot.day_of_week] ?? `Day ${slot.day_of_week}`;

                        return (
                            <div
                                key={slot.day_of_week}
                                className={`grid grid-cols-[5.5rem_1fr_auto] items-center gap-3 sm:gap-4 px-2 py-1.5 rounded-lg ${
                                    isToday ? 'bg-primary/5' : ''
                                }`}
                            >
                                <div className="flex items-center gap-2 min-w-0">
                                    {isToday && (
                                        <span className="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
                                    )}
                                    <span
                                        className={`text-sm truncate ${
                                            isToday
                                                ? 'text-primary font-semibold'
                                                : 'text-text-main font-medium'
                                        }`}
                                    >
                                        {dayName}
                                    </span>
                                </div>

                                <div
                                    className="relative h-2 rounded-full bg-gray-100 overflow-hidden"
                                    dir="ltr"
                                >
                                    {!slot.is_closed && (
                                        <div
                                            className="absolute top-0 h-full bg-primary rounded-full"
                                            style={positionFor(slot)}
                                        />
                                    )}
                                </div>

                                <div
                                    className="text-xs tabular-nums text-right"
                                    dir="ltr"
                                >
                                    {slot.is_closed ? (
                                        <span className="text-gray-400">{t('detail.closed')}</span>
                                    ) : (
                                        <span className="text-gray-600">
                                            {formatTimeLabel(slot.open_from)}
                                            {' – '}
                                            {formatTimeLabel(slot.open_to)}
                                        </span>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>

                {scaleTicks.length > 0 && (
                    <div
                        className="grid grid-cols-[5.5rem_1fr_auto] items-center gap-3 sm:gap-4 px-2 mt-3 pt-3 border-t border-gray-100"
                        dir="ltr"
                    >
                        <div />
                        <div className="relative h-3">
                            {scaleTicks.map((tick) => {
                                const left = ((tick - scaleStart) / scaleSpan) * 100;
                                return (
                                    <span
                                        key={tick}
                                        className="absolute top-0 -translate-x-1/2 text-[10px] text-gray-400 tabular-nums"
                                        style={{ left: `${left}%` }}
                                    >
                                        {formatTickLabel(tick)}
                                    </span>
                                );
                            })}
                        </div>
                        <div />
                    </div>
                )}
            </div>
        </div>
    );
};
