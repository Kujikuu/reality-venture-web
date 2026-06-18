<?php

namespace App\Mail;

use App\Models\ConsultantProfile;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultantRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ConsultantProfile $profile) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.consultant_rejected'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.consultants.rejected',
            with: [
                'profile' => $this->profile,
                'name' => $this->profile->user->name,
                'reason' => $this->profile->rejection_reason,
            ],
        );
    }
}
