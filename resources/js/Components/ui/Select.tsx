import React from 'react';
import { ChevronDown } from 'lucide-react';

interface SelectOption {
  value: string;
  label: string;
}

interface SelectProps extends Omit<React.SelectHTMLAttributes<HTMLSelectElement>, 'children'> {
  options: SelectOption[];
  placeholder?: string;
}

export const Select: React.FC<SelectProps> = ({
  options,
  placeholder,
  className = '',
  ...props
}) => {
  return (
    <div className="relative">
      <select
        className={`w-full h-14 px-6 pr-12 bg-gray-50 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all rounded-lg text-gray-900 appearance-none cursor-pointer ${className}`}
        {...props}
      >
        {placeholder && (
          <option value="" disabled>
            {placeholder}
          </option>
        )}
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
      <ChevronDown className="w-5 h-5 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" />
    </div>
  );
};
