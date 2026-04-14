import React from "react";
import { Upload, X } from "lucide-react";

interface FileUploadProps {
    label?: string;
    value: File | null;
    onChange: (file: File | null) => void;
    accept?: string;
    placeholder?: string;
    helpText?: string;
    error?: string;
    containerClassName?: string;
}

export const FileUpload: React.FC<FileUploadProps> = ({
    label,
    value,
    onChange,
    accept = ".pdf,.jpg,.jpeg,.png",
    placeholder,
    helpText,
    error,
    containerClassName = "",
}) => {
    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        onChange(e.target.files?.[0] ?? null);
    };

    return (
        <div className={`flex flex-col gap-2 w-full ${containerClassName}`}>
            {label && (
                <label className="text-xs font-bold uppercase tracking-wide text-gray-500">
                    {label}
                </label>
            )}
            {value ? (
                <div className="flex items-center gap-3 p-4 bg-primary-50 border border-primary/20 rounded-lg">
                    <Upload className="w-5 h-5 text-primary shrink-0" />
                    <span className="text-sm text-gray-700 truncate flex-1">
                        {value.name}
                    </span>
                    <span className="text-xs text-gray-400 shrink-0">
                        {(value.size / 1024 / 1024).toFixed(1)} MB
                    </span>
                    <button
                        type="button"
                        onClick={() => onChange(null)}
                        className="p-1 rounded-full hover:bg-primary/10 transition-colors"
                    >
                        <X className="w-4 h-4 text-gray-500" />
                    </button>
                </div>
            ) : (
                <label className="flex flex-col items-center justify-center gap-2 p-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg cursor-pointer hover:border-primary/40 hover:bg-gray-50/80 transition-all">
                    <Upload className="w-8 h-8 text-gray-300" />
                    {placeholder && (
                        <span className="text-sm font-semibold text-primary">
                            {placeholder}
                        </span>
                    )}
                    {helpText && (
                        <span className="text-xs text-gray-400">{helpText}</span>
                    )}
                    <input
                        type="file"
                        accept={accept}
                        onChange={handleChange}
                        className="hidden"
                    />
                </label>
            )}
            {error && <p className="text-red-500 text-xs mt-1">{error}</p>}
        </div>
    );
};