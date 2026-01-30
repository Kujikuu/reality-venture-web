import React from 'react';
import { ArrowRight } from 'lucide-react';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'outline' | 'ghost' | 'white';
  withArrow?: boolean;
}

export const Button: React.FC<ButtonProps> = ({
  children,
  variant = 'primary',
  withArrow = false,
  className = '',
  ...props
}) => {
  const baseStyles = "h-14 px-10 text-base font-bold tracking-tight rounded-md transition-all duration-300 flex items-center justify-center gap-2 active:scale-95 relative overflow-hidden";

  const variants = {
    primary: "bg-primary text-white hover:bg-primary-800",
    outline: "border border-secondary text-secondary bg-white hover:bg-secondary hover:border-secondary hover:text-white",
    ghost: "hover:text-secondary hover:bg-secondary/5 p-2 h-auto text-gray-700",
    white: "bg-white text-black hover:bg-gray-50"
  };

  return (
    <button
      className={`${baseStyles} ${variants[variant]} ${className}`}
      {...props}
    >
      {children}
      {withArrow && <ArrowRight className="w-5 h-5 rtl:-scale-x-100" />}
    </button>
  );
};