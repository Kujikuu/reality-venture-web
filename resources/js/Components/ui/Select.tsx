import React, { useState, useRef, useEffect } from "react";
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
    const { t, i18n } = useTranslation();
    const [isOpen, setIsOpen] = useState(false);
    const [searchTerm, setSearchTerm] = useState("");
    const containerRef = useRef<HTMLDivElement>(null);
    const isRtl = i18n.language === "ar";

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

    const handleSelect = (optionValue: string) => {
        onChange(optionValue);
        setIsOpen(false);
        setSearchTerm("");
    };

    const filteredOptions = options.filter((option) =>
        option.label.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const selectedOption = options.find((option) => option.value === value);

    return (
        <div className={`relative w-full ${className}`} ref={containerRef}>
            {label && (
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">
                    {label}
                </label>
            )}
            <div
                onClick={() => setIsOpen(!isOpen)}
                className={`h-14 w-full px-6 rounded-lg bg-gray-50 border ${
                    error ? "border-red-500" : "border-gray-200"
                } text-gray-900 cursor-pointer flex items-center justify-between transition-all focus-within:ring-1 focus-within:ring-primary focus-within:border-primary`}
            >
                <span className={`text-sm ${!selectedOption ? "text-black/50" : "text-black"}`}>
                    {selectedOption ? selectedOption.label : placeholder}
                </span>
                <ChevronDown
                    className={`w-4 h-4 text-gray-500 transition-transform duration-200 ${
                        isOpen ? "rotate-180" : ""
                    }`}
                />
            </div>

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
                            <Search className="w-4 h-4 text-gray-400" />
                            <input
                                type="text"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                onClick={(e) => e.stopPropagation()}
                                placeholder={searchPlaceholder}
                                className="w-full bg-transparent border-none focus:ring-0 text-sm py-1 outline-none"
                            />
                            {searchTerm && (
                                <button
                                    onClick={() => setSearchTerm("")}
                                    className="p-1 hover:bg-gray-200 rounded-full"
                                >
                                    <X className="w-3 h-3 text-gray-400" />
                                </button>
                            )}
                        </div>

                        <div
                            className="max-h-60 overflow-y-auto custom-scrollbar"
                            onWheel={(e) => e.stopPropagation()}
                        >
                            {filteredOptions.length > 0 ? (
                                <div className="p-1">
                                    {filteredOptions.map((option) => {
                                        const isSelected = option.value === value;
                                        return (
                                            <div
                                                key={option.value}
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    handleSelect(option.value);
                                                }}
                                                className={`flex items-center justify-between px-3 py-2.5 rounded-md cursor-pointer transition-colors ${
                                                    isSelected
                                                        ? "bg-primary-50 text-primary-900"
                                                        : "hover:bg-gray-50 text-gray-700"
                                                }`}
                                            >
                                                <span className="text-sm font-medium">
                                                    {option.label}
                                                </span>
                                                {isSelected && (
                                                    <Check className="w-4 h-4 text-primary" />
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

            {error && <p className="mt-1.5 text-xs text-red-500 font-medium ps-1">{error}</p>}

            <style dangerouslySetInnerHTML={{ __html: `
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #e5e7eb;
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #d1d5db;
                }
            `}} />
        </div>
    );
};
