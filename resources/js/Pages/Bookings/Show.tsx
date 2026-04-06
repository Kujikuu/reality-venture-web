import { Link, useForm, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { Calendar, Clock, User, ExternalLink, Star, AlertTriangle } from 'lucide-react';
import { useState } from 'react';
import type { BookingItem } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';

interface Props {
  booking: BookingItem;
}

const statusColors: Record<string, string> = {
  awaiting_payment: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
  completed: 'bg-blue-100 text-blue-800',
  no_show: 'bg-gray-100 text-gray-800',
};

export default function BookingShow({ booking }: Props) {
  const { t } = useTranslation('bookings');
  const [showCancelConfirm, setShowCancelConfirm] = useState(false);
  const [showReviewForm, setShowReviewForm] = useState(false);

  const cancelForm = useForm({ reason: '' });
  const reviewForm = useForm({ rating: 5, comment: '' });

  const startDate = new Date(booking.start_at);
  const canCancel = ['awaiting_payment', 'confirmed'].includes(booking.status);
  const canReview = booking.status === 'completed' && !booking.has_review;

  const handleCancel = () => {
    cancelForm.post(`/bookings/${booking.id}/cancel`, {
      onSuccess: () => setShowCancelConfirm(false),
    });
  };

  const handleReview = (e: React.FormEvent) => {
    e.preventDefault();
    reviewForm.post(`/bookings/${booking.id}/review`, {
      onSuccess: () => setShowReviewForm(false),
    });
  };

  return (
    <>
      <SEO />
      <div className="min-h-screen bg-gray-50 py-12 px-6">
        <div className="max-w-2xl mx-auto">
          <motion.div
            className="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-xs"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <div className="p-8">
              <div className="flex items-center justify-between mb-8">
                <h1 className="text-xl font-bold text-gray-900">{t('show.title')}</h1>
                <span className={`px-3 py-1 rounded-full text-xs font-bold ${statusColors[booking.status] || 'bg-gray-100 text-gray-800'}`}>
                  {t(`status.${booking.status}`)}
                </span>
              </div>

              <div className="space-y-5">
                <div className="flex items-center gap-3">
                  <span className="text-sm text-gray-500 w-28">{t('show.reference')}</span>
                  <span className="text-sm font-mono font-semibold text-gray-900">{booking.reference}</span>
                </div>
                <div className="flex items-center gap-3">
                  <User className="w-4 h-4 text-gray-400" />
                  <span className="text-sm text-gray-500">{t('show.consultant')}</span>
                  <Link
                    href={`/consultants/${booking.consultant?.slug}`}
                    className="text-sm font-semibold text-primary hover:text-primary-800"
                  >
                    {booking.consultant?.name}
                  </Link>
                </div>
                <div className="flex items-center gap-3">
                  <Calendar className="w-4 h-4 text-gray-400" />
                  <span className="text-sm text-gray-500">{t('show.dateTime')}</span>
                  <span className="text-sm font-semibold text-gray-900">
                    {startDate.toLocaleDateString()} {startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                  </span>
                </div>
                <div className="flex items-center gap-3">
                  <Clock className="w-4 h-4 text-gray-400" />
                  <span className="text-sm text-gray-500">{t('show.duration')}</span>
                  <span className="text-sm font-semibold text-gray-900">{booking.duration_minutes} {t('show.minutes')}</span>
                </div>
                <div className="flex items-center gap-3 pt-3 border-t border-gray-100">
                  <span className="text-sm font-bold text-gray-900">{t('show.amount')}</span>
                  <span className="ms-auto text-lg font-bold text-secondary">{booking.total_amount} SAR</span>
                </div>
              </div>

              {/* Meeting Link */}
              {booking.meeting_url && booking.status === 'confirmed' && (
                <div className="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                  <a
                    href={booking.meeting_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-2 text-sm font-bold text-green-700 hover:text-green-800"
                  >
                    <ExternalLink className="w-4 h-4" /> {t('show.joinMeeting')}
                  </a>
                </div>
              )}

              {/* Actions */}
              <div className="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-3">
                {booking.status === 'awaiting_payment' && booking.calendly_event_uuid && (
                  <Link
                    href={`/bookings/${booking.calendly_event_uuid}/pay`}
                    className="px-6 h-10 flex items-center bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary-800 transition-colors"
                  >
                    {t('show.completePayment')}
                  </Link>
                )}

                {canCancel && (
                  <button
                    onClick={() => setShowCancelConfirm(true)}
                    className="px-6 h-10 flex items-center border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition-colors"
                  >
                    {t('show.cancelBooking')}
                  </button>
                )}

                {canReview && (
                  <button
                    onClick={() => setShowReviewForm(true)}
                    className="px-6 h-10 flex items-center border border-primary/20 text-primary text-sm font-bold rounded-lg hover:bg-primary/5 transition-colors"
                  >
                    {t('show.leaveReview')}
                  </button>
                )}
              </div>

              {/* Cancel Confirmation */}
              {showCancelConfirm && (
                <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                  <div className="flex items-start gap-3 mb-3">
                    <AlertTriangle className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                    <div>
                      <p className="text-sm font-medium text-red-800">{t('show.cancelConfirm')}</p>
                      <p className="text-xs text-red-600 mt-1">
                        {booking.is_refund_eligible ? t('show.refundNote') : t('show.noRefundNote')}
                      </p>
                    </div>
                  </div>
                  <div className="flex gap-2">
                    <button
                      onClick={handleCancel}
                      disabled={cancelForm.processing}
                      className="px-4 h-8 bg-red-600 text-white text-xs font-bold rounded-md hover:bg-red-700 disabled:opacity-50"
                    >
                      {t('show.confirmCancel')}
                    </button>
                    <button
                      onClick={() => setShowCancelConfirm(false)}
                      className="px-4 h-8 text-xs font-bold text-gray-600 hover:text-gray-900"
                    >
                      {t('show.keepBooking')}
                    </button>
                  </div>
                </div>
              )}

              {/* Review Form */}
              {showReviewForm && (
                <form onSubmit={handleReview} className="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-4">
                  <div>
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('show.rating')}</label>
                    <div className="flex items-center gap-1 mt-1">
                      {[1, 2, 3, 4, 5].map((star) => (
                        <button
                          key={star}
                          type="button"
                          onClick={() => reviewForm.setData('rating', star)}
                          className="p-0.5"
                        >
                          <Star className={`w-6 h-6 ${star <= reviewForm.data.rating ? 'text-secondary fill-secondary' : 'text-gray-300'}`} />
                        </button>
                      ))}
                    </div>
                  </div>
                  <div>
                    <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('show.comment')}</label>
                    <textarea
                      rows={3}
                      value={reviewForm.data.comment}
                      onChange={(e) => reviewForm.setData('comment', e.target.value)}
                      className="w-full p-3 mt-1 bg-white border border-gray-200 rounded-lg text-gray-900 text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none resize-none"
                    />
                  </div>
                  <div className="flex gap-2">
                    <button
                      type="submit"
                      disabled={reviewForm.processing}
                      className="px-4 h-8 bg-primary text-white text-xs font-bold rounded-md hover:bg-primary-800 disabled:opacity-50"
                    >
                      {t('show.submitReview')}
                    </button>
                    <button
                      type="button"
                      onClick={() => setShowReviewForm(false)}
                      className="px-4 h-8 text-xs font-bold text-gray-600 hover:text-gray-900"
                    >
                      {t('show.cancelReview')}
                    </button>
                  </div>
                </form>
              )}
            </div>
          </motion.div>
        </div>
      </div>
    </>
  );
}
