import React, { useEffect, useRef, useState } from 'react';
import { Check, ChevronDown, Clock } from 'lucide-react';
import { AnimatePresence, motion } from 'framer-motion';
import { formatTimeLabel } from '../../lib/time';

interface TimeSelectProps {
    value: string;
    onChange: (value: string) => void;
    options: string[];
    format?: '12h' | '24h';
    placeholder?: string;
    disabled?: boolean;
    icon?: React.ReactNode;
    ariaLabel?: string;
    className?: string;
}

const triggerClass =
    'flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 w-full transition-colors focus-within:border-primary focus-within:ring-1 focus-within:ring-primary';

export const TimeSelect: React.FC<TimeSelectProps> = ({
    value,
    onChange,
    options,
    format = '24h',
    placeholder,
    disabled = false,
    icon,
    ariaLabel,
    className = '',
}) => {
    const [isOpen, setIsOpen] = useState(false);
    const [activeIndex, setActiveIndex] = useState<number>(-1);
    const containerRef = useRef<HTMLDivElement>(null);
    const listRef = useRef<HTMLDivElement>(null);

    const isEmpty = options.length === 0 || disabled;

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
        if (!isOpen) {
            return;
        }

        const initialIndex = options.findIndex((option) => option === value);
        setActiveIndex(initialIndex >= 0 ? initialIndex : 0);

        const frame = requestAnimationFrame(() => {
            const list = listRef.current;
            if (!list || initialIndex < 0) {
                return;
            }
            const item = list.children[initialIndex] as HTMLElement | undefined;
            item?.scrollIntoView({ block: 'nearest' });
        });

        return () => cancelAnimationFrame(frame);
    }, [isOpen, options, value]);

    const toggleOpen = () => {
        if (isEmpty) {
            return;
        }
        setIsOpen((prev) => !prev);
    };

    const handleSelect = (option: string) => {
        onChange(option);
        setIsOpen(false);
    };

    const handleKeyDown = (event: React.KeyboardEvent<HTMLDivElement>) => {
        if (isEmpty) {
            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if (!isOpen) {
                setIsOpen(true);
                return;
            }
            if (activeIndex >= 0 && activeIndex < options.length) {
                handleSelect(options[activeIndex]);
            }
            return;
        }

        if (!isOpen) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActiveIndex((prev) => Math.min(options.length - 1, prev + 1));
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActiveIndex((prev) => Math.max(0, prev - 1));
        }
    };

    const selectedLabel = value && options.includes(value) ? formatTimeLabel(value, format) : null;

    return (
        <div className={`relative w-full ${className}`} ref={containerRef}>
            <div
                role="button"
                tabIndex={isEmpty ? -1 : 0}
                aria-haspopup="listbox"
                aria-expanded={isOpen}
                aria-label={ariaLabel}
                aria-disabled={isEmpty}
                onClick={toggleOpen}
                onKeyDown={handleKeyDown}
                className={`${triggerClass} ${isEmpty ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'}`}
                dir="ltr"
            >
                {icon ?? <Clock className="w-4 h-4 text-gray-400 flex-shrink-0" />}
                <span
                    className={`flex-1 text-sm ${selectedLabel ? 'text-text-main' : 'text-gray-400'}`}
                >
                    {selectedLabel ?? placeholder ?? '--:--'}
                </span>
                <ChevronDown
                    className={`w-4 h-4 text-gray-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}
                />
            </div>

            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ opacity: 0, y: -6 }}
                        animate={{ opacity: 1, y: 4 }}
                        exit={{ opacity: 0, y: -6 }}
                        transition={{ duration: 0.15 }}
                        className="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden"
                        dir="ltr"
                    >
                        <div
                            ref={listRef}
                            role="listbox"
                            className="max-h-56 overflow-y-auto p-1"
                            onWheel={(e) => e.stopPropagation()}
                        >
                            {options.map((option, index) => {
                                const isSelected = option === value;
                                const isActive = index === activeIndex;
                                return (
                                    <div
                                        key={option}
                                        role="option"
                                        aria-selected={isSelected}
                                        onMouseEnter={() => setActiveIndex(index)}
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            handleSelect(option);
                                        }}
                                        className={`flex items-center justify-between px-3 py-2 rounded-md cursor-pointer text-sm transition-colors ${
                                            isSelected
                                                ? 'bg-primary/10 text-primary font-medium'
                                                : isActive
                                                  ? 'bg-gray-100 text-text-main'
                                                  : 'text-gray-700 hover:bg-gray-50'
                                        }`}
                                    >
                                        <span>{formatTimeLabel(option, format)}</span>
                                        {isSelected && <Check className="w-4 h-4 text-primary" />}
                                    </div>
                                );
                            })}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
};
