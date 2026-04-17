import React, { useState, useRef, useEffect, useId, useCallback } from "react";
import { Check, ChevronDown, X, Search } from "lucide-react";
import { useTranslation } from "react-i18next";
import { AnimatePresence, motion } from "framer-motion";

interface SelectOption {
    value: string;
    label: string;
}

interface SelectProps {
    options: SelectOption[];
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    noResultsText?: string;
    className?: string;
    error?: string;
    label?: string;
}

export const Select: React.FC<SelectProps> = ({
    options,
    value,
    onChange,
    placeholder = "Select an option...",
    searchPlaceholder = "Search...",
    noResultsText = "No results found",
    className = "",
    error,
    label,
}) => {
    const { i18n } = useTranslation();
    const [isOpen, setIsOpen] = useState(false);
    const [searchTerm, setSearchTerm] = useState("");
    const [highlightedIndex, setHighlightedIndex] = useState(-1);
    const containerRef = useRef<HTMLDivElement>(null);
    const listRef = useRef<HTMLDivElement>(null);
    const searchInputRef = useRef<HTMLInputElement>(null);

    const generatedId = useId();
    const triggerId = `select-trigger-${generatedId}`;
    const listboxId = `select-listbox-${generatedId}`;
    const labelId = label ? `select-label-${generatedId}` : undefined;
    const errorId = error ? `select-error-${generatedId}` : undefined;

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                containerRef.current &&
                !containerRef.current.contains(event.target as Node)
            ) {
                setIsOpen(false);
            }
        };
        document.addEventListener("mousedown", handleClickOutside);
        return () => document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    useEffect(() => {
        if (isOpen) {
            searchInputRef.current?.focus();
            setHighlightedIndex(-1);
        } else {
            setSearchTerm("");
        }
    }, [isOpen]);

    const filteredOptions = options.filter((option) =>
        option.label.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const selectedOption = options.find((option) => option.value === value);

    const handleSelect = useCallback((optionValue: string) => {
        onChange(optionValue);
        setIsOpen(false);
        setSearchTerm("");
    }, [onChange]);

    const handleKeyDown = useCallback((e: React.KeyboardEvent) => {
        switch (e.key) {
            case "Enter":
            case " ":
                e.preventDefault();
                if (!isOpen) {
                    setIsOpen(true);
                } else if (highlightedIndex >= 0 && highlightedIndex < filteredOptions.length) {
                    handleSelect(filteredOptions[highlightedIndex].value);
                }
                break;
            case "Escape":
                e.preventDefault();
                setIsOpen(false);
                break;
            case "ArrowDown":
                e.preventDefault();
                if (!isOpen) {
                    setIsOpen(true);
                } else {
                    setHighlightedIndex((prev) =>
                        prev < filteredOptions.length - 1 ? prev + 1 : 0
                    );
                }
                break;
            case "ArrowUp":
                e.preventDefault();
                if (isOpen) {
                    setHighlightedIndex((prev) =>
                        prev > 0 ? prev - 1 : filteredOptions.length - 1
                    );
                }
                break;
            case "Tab":
                if (isOpen) setIsOpen(false);
                break;
        }
    }, [isOpen, highlightedIndex, filteredOptions, handleSelect]);

    useEffect(() => {
        if (highlightedIndex >= 0 && listRef.current) {
            const highlighted = listRef.current.querySelector(`[data-index="${highlightedIndex}"]`);
            highlighted?.scrollIntoView({ block: "nearest" });
        }
    }, [highlightedIndex]);

    return (
        <div className={`relative w-full ${className}`} ref={containerRef}>
            {label && (
                <label id={labelId} className="text-xs font-bold uppercase tracking-wide text-gray-600 mb-2 block">
                    {label}
                </label>
            )}
            <button
                type="button"
                id={triggerId}
                role="combobox"
                aria-expanded={isOpen}
                aria-haspopup="listbox"
                aria-controls={isOpen ? listboxId : undefined}
                aria-labelledby={labelId}
                aria-invalid={error ? true : undefined}
                aria-describedby={errorId}
                onClick={() => setIsOpen(!isOpen)}
                onKeyDown={handleKeyDown}
                className={`h-14 w-full px-6 rounded-lg bg-gray-50 border ${
                    error ? "border-red-500" : "border-gray-200"
                } text-gray-900 cursor-pointer flex items-center justify-between transition-all focus:ring-1 focus:ring-primary focus:border-primary focus:outline-none`}
            >
                <span className={`text-sm ${!selectedOption ? "text-gray-400" : "text-gray-900"}`}>
                    {selectedOption ? selectedOption.label : placeholder}
                </span>
                <ChevronDown
                    className={`w-4 h-4 text-gray-500 transition-transform duration-200 ${
                        isOpen ? "rotate-180" : ""
                    }`}
                    aria-hidden="true"
                />
            </button>

            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 4 }}
                        exit={{ opacity: 0, y: -10 }}
                        transition={{ duration: 0.2 }}
                        className="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden"
                    >
                        <div className="p-2 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
                            <Search className="w-4 h-4 text-gray-400" aria-hidden="true" />
                            <input
                                ref={searchInputRef}
                                type="text"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                onKeyDown={handleKeyDown}
                                placeholder={searchPlaceholder}
                                className="w-full bg-transparent border-none focus:ring-0 text-sm py-1 outline-none"
                                aria-label={searchPlaceholder}
                            />
                            {searchTerm && (
                                <button
                                    type="button"
                                    onClick={() => setSearchTerm("")}
                                    className="p-1.5 hover:bg-gray-200 rounded-full"
                                    aria-label="Clear search"
                                >
                                    <X className="w-3 h-3 text-gray-400" aria-hidden="true" />
                                </button>
                            )}
                        </div>

                        <div
                            ref={listRef}
                            role="listbox"
                            id={listboxId}
                            aria-labelledby={labelId}
                            className="max-h-60 overflow-y-auto select-scrollbar"
                            onWheel={(e) => e.stopPropagation()}
                        >
                            {filteredOptions.length > 0 ? (
                                <div className="p-1">
                                    {filteredOptions.map((option, index) => {
                                        const isSelected = option.value === value;
                                        const isHighlighted = index === highlightedIndex;
                                        return (
                                            <div
                                                key={option.value}
                                                role="option"
                                                aria-selected={isSelected}
                                                data-index={index}
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    handleSelect(option.value);
                                                }}
                                                className={`flex items-center justify-between px-3 py-2.5 rounded-md cursor-pointer transition-colors ${
                                                    isSelected
                                                        ? "bg-primary-50 text-primary-900"
                                                        : isHighlighted
                                                            ? "bg-gray-100 text-gray-900"
                                                            : "hover:bg-gray-50 text-gray-700"
                                                }`}
                                            >
                                                <span className="text-sm font-medium">
                                                    {option.label}
                                                </span>
                                                {isSelected && (
                                                    <Check className="w-4 h-4 text-primary" aria-hidden="true" />
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            ) : (
                                <div className="p-8 text-center text-sm text-gray-500">
                                    {noResultsText}
                                </div>
                            )}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>

            {error && <p id={errorId} className="mt-1.5 text-xs text-red-500 font-medium ps-1" role="alert">{error}</p>}
        </div>
    );
};
