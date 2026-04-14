import React from "react";
import { Check } from "lucide-react";

interface CheckboxProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, "type"> {
    label: string | React.ReactNode;
    error?: string;
}

export const Checkbox: React.FC<CheckboxProps> = ({
    label,
    error,
    className = "",
    id,
    ...props
}) => {
    const uniqueId = id || `checkbox-${Math.random().toString(36).substr(2, 9)}`;

    return (
        <div className="flex flex-col gap-1.5">
            <label
                htmlFor={uniqueId}
                className={`flex items-center gap-2.5 cursor-pointer group select-none ${className}`}
            >
                <div className="relative flex items-center justify-center">
                    <input
                        id={uniqueId}
                        type="checkbox"
                        className="sr-only peer"
                        {...props}
                    />
                    <div className={`w-5 h-5 border-2 rounded transition-all duration-200 flex items-center justify-center ${
                        props.checked
                            ? "bg-primary border-primary"
                            : "border-gray-200 bg-white group-hover:border-primary/50"
                    } peer-focus-visible:ring-2 peer-focus-visible:ring-primary/30`}>
                        {props.checked && <Check className="w-3.5 h-3.5 text-white stroke-[3px]" />}
                    </div>
                </div>
                <span className={`text-sm font-medium transition-colors ${
                    props.checked ? "text-primary-900" : "text-gray-700"
                }`}>
                    {label}
                </span>
            </label>
            {error && <p className="text-red-500 text-xs ps-1 mt-0.5">{error}</p>}
        </div>
    );
};
