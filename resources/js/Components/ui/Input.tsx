import React from "react";

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    containerClassName?: string;
}

export const Input: React.FC<InputProps> = ({
    label,
    error,
    containerClassName = "",
    className = "",
    ...props
}) => {
    return (
        <div className={`flex flex-col gap-1.5 w-full ${containerClassName}`}>
            {label && (
                <label className="text-sm font-medium text-gray-700 ps-1">
                    {label}
                </label>
            )}
            <input
                className={`w-full px-4 py-3 rounded-md bg-white border ${
                    error ? "border-red-500" : "border-gray-500"
                } text-black placeholder-black/50 focus:border-black/40 focus:ring-1 focus:ring-primary outline-none text-sm transition-all ${className}`}
                {...props}
            />
            {error && (
                <p className="text-red-500 text-sm mt-0.5 ps-1">
                    {error}
                </p>
            )}
        </div>
    );
};
