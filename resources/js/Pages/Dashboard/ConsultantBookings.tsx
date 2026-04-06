import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LayoutDashboard, Calendar, DollarSign, UserCircle, Wallet } from 'lucide-react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { BookingItem, PaginatedData } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';

interface Props {
  bookings: PaginatedData<BookingItem>;
}

const statusColors: Record<string, string> = {
  awaiting_payment: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
  completed: 'bg-blue-100 text-blue-800',
  no_show: 'bg-gray-100 text-gray-800',
};

export default function ConsultantBookings({ bookings }: Props) {
  const { t } = useTranslation('dashboard');
  const { t: tBookings } = useTranslation('bookings');

  const sidebarLinks = [
    { href: '/consultant/dashboard', icon: LayoutDashboard, label: t('consultant.overview') },
    { href: '/consultant/bookings', icon: Calendar, label: t('consultant.bookings') },
    { href: '/consultant/earnings', icon: DollarSign, label: t('consultant.earnings') },
    { href: '/consultant/wallet', icon: Wallet, label: t('consultant.wallet') },
    { href: '/consultant/profile/edit', icon: UserCircle, label: t('consultant.profileEdit') },
  ];

  return (
    <>
      <SEO />
      <DashboardLayout links={sidebarLinks} title={t('consultant.bookings')}>
        <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
          {bookings.data.length === 0 ? (
            <div className="text-center py-16">
              <Calendar className="w-10 h-10 text-gray-300 mx-auto mb-3" />
              <p className="text-sm text-gray-400">{t('consultant.noBookings')}</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th className="px-6 py-3 text-start text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.client')}</th>
                    <th className="px-6 py-3 text-start text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.date')}</th>
                    <th className="px-6 py-3 text-start text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.duration')}</th>
                    <th className="px-6 py-3 text-start text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.status')}</th>
                    <th className="px-6 py-3 text-end text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.gross')}</th>
                    <th className="px-6 py-3 text-end text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.platformFee')}</th>
                    <th className="px-6 py-3 text-end text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.netEarnings')}</th>
                    <th className="px-6 py-3"></th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                  {bookings.data.map((booking) => (
                    <tr key={booking.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 text-sm font-medium text-gray-900">{booking.client?.name}</td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {new Date(booking.start_at).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">{booking.duration_minutes} {tBookings('show.minutes')}</td>
                      <td className="px-6 py-4">
                        <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold ${statusColors[booking.status]}`}>
                          {tBookings(`status.${booking.status}`)}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-end font-medium text-gray-900">{booking.total_amount} SAR</td>
                      <td className="px-6 py-4 text-sm text-end text-red-500">-{booking.commission_amount} SAR</td>
                      <td className="px-6 py-4 text-sm text-end font-bold text-green-600">{booking.consultant_amount} SAR</td>
                      <td className="px-6 py-4">
                        {booking.status === 'confirmed' && (
                          <Link
                            href={`/consultant/bookings/${booking.id}/complete`}
                            method="post"
                            as="button"
                            className="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md hover:bg-green-100 transition-colors"
                          >
                            {t('consultant.complete')}
                          </Link>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {/* Pagination */}
          {bookings.last_page > 1 && (
            <div className="flex justify-center gap-2 py-4 border-t border-gray-100">
              {Array.from({ length: bookings.last_page }, (_, i) => i + 1).map((page) => (
                <Link
                  key={page}
                  href={`/consultant/bookings?page=${page}`}
                  className={`w-8 h-8 flex items-center justify-center rounded text-sm font-medium ${
                    page === bookings.current_page ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {page}
                </Link>
              ))}
            </div>
          )}
        </div>
      </DashboardLayout>
    </>
  );
}
