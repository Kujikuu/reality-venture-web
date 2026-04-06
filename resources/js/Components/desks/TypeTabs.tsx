import React from 'react';
import { useTranslation } from 'react-i18next';

const TAB_KEYS = ['all', 'desk', 'meeting_room', 'private_office', 'event_space', 'studio', 'virtual_office'] as const;

interface TypeTabsProps {
    activeType: string;
    onTypeChange: (type: string) => void;
}

export const TypeTabs: React.FC<TypeTabsProps> = ({ activeType, onTypeChange }) => {
    const { t } = useTranslation('desks');

    return (
        <div className="flex gap-2 flex-wrap">
            {TAB_KEYS.map((key) => (
                <button
                    key={key}
                    onClick={() => onTypeChange(key)}
                    className={`px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap ${
                        activeType === key
                            ? 'bg-primary text-white'
                            : 'bg-surface text-gray-700 hover:bg-gray-200'
                    }`}
                >
                    {t(`tabs.${key}`)}
                </button>
            ))}
        </div>
    );
};
