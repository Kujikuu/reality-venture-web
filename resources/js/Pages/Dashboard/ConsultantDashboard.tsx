import { Head, Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LayoutDashboard, Calendar, DollarSign, UserCircle, Star, TrendingUp, Wallet } from 'lucide-react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { BookingItem, PageProps } from '../../types/marketplace';

interface Props {
  stats: {
    upcoming_count: number;
    total_net_earnings: string;
    average_rating: string;
    total_bookings: number;
    available_balance: number;
  };
  recentBookings: BookingItem[];
}

const statusColors: Record<string, string> = {
  awaiting_payment: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
  completed: 'bg-blue-100 text-blue-800',
  no_show: 'bg-gray-100 text-gray-800',
};

export default function ConsultantDashboard({ stats, recentBookings }: Props) {
  const { t } = useTranslation('dashboard');
  const { t: tBookings } = useTranslation('bookings');
  const { auth } = usePage<PageProps>().props;
  const firstName = auth.user?.name?.split(' ')[0] ?? '';

  const sidebarLinks = [
    { href: '/consultant/dashboard', icon: LayoutDashboard, label: t('consultant.overview') },
    { href: '/consultant/bookings', icon: Calendar, label: t('consultant.bookings') },
    { href: '/consultant/earnings', icon: DollarSign, label: t('consultant.earnings') },
    { href: '/consultant/wallet', icon: Wallet, label: t('consultant.wallet') },
    { href: '/consultant/profile/edit', icon: UserCircle, label: t('consultant.profileEdit') },
  ];

  return (
    <>
      <Head title={t('consultant.title')} />
      <DashboardLayout links={sidebarLinks} title={t('consultant.title')}>
        {/* Welcome */}
        <div className="mb-6">
          <h2 className="text-xl font-bold text-gray-900">{t('consultant.welcome', { name: firstName })}</h2>
          <p className="text-sm text-gray-500 mt-1">{t('consultant.welcomeSub')}</p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">
              <Calendar className="w-4 h-4" /> {t('consultant.upcomingCount')}
            </div>
            <div className="text-2xl font-bold text-gray-900">{stats.upcoming_count}</div>
          </div>
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">
              <DollarSign className="w-4 h-4" /> {t('consultant.totalEarnings')}
            </div>
            <div className="text-2xl font-bold text-secondary">{stats.total_net_earnings} SAR</div>
          </div>
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">
              <Star className="w-4 h-4" /> {t('consultant.averageRating')}
            </div>
            <div className="text-2xl font-bold text-gray-900 flex items-center gap-1">
              {stats.average_rating}
              <Star className="w-5 h-5 text-secondary fill-secondary" />
            </div>
          </div>
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">
              <TrendingUp className="w-4 h-4" /> {t('consultant.totalBookings')}
            </div>
            <div className="text-2xl font-bold text-gray-900">{stats.total_bookings}</div>
          </div>
          <Link href="/consultant/wallet" className="bg-white border border-emerald-200 rounded-xl p-5 hover:bg-emerald-50/50 transition-colors">
            <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">
              <Wallet className="w-4 h-4" /> {t('consultant.availableForPayout')}
            </div>
            <div className="text-2xl font-bold text-emerald-600">{Number(stats.available_balance).toLocaleString()} SAR</div>
          </Link>
        </div>

        {/* Recent Bookings */}
        <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h2 className="font-bold text-gray-900">{t('consultant.recentBookings')}</h2>
          </div>

          {recentBookings.length === 0 ? (
            <div className="text-center py-16">
              <Calendar className="w-10 h-10 text-gray-300 mx-auto mb-3" />
              <p className="text-sm text-gray-400">{t('consultant.noBookings')}</p>
            </div>
          ) : (
            <div className="divide-y divide-gray-100">
              {recentBookings.map((booking) => (
                <div key={booking.id} className="px-6 py-4 hover:bg-gray-50 transition-colors">
                  <div className="flex items-center justify-between">
                    <div>
                      <div className="flex items-center gap-3">
                        <span className="font-semibold text-sm text-gray-900">{booking.client?.name}</span>
                        <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${statusColors[booking.status]}`}>
                          {tBookings(`status.${booking.status}`)}
                        </span>
                      </div>
                      <p className="text-xs text-gray-500 mt-1">
                        {new Date(booking.start_at).toLocaleDateString()} at{' '}
                        {new Date(booking.start_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        {' '}&middot;{' '}{booking.duration_minutes} min
                      </p>
                    </div>
                    <div className="text-end">
                      <div className="text-sm font-bold text-gray-900">{booking.total_amount} SAR</div>
                      <div className="text-xs text-gray-400">
                        {t('consultant.netEarnings')}: <span className="text-green-600 font-semibold">{booking.consultant_amount} SAR</span>
                      </div>
                      <div className="text-xs text-gray-300">
                        -{booking.commission_amount} SAR {t('consultant.platformFee').toLowerCase()}
                      </div>
                    </div>
                  </div>

                  {booking.status === 'confirmed' && (
                    <div className="flex gap-2 mt-3">
                      <Link
                        href={`/consultant/bookings/${booking.id}/complete`}
                        method="post"
                        as="button"
                        className="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md hover:bg-green-100 transition-colors"
                      >
                        {t('consultant.complete')}
                      </Link>
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>
      </DashboardLayout>
    </>
  );
}
