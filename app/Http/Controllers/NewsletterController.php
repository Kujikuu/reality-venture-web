<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeToNewsletterRequest;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function subscribe(SubscribeToNewsletterRequest $request): RedirectResponse
    {
        $subscriber = Subscriber::where('email', $request->validated('email'))->first();

        if ($subscriber) {
            if (! $subscriber->is_active) {
                $subscriber->update(['is_active' => true]);
            }
        } else {
            Subscriber::create(['email' => $request->validated('email')]);
        }

        return back()->with('newsletter_success', true);
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
