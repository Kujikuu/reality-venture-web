import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LayoutDashboard, Calendar, DollarSign, UserCircle, TrendingUp, ArrowUpRight, ArrowDownRight, Wallet } from 'lucide-react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { BookingItem, PaginatedData } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';

interface Props {
  bookings: PaginatedData<BookingItem>;
  totals: {
    gross: number;
    fees: number;
    net: number;
  };
}

const statusColors: Record<string, string> = {
  confirmed: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
  completed: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
};

export default function ConsultantEarnings({ bookings, totals }: Props) {
  const { t } = useTranslation('dashboard');
  const { t: tBookings } = useTranslation('bookings');

  const sidebarLinks = [
    { href: '/consultant/dashboard', icon: LayoutDashboard, label: t('consultant.overview') },
    { href: '/consultant/bookings', icon: Calendar, label: t('consultant.bookings') },
    { href: '/consultant/earnings', icon: DollarSign, label: t('consultant.earnings') },
    { href: '/consultant/wallet', icon: Wallet, label: t('consultant.wallet') },
    { href: '/consultant/profile/edit', icon: UserCircle, label: t('consultant.profileEdit') },
  ];

  const formatDate = (iso: string) => {
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
  };

  const formatTime = (iso: string) => {
    const d = new Date(iso);
    return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
  };

  return (
    <>
      <SEO />
      <DashboardLayout links={sidebarLinks} title={t('consultant.earningsTitle')}>

        {/* Summary Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.totalGross')}</span>
              <span className="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                <DollarSign className="w-4 h-4 text-gray-400" />
              </span>
            </div>
            <div className="text-2xl font-bold text-gray-900">{Number(totals.gross).toLocaleString()} <span className="text-sm font-medium text-gray-400">SAR</span></div>
          </div>

          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.totalFees')}</span>
              <span className="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <ArrowDownRight className="w-4 h-4 text-red-400" />
              </span>
            </div>
            <div className="text-2xl font-bold text-red-500">−{Number(totals.fees).toLocaleString()} <span className="text-sm font-medium text-red-300">SAR</span></div>
          </div>

          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.totalNet')}</span>
              <span className="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                <ArrowUpRight className="w-4 h-4 text-emerald-500" />
              </span>
            </div>
            <div className="text-2xl font-bold text-emerald-600">{Number(totals.net).toLocaleString()} <span className="text-sm font-medium text-emerald-400">SAR</span></div>
          </div>
        </div>

        {/* Bookings Table */}
        <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 className="font-bold text-gray-900">{t('consultant.earningsSummary')}</h2>
            <span className="text-xs text-gray-400 font-medium">{bookings.total} {t('consultant.bookings').toLowerCase()}</span>
          </div>

          {bookings.data.length === 0 ? (
            <div className="text-center py-16">
              <DollarSign className="w-10 h-10 text-gray-300 mx-auto mb-3" />
              <p className="text-sm text-gray-400">{t('consultant.noEarnings')}</p>
            </div>
          ) : (
            <>
              {/* Desktop Table */}
              <div className="hidden md:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.date')}</th>
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.client')}</th>
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.duration')}</th>
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.status')}</th>
                      <th className="text-end px-6 py-3 font-semibold">{t('consultant.gross')}</th>
                      <th className="text-end px-6 py-3 font-semibold">{t('consultant.platformFee')}</th>
                      <th className="text-end px-6 py-3 font-semibold">{t('consultant.netEarnings')}</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100">
                    {bookings.data.map((booking) => (
                      <tr key={booking.id} className="hover:bg-gray-50/50 transition-colors">
                        <td className="px-6 py-4">
                          <div className="font-medium text-gray-900">{formatDate(booking.start_at)}</div>
                          <div className="text-xs text-gray-400 mt-0.5">{formatTime(booking.start_at)}</div>
                        </td>
                        <td className="px-6 py-4">
                          <span className="font-medium text-gray-700">{booking.client?.name}</span>
                        </td>
                        <td className="px-6 py-4 text-gray-500">
                          {booking.duration_minutes} {t('consultant.min')}
                        </td>
                        <td className="px-6 py-4">
                          <span className={`inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold ${statusColors[booking.status] ?? 'bg-gray-100 text-gray-600'}`}>
                            {tBookings(`status.${booking.status}`)}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-end font-semibold text-gray-900">
                          {booking.total_amount} SAR
                        </td>
                        <td className="px-6 py-4 text-end text-red-500 font-medium">
                          −{booking.commission_amount} SAR
                        </td>
                        <td className="px-6 py-4 text-end font-bold text-emerald-600">
                          {booking.consultant_amount} SAR
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile Cards */}
              <div className="md:hidden divide-y divide-gray-100">
                {bookings.data.map((booking) => (
                  <div key={booking.id} className="px-5 py-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-medium text-gray-900 text-sm">{booking.client?.name}</span>
                      <span className={`inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold ${statusColors[booking.status] ?? 'bg-gray-100 text-gray-600'}`}>
                        {tBookings(`status.${booking.status}`)}
                      </span>
                    </div>
                    <div className="text-xs text-gray-400 mb-3">
                      {formatDate(booking.start_at)} · {formatTime(booking.start_at)} · {booking.duration_minutes} {t('consultant.min')}
                    </div>
                    <div className="flex items-center justify-between text-sm">
                      <div className="text-gray-500">
                        {booking.total_amount} <span className="text-red-400">− {booking.commission_amount}</span>
                      </div>
                      <div className="font-bold text-emerald-600">{booking.consultant_amount} SAR</div>
                    </div>
                  </div>
                ))}
              </div>

              {/* Pagination */}
              {bookings.last_page > 1 && (
                <div className="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                  <span className="text-xs text-gray-400">
                    {bookings.from}–{bookings.to} / {bookings.total}
                  </span>
                  <div className="flex gap-1.5">
                    {bookings.prev_page_url && (
                      <Link
                        href={bookings.prev_page_url}
                        className="px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-md hover:bg-gray-50 transition-colors"
                      >
                        ←
                      </Link>
                    )}
                    {bookings.next_page_url && (
                      <Link
                        href={bookings.next_page_url}
                        className="px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-md hover:bg-gray-50 transition-colors"
                      >
                        →
                      </Link>
                    )}
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      </DashboardLayout>
    </>
  );
}
