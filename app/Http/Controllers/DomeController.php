<?php

namespace App\Http\Controllers;

use App\Exceptions\DomeIntegrationException;
use App\Http\Requests\StoreDomeApplicationRequest;
use App\Http\Requests\StoreDomeSubscriptionRequest;
use App\Services\DomeApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DomeController extends Controller
{
    public function createSubscribe(): Response
    {
        return $this->form('subscribe');
    }

    public function createApply(): Response
    {
        return $this->form('apply');
    }

    public function subscribe(StoreDomeSubscriptionRequest $request, DomeApiService $dome): RedirectResponse
    {
        return $this->submit(fn (): array => $dome->subscribe([
            ...$request->validated(), 'preferred_locale' => app()->getLocale(), 'consent_version' => 'reality-venture-privacy-v1',
        ]), app()->isLocale('ar') ? 'تم اشتراكك في The Dome. راجع بريدك للتأكيد.' : 'You are subscribed to The Dome. Check your inbox for confirmation.');
    }

    public function apply(StoreDomeApplicationRequest $request, DomeApiService $dome): RedirectResponse
    {
        return $this->submit(fn (): array => $dome->apply([
            ...$request->validated(), 'preferred_locale' => app()->getLocale(), 'consent_version' => 'reality-venture-privacy-v1',
        ]), app()->isLocale('ar') ? 'تم استلام طلبك للانضمام إلى The Dome.' : 'Your The Dome application has been received.');
    }

    private function form(string $mode): Response
    {
        Inertia::share('seo', fn (): array => [
            'title' => $mode === 'apply' ? 'Apply to The Dome' : 'Subscribe to The Dome',
            'description' => 'Join The Dome community across Sniper, Reality Venture, and GRIT.',
            'canonical' => route("the-dome.{$mode}"), 'robots' => 'index, follow', 'jsonLd' => null,
        ]);

        return Inertia::render('TheDome', [
            'mode' => $mode, 'locale' => app()->getLocale(), 'submissionUuid' => (string) Str::uuid(),
            'requestedTier' => in_array(request()->query('requested_tier'), ['connect', 'engage'], true) ? request()->query('requested_tier') : null,
        ]);
    }

    /** @param callable(): array<string, mixed> $submission */
    private function submit(callable $submission, string $successMessage): RedirectResponse
    {
        try {
            $result = $submission();
        } catch (DomeIntegrationException $exception) {
            if ($exception->reason === 'validation' && $exception->errors !== []) {
                return back()->withErrors($exception->errors)->withInput();
            }

            return back()->withInput()->with('error', $this->errorMessage($exception->reason));
        }

        if ($result['duplicate'] ?? false) {
            return back()->with('notice', app()->isLocale('ar') ? 'استلمنا هذا الطلب مسبقاً، ولم يتم إنشاء سجل مكرر.' : 'We already received this submission. No duplicate record was created.');
        }

        return back()->with('success', $successMessage);
    }

    private function errorMessage(string $reason): string
    {
        $messages = app()->isLocale('ar') ? [
            'unauthorized' => 'إعدادات الاتصال بالخدمة غير مصرح بها. يرجى المحاولة لاحقاً.', 'rate_limited' => 'تم إرسال طلبات كثيرة. انتظر قليلاً ثم حاول مجدداً.',
            'conflict' => 'استُخدم مرجع الطلب نفسه لبيانات مختلفة. حدّث الصفحة وحاول مجدداً.', 'unavailable' => 'خدمة The Dome غير متاحة مؤقتاً. حاول بعد قليل.', 'failed' => 'تعذر إرسال الطلب. حاول مجدداً.',
        ] : [
            'unauthorized' => 'The service configuration is not authorized. Please try again later.', 'rate_limited' => 'Too many requests were received. Please wait and try again.',
            'conflict' => 'This reference was used with different information. Refresh and try again.', 'unavailable' => 'The Dome is temporarily unavailable. Please try again shortly.', 'failed' => 'We could not submit your request. Please try again.',
        ];

        return $messages[$reason] ?? $messages['failed'];
    }
}
