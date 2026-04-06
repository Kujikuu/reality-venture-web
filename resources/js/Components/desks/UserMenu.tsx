import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { User, ChevronDown, LogOut } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface DesksUser {
    id: number;
    name: string;
    email: string;
}

interface UserMenuProps {
    user: DesksUser | null;
    loading: boolean;
    onLoginClick: () => void;
    onLogout: () => void;
}

export const UserMenu: React.FC<UserMenuProps> = ({ user, loading, onLoginClick, onLogout }) => {
    const { t } = useTranslation('desks');
    const [open, setOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    if (loading) {
        return (
            <div className="h-9 w-28 rounded-full bg-gray-200 animate-pulse" />
        );
    }

    if (!user) {
        return (
            <button
                onClick={onLoginClick}
                className="flex items-center gap-2 px-4 py-2 rounded-full bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors"
            >
                <User className="w-4 h-4" />
                {t('auth.loginButton')}
            </button>
        );
    }

    return (
        <div className="relative" ref={containerRef}>
            <button
                onClick={() => setOpen((prev) => !prev)}
                className="flex items-center gap-2 px-3 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 transition-colors"
            >
                <div className="w-7 h-7 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                    {user.name.charAt(0).toUpperCase()}
                </div>
                <span className="text-sm font-medium text-text-main max-w-[120px] truncate">
                    {user.name}
                </span>
                <ChevronDown className={`w-4 h-4 text-gray-400 transition-transform ${open ? 'rotate-180' : ''}`} />
            </button>

            {open && (
                <div className="absolute right-0 top-full mt-2 w-56 rounded-xl border border-gray-200 bg-white shadow-lg z-20 overflow-hidden">
                    <div className="px-4 py-3 border-b border-gray-100">
                        <p className="text-sm font-semibold text-text-main truncate">{user.name}</p>
                        <p className="text-xs text-gray-500 truncate">{user.email}</p>
                    </div>
                    <Link
                        href="/desks/bookings"
                        className="flex items-center gap-2 px-4 py-2.5 text-sm text-text-main hover:bg-gray-50 transition-colors"
                        onClick={() => setOpen(false)}
                    >
                        {t('bookings.title')}
                    </Link>
                    <button
                        onClick={() => {
                            setOpen(false);
                            onLogout();
                        }}
                        className="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors border-t border-gray-100"
                    >
                        <LogOut className="w-4 h-4" />
                        {t('auth.logout')}
                    </button>
                </div>
            )}
        </div>
    );
};
