import { Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { LayoutDashboard, Calendar, Settings, ExternalLink, Star } from 'lucide-react';
import { useState } from 'react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { BookingItem, PageProps } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';

interface Props {
  upcoming: BookingItem[];
  pendingPayment: BookingItem[];
  past: BookingItem[];
  stats: {
    total_bookings: number;
    total_spent: string;
  };
}

const statusColors: Record<string, string> = {
  awaiting_payment: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
  completed: 'bg-blue-100 text-blue-800',
  no_show: 'bg-gray-100 text-gray-800',
};

export default function ClientDashboard({ upcoming, pendingPayment, past, stats }: Props) {
  const { t } = useTranslation('dashboard');
  const { t: tBookings } = useTranslation('bookings');
  const { auth } = usePage<PageProps>().props;
  const firstName = auth.user?.name?.split(' ')[0] ?? '';
  const [activeTab, setActiveTab] = useState<'upcoming' | 'pending' | 'past'>('upcoming');

  const sidebarLinks = [
    { href: '/dashboard', icon: LayoutDashboard, label: t('client.bookings') },
    { href: '/dashboard/settings', icon: Settings, label: t('client.settings') },
  ];

  const tabs = [
    { key: 'upcoming' as const, label: t('client.upcoming'), items: upcoming },
    { key: 'pending' as const, label: t('client.pendingPayment'), items: pendingPayment },
    { key: 'past' as const, label: t('client.past'), items: past },
  ];

  return (
    <>
      <SEO />
      <DashboardLayout links={sidebarLinks} title={t('client.title')}>
        {/* Welcome */}
        <div className="mb-6">
          <h2 className="text-xl font-bold text-gray-900">{t('client.welcome', { name: firstName })}</h2>
          <p className="text-sm text-gray-500 mt-1">{t('client.welcomeSub')}</p>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">{t('client.totalBookings')}</div>
            <div className="text-2xl font-bold text-gray-900">{stats.total_bookings}</div>
          </div>
          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">{t('client.totalSpent')}</div>
            <div className="text-2xl font-bold text-secondary">{stats.total_spent} SAR</div>
          </div>
        </div>

        {/* Tabs */}
        <div className="flex gap-1 bg-gray-100 rounded-lg p-1 mb-6 w-fit">
          {tabs.map((tab) => (
            <button
              key={tab.key}
              onClick={() => setActiveTab(tab.key)}
              className={`px-4 py-2 text-sm font-medium rounded-md transition-colors ${
                activeTab === tab.key
                  ? 'bg-white text-gray-900 shadow-xs'
                  : 'text-gray-500 hover:text-gray-700'
              }`}
            >
              {tab.label} ({tab.items.length})
            </button>
          ))}
        </div>

        {/* Bookings List */}
        <div className="space-y-3">
          {tabs.find(t => t.key === activeTab)?.items.length === 0 ? (
            <div className="text-center py-16 bg-white border border-gray-200 rounded-xl">
              <Calendar className="w-10 h-10 text-gray-300 mx-auto mb-3" />
              <p className="text-sm text-gray-400">
                {activeTab === 'upcoming' ? t('client.noUpcoming') : activeTab === 'pending' ? t('client.noPending') : t('client.noPast')}
              </p>
            </div>
          ) : (
            tabs.find(t => t.key === activeTab)?.items.map((booking) => (
              <div key={booking.id} className="bg-white border border-gray-200 rounded-xl p-5 hover:border-primary/20 transition-colors">
                <div className="flex items-start justify-between">
                  <div className="flex items-start gap-4">
                    <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                      {booking.consultant?.name?.charAt(0)}
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{booking.consultant?.name}</h3>
                      <p className="text-sm text-gray-500 mt-0.5">
                        {new Date(booking.start_at).toLocaleDateString()} at{' '}
                        {new Date(booking.start_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        {' '}&middot;{' '}{booking.duration_minutes} min
                      </p>
                      <span className={`inline-block mt-2 px-2.5 py-0.5 rounded-full text-xs font-bold ${statusColors[booking.status]}`}>
                        {tBookings(`status.${booking.status}`)}
                      </span>
                    </div>
                  </div>
                  <div className="text-end">
                    <span className="text-sm font-bold text-gray-900">{booking.total_amount} SAR</span>
                  </div>
                </div>

                <div className="flex flex-wrap gap-2 mt-4 pt-3 border-t border-gray-100">
                  {booking.status === 'awaiting_payment' && booking.calendly_event_uuid && (
                    <Link
                      href={`/bookings/${booking.calendly_event_uuid}/pay`}
                      className="px-3 py-1.5 bg-primary text-white text-xs font-bold rounded-md hover:bg-primary-800 transition-colors"
                    >
                      {t('client.completePayment')}
                    </Link>
                  )}
                  {booking.meeting_url && booking.status === 'confirmed' && (
                    <a
                      href={booking.meeting_url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-bold rounded-md hover:bg-green-100 transition-colors"
                    >
                      <ExternalLink className="w-3 h-3" /> {t('client.joinMeeting')}
                    </a>
                  )}
                  <Link
                    href={`/bookings/${booking.reference}`}
                    className="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-bold rounded-md hover:bg-gray-200 transition-colors"
                  >
                    {t('client.viewDetails')}
                  </Link>
                </div>
              </div>
            ))
          )}
        </div>
      </DashboardLayout>
    </>
  );
}
