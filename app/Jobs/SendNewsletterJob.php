<?php

namespace App\Jobs;

use App\Enums\NewsletterStatus;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Newsletter $newsletter) {}

    public function handle(): void
    {
        $this->newsletter->update(['status' => NewsletterStatus::Sending]);

        $sentCount = 0;

        Subscriber::query()
            ->active()
            ->chunkById(100, function ($subscribers) use (&$sentCount) {
                foreach ($subscribers as $subscriber) {
                    Mail::to($subscriber->email)->send(new NewsletterMail($this->newsletter, $subscriber));
                    $sentCount++;
                }
            });

        $this->newsletter->update([
            'status' => NewsletterStatus::Sent,
            'sent_at' => now(),
            'sent_count' => $sentCount,
        ]);
    }
}
