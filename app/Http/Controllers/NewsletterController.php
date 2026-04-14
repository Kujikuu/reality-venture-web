<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeToNewsletterRequest;
use App\Mail\WelcomeToClub;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(SubscribeToNewsletterRequest $request): RedirectResponse
    {
        $fullname = $request->validated('fullname');
        $email = $request->validated('email');
        $phone = $this->normalizePhone($request->validated('phone'));

        $subscriber = Subscriber::where('email', $email)
            ->orWhere('phone', $phone)
            ->first();

        if ($subscriber) {
            $subscriber->update([
                'fullname' => $fullname,
                'is_active' => true,
                'phone' => $phone,
                'position' => $request->validated('position'),
                'interests' => $request->validated('interests'),
                'city' => $request->validated('city'),
                'organization' => $request->validated('organization'),
            ]);
        } else {
            $subscriber = Subscriber::create([
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'is_active' => true,
                'position' => $request->validated('position'),
                'interests' => $request->validated('interests'),
                'city' => $request->validated('city'),
                'organization' => $request->validated('organization'),
            ]);
        }

        Mail::to($email)->send(new WelcomeToClub($subscriber));

        return back()->with('newsletter_success', true);
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/\s+/', '', $phone);
        $phone = ltrim($phone, '+');

        if (str_starts_with($phone, '966')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return '+966'.$phone;
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if ($subscriber) {
            $subscriber->update(['is_active' => false]);
        }

        return redirect('/')->with('unsubscribed', true);
    }
}
