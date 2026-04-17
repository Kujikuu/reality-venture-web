import React, { useId } from "react";

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
    id: externalId,
    ...props
}) => {
    const generatedId = useId();
    const inputId = externalId || generatedId;
    const errorId = error ? `${inputId}-error` : undefined;

    return (
        <div className={`flex flex-col gap-2 w-full ${containerClassName}`}>
            {label && (
                <label
                    htmlFor={inputId}
                    className="text-xs font-bold uppercase tracking-wide text-gray-600"
                >
                    {label}
                </label>
            )}
            <input
                id={inputId}
                className={`w-full h-14 px-6 bg-gray-50 border rounded-lg text-gray-900 transition-all outline-none ${
                    error
                        ? "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        : "border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                } ${className}`}
                aria-invalid={error ? true : undefined}
                aria-describedby={errorId}
                {...props}
            />
            {error && (
                <p id={errorId} className="text-red-500 text-xs mt-1" role="alert">
                    {error}
                </p>
            )}
        </div>
    );
};
