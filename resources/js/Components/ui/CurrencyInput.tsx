import React from "react";
import { SarIcon } from "./SarIcon";

interface CurrencyInputProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, "type"> {
    label?: string;
    error?: string;
    containerClassName?: string;
}

export const CurrencyInput: React.FC<CurrencyInputProps> = ({
    label,
    error,
    containerClassName = "",
    className = "",
    ...props
}) => {
    return (
        <div className={`flex flex-col gap-2 w-full ${containerClassName}`}>
            {label && (
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">
                    {label}
                </label>
            )}
            <div className="relative">
                <input
                    type="number"
                    className={`w-full h-14 px-6 pe-16 bg-gray-50 border rounded-lg text-gray-900 transition-all outline-none ${
                        error
                            ? "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                            : "border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                    } ${className}`}
                    {...props}
                />
                <span className="absolute end-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">
                    <SarIcon />
                </span>
            </div>
            {error && (
                <p className="text-red-500 text-xs mt-1">{error}</p>
            )}
        </div>
    );
};