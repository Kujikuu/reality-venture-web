import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { X } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface CancelModalProps {
    show: boolean;
    onClose: () => void;
    onConfirm: () => void;
    loading: boolean;
}

export const CancelModal: React.FC<CancelModalProps> = ({ show, onClose, onConfirm, loading }) => {
    const { t } = useTranslation('desks');

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
                        <div className="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                            {/* Header */}
                            <div className="flex items-center justify-between px-6 pt-5 pb-0">
                                <span />
                                <button
                                    onClick={onClose}
                                    className="text-gray-400 hover:text-gray-600 transition-colors"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            {/* Body */}
                            <div className="px-6 py-6 flex flex-col items-center text-center gap-4">
                                <div className="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                    <X className="w-6 h-6 text-red-600" />
                                </div>
                                <p className="text-text-main font-medium">{t('bookings.cancelConfirm')}</p>
                            </div>

                            {/* Actions */}
                            <div className="flex gap-3 px-6 pb-6">
                                <button
                                    onClick={onClose}
                                    disabled={loading}
                                    className="flex-1 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {t('bookings.cancelKeep')}
                                </button>
                                <button
                                    onClick={onConfirm}
                                    disabled={loading}
                                    className="flex-1 py-2.5 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {loading ? '...' : t('bookings.cancelButton')}
                                </button>
                            </div>
                        </div>
                    </motion.div>
                </>
            )}
        </AnimatePresence>
    );
};
