<?php

use App\Http\Controllers\AdBannerController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendlyWebhookController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\ClientSettingsController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\ConsultantDashboardController;
use App\Http\Controllers\ConsultantOnboardingController;
use App\Http\Controllers\ConsultantPayoutController;
use App\Http\Controllers\ConsultantProfileController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// Static pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/application-form', fn () => redirect('/startuphub', 301));
Route::get('/startuphub', [PageController::class, 'applicationForm'])->name('application.form');
Route::get('/startup-application', [PageController::class, 'startupApplicationForm'])->name('startup-application.form');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms.service');

// Form submission
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
Route::post('/startup-applications', [ApplicationController::class, 'storeStartup'])->name('startup-applications.store');
Route::get('/applications/lookup/{uid}', [ApplicationController::class, 'lookup'])->name('applications.lookup');

// Agreement
Route::get('/agreement/{uid}', [AgreementController::class, 'show'])->name('agreement.show');
Route::post('/agreement/{uid}', [AgreementController::class, 'approve'])->name('agreement.approve');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Banner click tracking
Route::post('/banners/{adBanner}/click', [AdBannerController::class, 'trackClick'])->name('banners.click');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

// Desks / Workspaces
Route::get('/grit', fn () => inertia('Desks/Index'))->name('desks.index');
Route::get('/grit/bookings', fn () => inertia('Desks/Bookings'))->name('desks.bookings');
Route::get('/grit/{slug}', fn (string $slug) => inertia('Desks/Show', ['workspaceSlug' => $slug]))->name('desks.show');

// ─── Auth (Guest Only) ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/forgot-password', [PasswordController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ─── Public Marketplace ─────────────────────────────────────────────
Route::get('/consultants', [ConsultantController::class, 'index'])->name('consultants.index');
Route::get('/consultants/{consultantProfile:slug}', [ConsultantController::class, 'show'])->name('consultants.show');

// ─── Client Routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'role.client'])->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');
    Route::get('/dashboard/settings', [ClientSettingsController::class, 'edit'])->name('client.settings');
    Route::post('/dashboard/settings', [ClientSettingsController::class, 'update'])->name('client.settings.update');

    Route::get('/bookings/{calendlyEventUuid}/pay', [BookingController::class, 'showPayment'])->name('bookings.pay');
    Route::post('/bookings/{calendlyEventUuid}/pay', [BookingController::class, 'initiate'])->name('bookings.initiate');

    Route::get('/bookings/{booking:reference}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('bookings.review');
});

// ─── Consultant Routes ──────────────────────────────────────────────
Route::middleware(['auth', 'role.consultant'])->group(function () {
    Route::get('/consultant/onboarding', [ConsultantOnboardingController::class, 'show'])->name('consultant.onboarding');
    Route::post('/consultant/onboarding', [ConsultantOnboardingController::class, 'store'])->name('consultant.onboarding.store');
    Route::post('/consultant/onboarding/reapply', [ConsultantOnboardingController::class, 'reapply'])->name('consultant.onboarding.reapply');

    Route::middleware('consultant.approved')->group(function () {
        Route::get('/consultant/dashboard', [ConsultantDashboardController::class, 'index'])->name('consultant.dashboard');
        Route::get('/consultant/bookings', [ConsultantDashboardController::class, 'bookings'])->name('consultant.bookings');
        Route::get('/consultant/earnings', [ConsultantDashboardController::class, 'earnings'])->name('consultant.earnings');
        Route::post('/consultant/bookings/{booking}/complete', [ConsultantDashboardController::class, 'completeBooking'])->name('consultant.bookings.complete');
        Route::get('/consultant/profile/edit', [ConsultantProfileController::class, 'edit'])->name('consultant.profile.edit');
        Route::post('/consultant/profile', [ConsultantProfileController::class, 'update'])->name('consultant.profile.update');

        Route::get('/consultant/wallet', [ConsultantPayoutController::class, 'index'])->name('consultant.wallet');
        Route::post('/consultant/wallet/bank-details', [ConsultantPayoutController::class, 'updateBankDetails'])->name('consultant.wallet.bank-details');
        Route::post('/consultant/wallet/request-payout', [ConsultantPayoutController::class, 'requestPayout'])->name('consultant.wallet.request-payout');
        Route::post('/consultant/wallet/payouts/{payout}/cancel', [ConsultantPayoutController::class, 'cancelPayout'])->name('consultant.wallet.cancel-payout');
        Route::get('/consultant/wallet/payouts/{payout}/receipt', [ConsultantPayoutController::class, 'downloadReceipt'])->name('consultant.wallet.download-receipt');
    });
});

// ─── Webhooks (No CSRF, No Auth) ────────────────────────────────────
Route::post('/webhooks/calendly', [CalendlyWebhookController::class, 'handle'])->name('webhooks.calendly');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// 404 fallback
Route::fallback([PageController::class, 'notFound']);
