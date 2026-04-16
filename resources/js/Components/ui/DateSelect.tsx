import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-react';
import { AnimatePresence, motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';

interface DateSelectProps {
    value: string;
    onChange: (value: string) => void;
    min?: string;
    max?: string;
    closedDaysOfWeek?: number[];
    placeholder?: string;
    disabled?: boolean;
    icon?: React.ReactNode;
    ariaLabel?: string;
    className?: string;
}

const triggerClass =
    'flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 w-full transition-colors focus-within:border-primary focus-within:ring-1 focus-within:ring-primary';

const pad = (n: number): string => (n < 10 ? `0${n}` : `${n}`);

const toIso = (date: Date): string =>
    `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

const fromIso = (value: string): Date | null => {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value);
    if (!match) {
        return null;
    }
    const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
    return Number.isNaN(date.getTime()) ? null : date;
};

const startOfDay = (date: Date): Date =>
    new Date(date.getFullYear(), date.getMonth(), date.getDate());

const isSameDay = (a: Date, b: Date): boolean =>
    a.getFullYear() === b.getFullYear() &&
    a.getMonth() === b.getMonth() &&
    a.getDate() === b.getDate();

export const DateSelect: React.FC<DateSelectProps> = ({
    value,
    onChange,
    min,
    max,
    closedDaysOfWeek = [],
    placeholder,
    disabled = false,
    icon,
    ariaLabel,
    className = '',
}) => {
    const { i18n } = useTranslation();
    const locale = i18n.language === 'ar' ? 'ar' : 'en';

    const [isOpen, setIsOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    const selectedDate = useMemo(() => fromIso(value), [value]);
    const minDate = useMemo(() => (min ? fromIso(min) : null), [min]);
    const maxDate = useMemo(() => (max ? fromIso(max) : null), [max]);

    const initialMonth = selectedDate ?? minDate ?? new Date();
    const [viewMonth, setViewMonth] = useState<Date>(
        new Date(initialMonth.getFullYear(), initialMonth.getMonth(), 1),
    );

    useEffect(() => {
        if (!isOpen) {
            return;
        }

        const handleClickOutside = (event: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };
        const handleEsc = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setIsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        document.addEventListener('keydown', handleEsc);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('keydown', handleEsc);
        };
    }, [isOpen]);

    useEffect(() => {
        if (isOpen && selectedDate) {
            setViewMonth(new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1));
        }
    }, [isOpen, selectedDate]);

    const monthLabel = useMemo(
        () =>
            new Intl.DateTimeFormat(locale, { month: 'long' }).format(viewMonth),
        [locale, viewMonth],
    );
    const yearLabel = `${viewMonth.getFullYear()}`;

    const weekdayLabels = useMemo(() => {
        const formatter = new Intl.DateTimeFormat(locale, { weekday: 'short' });
        const baseSunday = new Date(2024, 0, 7);
        return Array.from({ length: 7 }, (_, i) => {
            const day = new Date(baseSunday);
            day.setDate(baseSunday.getDate() + i);
            return formatter.format(day);
        });
    }, [locale]);

    const days = useMemo(() => {
        const firstOfMonth = new Date(viewMonth.getFullYear(), viewMonth.getMonth(), 1);
        const offset = firstOfMonth.getDay();
        const gridStart = new Date(firstOfMonth);
        gridStart.setDate(firstOfMonth.getDate() - offset);

        return Array.from({ length: 42 }, (_, i) => {
            const date = new Date(gridStart);
            date.setDate(gridStart.getDate() + i);
            return date;
        });
    }, [viewMonth]);

    const today = startOfDay(new Date());

    const isDisabled = (date: Date): boolean => {
        if (closedDaysOfWeek.includes(date.getDay())) {
            return true;
        }
        if (minDate && date < startOfDay(minDate)) {
            return true;
        }
        if (maxDate && date > startOfDay(maxDate)) {
            return true;
        }
        return false;
    };

    const goToMonth = (delta: number) => {
        setViewMonth((prev) => new Date(prev.getFullYear(), prev.getMonth() + delta, 1));
    };

    const handleSelect = (date: Date) => {
        if (isDisabled(date)) {
            return;
        }
        onChange(toIso(date));
        setIsOpen(false);
    };

    const triggerLabel = useMemo(() => {
        if (!selectedDate) {
            return null;
        }
        const monthName = new Intl.DateTimeFormat(locale, { month: 'short' }).format(selectedDate);
        const day = selectedDate.getDate();
        const year = selectedDate.getFullYear();
        return locale === 'ar' ? `${day} ${monthName} ${year}` : `${monthName} ${day}, ${year}`;
    }, [selectedDate, locale]);

    return (
        <div className={`relative w-full ${className}`} ref={containerRef}>
            <div
                role="button"
                tabIndex={disabled ? -1 : 0}
                aria-haspopup="dialog"
                aria-expanded={isOpen}
                aria-label={ariaLabel}
                aria-disabled={disabled}
                onClick={() => !disabled && setIsOpen((prev) => !prev)}
                onKeyDown={(e) => {
                    if (disabled) return;
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        setIsOpen((prev) => !prev);
                    }
                }}
                className={`${triggerClass} ${disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'}`}
            >
                {icon ?? <CalendarIcon className="w-4 h-4 text-gray-400 flex-shrink-0" />}
                <span
                    className={`flex-1 text-sm ${triggerLabel ? 'text-text-main' : 'text-gray-400'}`}
                >
                    {triggerLabel ?? placeholder ?? '----/--/--'}
                </span>
            </div>

            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ opacity: 0, y: -6 }}
                        animate={{ opacity: 1, y: 4 }}
                        exit={{ opacity: 0, y: -6 }}
                        transition={{ duration: 0.15 }}
                        className="absolute z-50 mt-1 w-72 bg-white border border-gray-200 rounded-lg shadow-xl p-3"
                        dir="ltr"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <button
                                type="button"
                                onClick={() => goToMonth(-1)}
                                className="p-1 rounded-md hover:bg-gray-100 text-gray-500"
                                aria-label="Previous month"
                            >
                                <ChevronLeft className="w-4 h-4" />
                            </button>
                            <div className="text-sm font-semibold text-text-main">
                                {monthLabel} {yearLabel}
                            </div>
                            <button
                                type="button"
                                onClick={() => goToMonth(1)}
                                className="p-1 rounded-md hover:bg-gray-100 text-gray-500"
                                aria-label="Next month"
                            >
                                <ChevronRight className="w-4 h-4" />
                            </button>
                        </div>

                        <div className="grid grid-cols-7 gap-1 mb-1">
                            {weekdayLabels.map((label, i) => (
                                <div
                                    key={i}
                                    className="text-center text-[10px] uppercase tracking-wide text-gray-400 py-1"
                                >
                                    {label}
                                </div>
                            ))}
                        </div>

                        <div className="grid grid-cols-7 gap-1">
                            {days.map((date, i) => {
                                const inMonth = date.getMonth() === viewMonth.getMonth();
                                const disabledDay = isDisabled(date);
                                const isToday = isSameDay(date, today);
                                const isSelected = selectedDate && isSameDay(date, selectedDate);

                                return (
                                    <button
                                        key={i}
                                        type="button"
                                        disabled={disabledDay}
                                        onClick={() => handleSelect(date)}
                                        className={[
                                            'h-9 w-9 text-sm rounded-md transition-colors',
                                            !inMonth ? 'text-gray-300' : 'text-text-main',
                                            disabledDay
                                                ? 'opacity-40 cursor-not-allowed'
                                                : 'hover:bg-primary/10',
                                            isSelected
                                                ? 'bg-primary text-white hover:bg-primary'
                                                : isToday
                                                  ? 'ring-1 ring-primary/40'
                                                  : '',
                                        ].join(' ')}
                                    >
                                        {date.getDate()}
                                    </button>
                                );
                            })}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
};
