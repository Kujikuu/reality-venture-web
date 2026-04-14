import React from "react";

interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
    label?: string;
    error?: string;
    containerClassName?: string;
}

export const Textarea: React.FC<TextareaProps> = ({
    label,
    error,
    containerClassName = "",
    className = "",
    children,
    ...props
}) => {
    return (
        <div className={`flex flex-col gap-2 w-full ${containerClassName}`}>
            {label && (
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">
                    {label}
                </label>
            )}
            <textarea
                className={`w-full p-6 bg-gray-50 border rounded-lg text-gray-900 transition-all outline-none resize-none ${
                    error
                        ? "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        : "border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                } ${className}`}
                {...props}
            />
            {children}
            {error && (
                <p className="text-red-500 text-xs mt-1">
                    {error}
                </p>
            )}
        </div>
    );
};