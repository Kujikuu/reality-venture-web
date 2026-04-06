import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { X, Mail } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface AuthErrors {
    [key: string]: string[];
}

interface AuthModalProps {
    show: boolean;
    onClose: () => void;
    onLogin: (email: string, password: string) => Promise<boolean>;
    onRegister: (data: {
        name: string;
        email: string;
        phone: string;
        password: string;
        password_confirmation: string;
    }) => Promise<boolean>;
    authError: string;
    authErrors: AuthErrors;
    authLoading: boolean;
    onClearErrors: () => void;
}

const fieldClass =
    'flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary';

const inputClass = 'flex-1 outline-none text-sm text-text-main bg-transparent placeholder:text-gray-400';

export const AuthModal: React.FC<AuthModalProps> = ({
    show,
    onClose,
    onLogin,
    onRegister,
    authError,
    authErrors,
    authLoading,
    onClearErrors,
}) => {
    const { t } = useTranslation('desks');
    const [tab, setTab] = useState<'login' | 'register'>('login');

    // Login form state
    const [loginEmail, setLoginEmail] = useState('');
    const [loginPassword, setLoginPassword] = useState('');

    // Register form state
    const [regName, setRegName] = useState('');
    const [regEmail, setRegEmail] = useState('');
    const [regPhone, setRegPhone] = useState('');
    const [regPassword, setRegPassword] = useState('');
    const [regPasswordConfirm, setRegPasswordConfirm] = useState('');

    const handleTabChange = (newTab: 'login' | 'register') => {
        setTab(newTab);
        onClearErrors();
    };

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        await onLogin(loginEmail, loginPassword);
    };

    const handleRegister = async (e: React.FormEvent) => {
        e.preventDefault();
        await onRegister({
            name: regName,
            email: regEmail,
            phone: regPhone,
            password: regPassword,
            password_confirmation: regPasswordConfirm,
        });
    };

    const FieldError: React.FC<{ field: string }> = ({ field }) => {
        const errors = authErrors[field];
        if (!errors || errors.length === 0) return null;
        return <p className="text-red-500 text-xs mt-1">{errors[0]}</p>;
    };

    return (
        <AnimatePresence>
            {show && (
                <>
                    {/* Backdrop */}
                    <motion.div
                        key="backdrop"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="fixed inset-0 bg-black/50 z-40"
                        onClick={onClose}
                    />

                    {/* Modal */}
                    <motion.div
                        key="modal"
                        initial={{ opacity: 0, scale: 0.95, y: 20 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.95, y: 20 }}
                        transition={{ type: 'spring', stiffness: 300, damping: 25 }}
                        className="fixed inset-0 z-50 flex items-center justify-center p-4"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                            {/* Header */}
                            <div className="flex items-center justify-between px-6 pt-5 pb-4">
                                <h2 className="text-lg font-semibold text-text-main">
                                    {tab === 'login' ? t('auth.loginTab') : t('auth.registerTab')}
                                </h2>
                                <button
                                    onClick={onClose}
                                    className="text-gray-400 hover:text-gray-600 transition-colors"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            {/* Tabs */}
                            <div className="flex border-b border-gray-200 px-6">
                                {(['login', 'register'] as const).map((t_key) => (
                                    <button
                                        key={t_key}
                                        onClick={() => handleTabChange(t_key)}
                                        className={`pb-3 px-4 text-sm font-medium transition-colors border-b-2 -mb-px ${
                                            tab === t_key
                                                ? 'border-primary text-primary'
                                                : 'border-transparent text-gray-500 hover:text-gray-700'
                                        }`}
                                    >
                                        {t_key === 'login' ? t('auth.loginTab') : t('auth.registerTab')}
                                    </button>
                                ))}
                            </div>

                            {/* Form */}
                            <div className="px-6 py-5">
                                {authError && (
                                    <div className="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-2.5 text-sm text-red-600">
                                        {authError}
                                    </div>
                                )}

                                {tab === 'login' ? (
                                    <form onSubmit={handleLogin} className="space-y-4">
                                        <div>
                                            <div className={fieldClass}>
                                                <Mail className="w-4 h-4 text-gray-400 flex-shrink-0" />
                                                <input
                                                    type="email"
                                                    placeholder={t('auth.email')}
                                                    value={loginEmail}
                                                    onChange={(e) => setLoginEmail(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="email" />
                                        </div>
                                        <div>
                                            <div className={fieldClass}>
                                                <input
                                                    type="password"
                                                    placeholder={t('auth.password')}
                                                    value={loginPassword}
                                                    onChange={(e) => setLoginPassword(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="password" />
                                        </div>
                                        <button
                                            type="submit"
                                            disabled={authLoading}
                                            className="w-full bg-primary text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {authLoading ? '...' : t('auth.loginButton')}
                                        </button>
                                    </form>
                                ) : (
                                    <form onSubmit={handleRegister} className="space-y-4">
                                        <div>
                                            <div className={fieldClass}>
                                                <input
                                                    type="text"
                                                    placeholder={t('auth.name')}
                                                    value={regName}
                                                    onChange={(e) => setRegName(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="name" />
                                        </div>
                                        <div>
                                            <div className={fieldClass}>
                                                <Mail className="w-4 h-4 text-gray-400 flex-shrink-0" />
                                                <input
                                                    type="email"
                                                    placeholder={t('auth.email')}
                                                    value={regEmail}
                                                    onChange={(e) => setRegEmail(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="email" />
                                        </div>
                                        <div>
                                            <div className={fieldClass}>
                                                <input
                                                    type="tel"
                                                    placeholder={t('auth.phone')}
                                                    value={regPhone}
                                                    onChange={(e) => setRegPhone(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="phone" />
                                        </div>
                                        <div>
                                            <div className={fieldClass}>
                                                <input
                                                    type="password"
                                                    placeholder={t('auth.password')}
                                                    value={regPassword}
                                                    onChange={(e) => setRegPassword(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="password" />
                                        </div>
                                        <div>
                                            <div className={fieldClass}>
                                                <input
                                                    type="password"
                                                    placeholder={t('auth.passwordConfirmation')}
                                                    value={regPasswordConfirm}
                                                    onChange={(e) => setRegPasswordConfirm(e.target.value)}
                                                    required
                                                    className={inputClass}
                                                />
                                            </div>
                                            <FieldError field="password_confirmation" />
                                        </div>
                                        <button
                                            type="submit"
                                            disabled={authLoading}
                                            className="w-full bg-primary text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {authLoading ? '...' : t('auth.registerButton')}
                                        </button>
                                    </form>
                                )}
                            </div>
                        </div>
                    </motion.div>
                </>
            )}
        </AnimatePresence>
    );
};
