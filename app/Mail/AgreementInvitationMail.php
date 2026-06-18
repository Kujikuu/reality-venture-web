<?php

namespace App\Mail;

use App\Models\Application;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgreementInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ?string $note = null,
        public bool $rvClubInvite = false
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.agreement_invitation', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.agreement-invitation',
            with: [
                'application' => $this->application,
                'note' => $this->note,
                'rvClubInvite' => $this->rvClubInvite,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
