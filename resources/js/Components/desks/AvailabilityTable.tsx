import React from 'react';
import { useTranslation } from 'react-i18next';

interface Slot {
    day_of_week: number;
    open_from: string;
    open_to: string;
    is_closed: boolean;
}

interface AvailabilityTableProps {
    availability: Slot[];
}

export const AvailabilityTable: React.FC<AvailabilityTableProps> = ({ availability }) => {
    const { t } = useTranslation('desks');
    const dayNames = t('detail.days', { returnObjects: true }) as string[];

    return (
        <div>
            <h3 className="text-base font-semibold text-text-main mb-3">{t('detail.availability')}</h3>
            <div className="rounded-xl overflow-hidden border border-gray-200">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-surface text-gray-500">
                            <th className="text-left px-4 py-3 font-medium">{t('booking.date')}</th>
                            <th className="text-left px-4 py-3 font-medium">{t('booking.startTime')}</th>
                            <th className="text-left px-4 py-3 font-medium">{t('booking.endTime')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {availability.map((slot) => (
                            <tr
                                key={slot.day_of_week}
                                className="border-t border-gray-100 even:bg-gray-50/50"
                            >
                                <td className="px-4 py-3 text-text-main font-medium">
                                    {dayNames[slot.day_of_week] ?? `Day ${slot.day_of_week}`}
                                </td>
                                {slot.is_closed ? (
                                    <td colSpan={2} className="px-4 py-3 text-gray-400 italic">
                                        {t('detail.closed')}
                                    </td>
                                ) : (
                                    <>
                                        <td className="px-4 py-3 text-gray-600">{slot.open_from}</td>
                                        <td className="px-4 py-3 text-gray-600">{slot.open_to}</td>
                                    </>
                                )}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};
