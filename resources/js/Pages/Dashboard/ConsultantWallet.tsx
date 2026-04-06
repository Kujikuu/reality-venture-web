import { useState } from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import {
  LayoutDashboard, Calendar, DollarSign, UserCircle, Wallet, Landmark,
  ArrowUpRight, ArrowDownRight, Clock, CheckCircle2, XCircle, Send, Download,
} from 'lucide-react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import type { BalanceSummary, BankDetails, PaginatedData, PageProps, PayoutItem } from '../../types/marketplace';
import { SEO } from '../../Components/SEO';

interface Props {
  balance: BalanceSummary;
  payouts: PaginatedData<PayoutItem>;
  bankDetails: BankDetails;
  hasPendingPayout: boolean;
  minimumPayout: number;
}

const payoutStatusColors: Record<string, string> = {
  requested: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-blue-100 text-blue-800',
  transferred: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
  cancelled: 'bg-gray-100 text-gray-800',
};

export default function ConsultantWallet({ balance, payouts, bankDetails, hasPendingPayout, minimumPayout }: Props) {
  const { t } = useTranslation('dashboard');
  const { t: tPayouts } = useTranslation('payouts');
  const { flash } = usePage<PageProps>().props;

  const [showBankForm, setShowBankForm] = useState(!bankDetails.bank_name);

  const sidebarLinks = [
    { href: '/consultant/dashboard', icon: LayoutDashboard, label: t('consultant.overview') },
    { href: '/consultant/bookings', icon: Calendar, label: t('consultant.bookings') },
    { href: '/consultant/earnings', icon: DollarSign, label: t('consultant.earnings') },
    { href: '/consultant/wallet', icon: Wallet, label: t('consultant.wallet') },
    { href: '/consultant/profile/edit', icon: UserCircle, label: t('consultant.profileEdit') },
  ];

  const bankForm = useForm({
    bank_name: bankDetails.bank_name ?? '',
    bank_account_holder_name: bankDetails.bank_account_holder_name ?? '',
    iban: bankDetails.iban ?? '',
  });

  const payoutForm = useForm({
    amount: balance.available > 0 ? balance.available : 0,
  });

  const handleBankSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    bankForm.post('/consultant/wallet/bank-details', {
      preserveScroll: true,
      onSuccess: () => setShowBankForm(false),
    });
  };

  const handlePayoutSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    payoutForm.post('/consultant/wallet/request-payout', {
      preserveScroll: true,
    });
  };

  const hasBankDetails = !!bankDetails.bank_name && !!bankDetails.iban;
  const canRequestPayout = hasBankDetails && !hasPendingPayout && balance.available >= minimumPayout;

  const maskIban = (iban: string) => {
    if (iban.length <= 8) return iban;
    return iban.slice(0, 4) + ' •••• •••• ' + iban.slice(-4);
  };

  const formatDate = (iso: string) => {
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
  };

  return (
    <>
      <SEO />
      <DashboardLayout links={sidebarLinks} title={t('consultant.walletTitle')}>

        {/* Flash Messages */}
        {flash.success && (
          <div className="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{tPayouts(flash.success, flash.success)}</div>
        )}
        {flash.error && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{tPayouts(flash.error, flash.error)}</div>
        )}

        {/* Balance Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div className="bg-white border border-emerald-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.availableBalance')}</span>
              <span className="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                <ArrowUpRight className="w-4 h-4 text-emerald-500" />
              </span>
            </div>
            <div className="text-2xl font-bold text-emerald-600">{Number(balance.available).toLocaleString()} <span className="text-sm font-medium text-emerald-400">SAR</span></div>
            <p className="text-[10px] text-gray-400 mt-1">{t('consultant.availableDesc')}</p>
          </div>

          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.pendingEarnings')}</span>
              <span className="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <Clock className="w-4 h-4 text-amber-500" />
              </span>
            </div>
            <div className="text-2xl font-bold text-amber-600">{Number(balance.pending).toLocaleString()} <span className="text-sm font-medium text-amber-400">SAR</span></div>
            <p className="text-[10px] text-gray-400 mt-1">{t('consultant.pendingDesc')}</p>
          </div>

          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.totalEarned')}</span>
              <span className="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                <DollarSign className="w-4 h-4 text-gray-400" />
              </span>
            </div>
            <div className="text-2xl font-bold text-gray-900">{Number(balance.total_earned).toLocaleString()} <span className="text-sm font-medium text-gray-400">SAR</span></div>
            <p className="text-[10px] text-gray-400 mt-1">{t('consultant.totalEarnedDesc')}</p>
          </div>

          <div className="bg-white border border-gray-200 rounded-xl p-5">
            <div className="flex items-center justify-between mb-3">
              <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.totalPaidOut')}</span>
              <span className="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <ArrowDownRight className="w-4 h-4 text-blue-500" />
              </span>
            </div>
            <div className="text-2xl font-bold text-blue-600">{Number(balance.total_paid_out).toLocaleString()} <span className="text-sm font-medium text-blue-400">SAR</span></div>
            <p className="text-[10px] text-gray-400 mt-1">{t('consultant.totalPaidOutDesc')}</p>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          {/* Bank Details Card */}
          <div className="bg-white border border-gray-200 rounded-xl p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="font-bold text-gray-900 flex items-center gap-2">
                <Landmark className="w-4 h-4" /> {t('consultant.bankDetails')}
              </h2>
              {hasBankDetails && !showBankForm && (
                <button
                  onClick={() => setShowBankForm(true)}
                  className="text-xs font-medium text-primary hover:text-primary-800 transition-colors"
                >
                  {t('consultant.updateBankDetails')}
                </button>
              )}
            </div>

            {!hasBankDetails && !showBankForm && (
              <div className="text-center py-8">
                <Landmark className="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <p className="text-sm text-gray-400 mb-4">{t('consultant.noBankDetails')}</p>
                <button
                  onClick={() => setShowBankForm(true)}
                  className="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary-800 transition-colors"
                >
                  {t('consultant.addBankDetails')}
                </button>
              </div>
            )}

            {hasBankDetails && !showBankForm && (
              <div className="space-y-3">
                <div>
                  <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.bankName')}</span>
                  <p className="text-sm font-medium text-gray-900 mt-0.5">{bankDetails.bank_name}</p>
                </div>
                <div>
                  <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.accountHolderName')}</span>
                  <p className="text-sm font-medium text-gray-900 mt-0.5">{bankDetails.bank_account_holder_name}</p>
                </div>
                <div>
                  <span className="text-xs font-bold uppercase tracking-wide text-gray-400">{t('consultant.iban')}</span>
                  <p className="text-sm font-medium text-gray-900 mt-0.5 font-mono">{maskIban(bankDetails.iban ?? '')}</p>
                </div>
              </div>
            )}

            {showBankForm && (
              <form onSubmit={handleBankSubmit} className="space-y-4">
                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.bankName')} *</label>
                  <input
                    type="text"
                    value={bankForm.data.bank_name}
                    onChange={(e) => bankForm.setData('bank_name', e.target.value)}
                    className="w-full h-11 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                    placeholder="e.g. Al Rajhi Bank"
                  />
                  {bankForm.errors.bank_name && <p className="text-red-500 text-xs">{tPayouts(bankForm.errors.bank_name, bankForm.errors.bank_name)}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.accountHolderName')} *</label>
                  <input
                    type="text"
                    value={bankForm.data.bank_account_holder_name}
                    onChange={(e) => bankForm.setData('bank_account_holder_name', e.target.value)}
                    className="w-full h-11 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                  />
                  {bankForm.errors.bank_account_holder_name && <p className="text-red-500 text-xs">{tPayouts(bankForm.errors.bank_account_holder_name, bankForm.errors.bank_account_holder_name)}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.iban')} *</label>
                  <input
                    type="text"
                    value={bankForm.data.iban}
                    onChange={(e) => bankForm.setData('iban', e.target.value.toUpperCase())}
                    className="w-full h-11 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                    placeholder="SA0000000000000000000000"
                    maxLength={24}
                  />
                  {bankForm.errors.iban && <p className="text-red-500 text-xs">{tPayouts(bankForm.errors.iban, bankForm.errors.iban)}</p>}
                </div>

                <div className="flex gap-2">
                  <button
                    type="submit"
                    disabled={bankForm.processing}
                    className="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
                  >
                    {t('consultant.saveBankDetails')}
                  </button>
                  {hasBankDetails && (
                    <button
                      type="button"
                      onClick={() => setShowBankForm(false)}
                      className="px-5 py-2.5 text-sm font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                      {t('consultant.cancelPayoutBtn')}
                    </button>
                  )}
                </div>
              </form>
            )}
          </div>

          {/* Request Payout Card */}
          <div className="bg-white border border-gray-200 rounded-xl p-6">
            <h2 className="font-bold text-gray-900 flex items-center gap-2 mb-4">
              <Send className="w-4 h-4" /> {t('consultant.requestPayout')}
            </h2>

            {!hasBankDetails ? (
              <div className="text-center py-8">
                <Landmark className="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <p className="text-sm text-gray-400">{t('consultant.noBankDetails')}</p>
              </div>
            ) : hasPendingPayout ? (
              <div className="text-center py-8">
                <Clock className="w-10 h-10 text-amber-300 mx-auto mb-3" />
                <p className="text-sm text-amber-600 font-medium">{t('consultant.pendingPayoutExists')}</p>
              </div>
            ) : (
              <form onSubmit={handlePayoutSubmit} className="space-y-4">
                <div className="space-y-1.5">
                  <label className="text-xs font-bold uppercase tracking-wide text-gray-500">{t('consultant.payoutAmount')}</label>
                  <input
                    type="number"
                    min={minimumPayout}
                    max={balance.available}
                    step="0.01"
                    value={payoutForm.data.amount}
                    onChange={(e) => payoutForm.setData('amount', parseFloat(e.target.value) || 0)}
                    className="w-full h-11 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all"
                  />
                  {payoutForm.errors.amount && <p className="text-red-500 text-xs">{tPayouts(payoutForm.errors.amount, payoutForm.errors.amount)}</p>}
                  <p className="text-[10px] text-gray-400">{t('consultant.minimumPayout', { amount: minimumPayout })}</p>
                </div>

                {balance.total_in_process > 0 && (
                  <div className="p-3 bg-amber-50 border border-amber-100 rounded-lg text-xs text-amber-700">
                    {t('consultant.totalInProcess')}: {Number(balance.total_in_process).toLocaleString()} SAR
                  </div>
                )}

                <button
                  type="submit"
                  disabled={!canRequestPayout || payoutForm.processing}
                  className="w-full py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary-800 transition-colors disabled:opacity-50"
                >
                  {t('consultant.requestPayoutBtn')}
                </button>
              </form>
            )}
          </div>
        </div>

        {/* Payout History */}
        <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h2 className="font-bold text-gray-900">{t('consultant.payoutHistory')}</h2>
          </div>

          {payouts.data.length === 0 ? (
            <div className="text-center py-16">
              <Wallet className="w-10 h-10 text-gray-300 mx-auto mb-3" />
              <p className="text-sm text-gray-400">{t('consultant.noPayouts')}</p>
            </div>
          ) : (
            <>
              {/* Desktop Table */}
              <div className="hidden md:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.payoutReference')}</th>
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.payoutDate')}</th>
                      <th className="text-end px-6 py-3 font-semibold">{t('consultant.payoutAmount')}</th>
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.status')}</th>
                      <th className="text-start px-6 py-3 font-semibold">{t('consultant.transferRef')}</th>
                      <th className="px-6 py-3"></th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100">
                    {payouts.data.map((payout) => (
                      <tr key={payout.id} className="hover:bg-gray-50/50 transition-colors">
                        <td className="px-6 py-4 font-mono text-xs font-medium text-gray-900">{payout.reference}</td>
                        <td className="px-6 py-4 text-gray-500">{formatDate(payout.created_at)}</td>
                        <td className="px-6 py-4 text-end font-bold text-gray-900">{Number(payout.amount).toLocaleString()} SAR</td>
                        <td className="px-6 py-4">
                          <span className={`inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold ${payoutStatusColors[payout.status]}`}>
                            {tPayouts(`status.${payout.status}`)}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-gray-500 font-mono text-xs">{payout.transfer_reference ?? '—'}</td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2">
                            {payout.status === 'requested' && (
                              <Link
                                href={`/consultant/wallet/payouts/${payout.id}/cancel`}
                                method="post"
                                as="button"
                                className="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md hover:bg-red-100 transition-colors"
                              >
                                {t('consultant.cancelPayoutBtn')}
                              </Link>
                            )}
                            {payout.status === 'transferred' && payout.has_receipt && (
                              <a
                                href={`/consultant/wallet/payouts/${payout.id}/receipt`}
                                className="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-md hover:bg-emerald-100 transition-colors"
                              >
                                <Download className="w-3 h-3" /> {t('consultant.downloadReceipt')}
                              </a>
                            )}
                            {payout.status === 'rejected' && payout.admin_notes && (
                              <span className="text-xs text-red-500" title={payout.admin_notes}>
                                <XCircle className="w-4 h-4 inline" /> {t('consultant.rejectionReason')}
                              </span>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile Cards */}
              <div className="md:hidden divide-y divide-gray-100">
                {payouts.data.map((payout) => (
                  <div key={payout.id} className="px-5 py-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-mono text-xs font-medium text-gray-900">{payout.reference}</span>
                      <span className={`inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold ${payoutStatusColors[payout.status]}`}>
                        {tPayouts(`status.${payout.status}`)}
                      </span>
                    </div>
                    <div className="text-xs text-gray-400 mb-2">{formatDate(payout.created_at)}</div>
                    <div className="flex items-center justify-between">
                      <div className="font-bold text-gray-900">{Number(payout.amount).toLocaleString()} SAR</div>
                      {payout.status === 'requested' && (
                        <Link
                          href={`/consultant/wallet/payouts/${payout.id}/cancel`}
                          method="post"
                          as="button"
                          className="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md hover:bg-red-100 transition-colors"
                        >
                          {t('consultant.cancelPayoutBtn')}
                        </Link>
                      )}
                    </div>
                    {payout.transfer_reference && (
                      <div className="text-xs text-gray-400 mt-1 font-mono">{t('consultant.transferRef')}: {payout.transfer_reference}</div>
                    )}
                    {payout.status === 'transferred' && payout.has_receipt && (
                      <a
                        href={`/consultant/wallet/payouts/${payout.id}/receipt`}
                        className="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-md hover:bg-emerald-100 transition-colors"
                      >
                        <Download className="w-3 h-3" /> {t('consultant.downloadReceipt')}
                      </a>
                    )}
                    {payout.status === 'rejected' && payout.admin_notes && (
                      <div className="text-xs text-red-500 mt-1">{t('consultant.rejectionReason')}: {payout.admin_notes}</div>
                    )}
                  </div>
                ))}
              </div>

              {/* Pagination */}
              {payouts.last_page > 1 && (
                <div className="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                  <span className="text-xs text-gray-400">{payouts.from}–{payouts.to} / {payouts.total}</span>
                  <div className="flex gap-1.5">
                    {payouts.prev_page_url && (
                      <Link
                        href={payouts.prev_page_url}
                        className="px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-md hover:bg-gray-50 transition-colors"
                      >
                        ←
                      </Link>
                    )}
                    {payouts.next_page_url && (
                      <Link
                        href={payouts.next_page_url}
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
