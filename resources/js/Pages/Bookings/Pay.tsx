import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { Shield, Calendar, Clock, User, Loader2 } from 'lucide-react';
import { loadStripe } from '@stripe/stripe-js';
import { Elements, PaymentElement, useStripe, useElements } from '@stripe/react-stripe-js';
import { useState, useEffect } from 'react';
import type { BookingItem } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';
import { SarIcon } from '../../Components/ui/SarIcon';

interface Props {
  booking: BookingItem | null;
  clientSecret: string | null;
  stripeKey: string;
  pending?: boolean;
}

function PaymentForm({ booking }: { booking: BookingItem }) {
  const { t } = useTranslation('bookings');
  const stripe = useStripe();
  const elements = useElements();
  const [processing, setProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!stripe || !elements) return;

    setProcessing(true);
    setError(null);

    const result = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: `${window.location.origin}/bookings/${booking.reference}`,
      },
    });

    if (result.error) {
      setError(result.error.message ?? 'Payment failed');
      setProcessing(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <PaymentElement />
      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{error}</div>
      )}
      <button
        type="submit"
        disabled={!stripe || processing}
        className="w-full h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
      >
        {processing ? t('pay.processing') : <><SarIcon /> {booking.total_amount} — {t('pay.payNow')}</>}
      </button>
      <div className="flex items-center justify-center gap-2 text-xs text-gray-400">
        <Shield className="w-3.5 h-3.5" /> {t('pay.secure')}
      </div>
    </form>
  );
}

export default function BookingPay({ booking, clientSecret, stripeKey, pending }: Props) {
  const { t } = useTranslation('bookings');
  const stripePromise = stripeKey ? loadStripe(stripeKey) : null;

  // Poll every 3s while waiting for the webhook to create the booking
  useEffect(() => {
    if (!pending) return;
    const interval = setInterval(() => {
      router.reload({ only: ['booking', 'clientSecret', 'pending'] });
    }, 3000);
    return () => clearInterval(interval);
  }, [pending]);

  if (pending || !booking) {
    return (
      <>
        <SEO />
        <div className="min-h-screen bg-gray-50 flex items-center justify-center px-6">
          <motion.div
            className="bg-white border border-gray-200 rounded-2xl p-12 text-center max-w-md w-full shadow-xs"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <Loader2 className="w-10 h-10 text-primary mx-auto mb-4 animate-spin" />
            <h2 className="text-lg font-bold text-gray-900 mb-2">{t('pay.pendingTitle')}</h2>
            <p className="text-sm text-gray-500">{t('pay.pendingDesc')}</p>
          </motion.div>
        </div>
      </>
    );
  }

  const startDate = new Date(booking.start_at);

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
            {/* Summary */}
            <div className="p-8 border-b border-gray-100 bg-gray-50/50">
              <h1 className="text-xl font-bold text-gray-900 mb-6">{t('pay.summary')}</h1>
              <div className="space-y-4">
                <div className="flex items-center gap-3">
                  <User className="w-4 h-4 text-gray-400" />
                  <span className="text-sm text-gray-500">{t('pay.consultant')}</span>
                  <span className="ms-auto text-sm font-semibold text-gray-900">{booking.consultant?.name}</span>
                </div>
                <div className="flex items-center gap-3">
                  <Calendar className="w-4 h-4 text-gray-400" />
                  <span className="text-sm text-gray-500">{t('pay.dateTime')}</span>
                  <span className="ms-auto text-sm font-semibold text-gray-900">
                    {startDate.toLocaleDateString()} {startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                  </span>
                </div>
                <div className="flex items-center gap-3">
                  <Clock className="w-4 h-4 text-gray-400" />
                  <span className="text-sm text-gray-500">{t('pay.duration')}</span>
                  <span className="ms-auto text-sm font-semibold text-gray-900">{booking.duration_minutes} {t('pay.minutes')}</span>
                </div>
                <div className="flex items-center gap-3 pt-3 border-t border-gray-200">
                  <span className="text-sm font-bold text-gray-900">{t('pay.amount')}</span>
                  <span className="ms-auto text-lg font-bold text-secondary"><SarIcon /> {booking.total_amount}</span>
                </div>
              </div>
            </div>

            {/* Stripe Form */}
            <div className="p-8">
              {clientSecret && (
                <Elements stripe={stripePromise} options={{ clientSecret }}>
                  <PaymentForm booking={booking} />
                </Elements>
              )}
            </div>
          </motion.div>
        </div>
      </div>
    </>
  );
}
