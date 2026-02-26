<?php

namespace App\Mail;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Payout $payout) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payout Request Rejected - {$this->payout->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payouts.rejected',
            with: [
                'payout' => $this->payout,
                'consultantName' => $this->payout->consultantProfile->user->name,
                'reason' => $this->payout->admin_notes,
            ],
        );
    }
}
